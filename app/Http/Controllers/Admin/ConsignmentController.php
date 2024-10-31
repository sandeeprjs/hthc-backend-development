<?php

namespace App\Http\Controllers\Admin;

use App\Branch;
use App\Consignment;
use App\Franchisee;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function foo\func;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsignmentsExport;

class ConsignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'office_type' => 'nullable',
            'office_id' => 'nullable',
//            'consg_number' => 'nullable'
        ]);

        $startDate = null;
        $endDate = null;
//        $consgNumber = $request->input('consg_number'); //consg no. filter is not required as per comment from Sudhilal.
        $officeType = $request->input('office_type');
        $officeId = $request->input('office_id');

        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->subDay()->format('Y-m-d');
        }

        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->addDay()->format('Y-m-d');
        }

        $consignments = Consignment::select(DB::raw('batch_id, MIN(consg_number) as minConsgNum, MAX(consg_number) as maxConsgNum, office_type, office_id, count(*) as count, created_at'))
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($officeType && $officeId, function ($query) use ($officeId, $officeType) {
                return $query->where('office_type', '=', $officeType)->where('office_id', '=', $officeId);
            }) 
            ->groupBy('batch_id')->latest()->paginate(10);
       
        return view('consignments.index', compact('consignments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('consignments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print(Request $request)
    {
        $this->validate($request, [
            'office_type' => 'required',
            'office_id' => 'required',
            'quantity' => 'required|numeric'
        ]);
        $officeType = $request->input('office_type');
        $officeId = $request->input('office_id');
        $quantity = $request->input('quantity');

        $barCodeImages = AppHelper::generateBarcode($officeType, $officeId, $quantity);
//        SELECT c1.batch_id, COUNT(*), c1.office_id, c1.office_type, MIN(c1.consg_number) as minConsgNum, MAX(c2.consg_number) as maxConsgNum FROM consignments c1 LEFT JOIN (SELECT id, RIGHT(consg_number, 4) as consg_number FROM consignments GROUP BY batch_id, id) c2 ON (c1.id = c2.id) GROUP BY c1.batch_id, c1.office_type, c1.office_id
        return view('consignments.print', compact('barCodeImages'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /*
     * Reprint bar-codes based on batch_id
     *
     */
    public function reprint($batchId) {
        $consignments = Consignment::where('batch_id', '=', $batchId)->get();

        return view('consignments.reprint', compact('consignments'));
    }

    public function searchOfficeId(Request $request) {
        $this->validate($request, [
            'term' => 'required'
        ]);

        $term = $request->input('term');

        $branches = Branch::select(['id', 'code as text'])
            ->where('code', 'LIKE', "%$term%");

        $franchisees = Franchisee::select(['id', 'code as text'])
            ->where('code', 'LIKE', "%$term%")
            ->union($branches)
            ->get()->makeHidden('branch');

//        $office = Branch::where('code', 'LIKE', "%$term%")
//            ->orWhereHas('franchisees', function ($q) use ($term) {
//                return $q->where('code', 'LIKE', "%$term%");
//            })->get();

        return response()->json([
            'data' => $franchisees
        ]);
    }

    /**
     * To export consignment number 
     */
    public function consignmentExport($batchId){
        return Excel::download(new ConsignmentsExport($batchId), 'consignments.xlsx');
        

    }

    public function getUsedConsignmentCount($office_type,$office_id,$batch_id){

        $users = DB::table('consignments')->select('count(*) as count')
                ->leftJoin('bookings', 'consignments.consg_number', '=', 'bookings.consg_number')
                ->get();
        return response()->json([
            'count' => $users
        ]);
    }
}
