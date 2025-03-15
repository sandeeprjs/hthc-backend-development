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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsignmentsExport;

class ConsignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role']);
    }

    /**
     * Display a listing of consignments.
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'office_type' => 'nullable',
            'office_id' => 'nullable',
        ]);

        $startDate = $request->input('start_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay()
            : null;
        $endDate = $request->input('end_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay()
            : null;

        $consignments = Consignment::with(['office']) // Add eager loading here
        ->select(DB::raw('batch_id, MIN(consg_number) as minConsgNum, MAX(consg_number) as maxConsgNum, office_type, office_id, COUNT(*) as count, created_at'))
            ->when($startDate && $endDate, fn($query) => $query->whereBetween('created_at', [$startDate, $endDate]))
            ->when($request->input('office_type'), fn($query) => $query->where('office_type', $request->input('office_type')))
            ->when($request->input('office_id'), fn($query) => $query->where('office_id', $request->input('office_id')))
            ->groupBy('batch_id')
            ->latest()
            ->paginate(10);

        return view('consignments.index', compact('consignments'));
    }

    /**
     * Show form to create consignments.
     */
    public function create()
    {
        return view('consignments.create');
    }

    /**
     * Generate consignments and print barcodes.
     */
    public function print(Request $request)
    {
        $this->validate($request, [
            'office_type' => 'required',
            'office_id' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);

        $officeType = $request->input('office_type');
        $officeId = $request->input('office_id');
        $quantity = (int) $request->input('quantity');
        $batchNumber = 'BATCH' . time(); // Generate unique batch number

        try {
            $barCodeImages = AppHelper::generateBarcode($officeType, $officeId, $batchNumber, $quantity);
            return view('consignments.print', compact('barCodeImages'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating consignments: ' . $e->getMessage());
        }
    }

    /**
     * Reprint consignments for a batch.
     */
    public function reprint($batchId)
    {
        $consignments = Consignment::where('batch_id', $batchId)->get();
        return view('consignments.reprint', compact('consignments'));
    }

    /**
     * Export consignments to Excel.
     */
    public function consignmentExport($batchId)
    {
        return Excel::download(new ConsignmentsExport($batchId), 'consignments.xlsx');
    }

    /**
     * Search office IDs dynamically.
     */
    public function searchOfficeId(Request $request)
    {
        $this->validate($request, ['term' => 'required']);
        $term = $request->input('term');

        $branches = Branch::select(['id', 'code as text'])->where('code', 'LIKE', "%$term%");
        $franchisees = Franchisee::select(['id', 'code as text'])->where('code', 'LIKE', "%$term%")->union($branches)->get();

        return response()->json(['data' => $franchisees]);
    }
}
