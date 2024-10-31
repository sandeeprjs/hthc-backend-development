<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use App\Imports\Manifest\IncomingImport;
use App\Imports\Manifest\OutgoingImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ManifestController extends Controller
{
    public function __construct() {
        $this->middleware(['auth', 'role'])->except(['getManifestSample']);
    }

    public function incomingImport(Request $request) {
        $this->validate($request, [
            'excel' => 'required',
            'customer_view' => 'nullable',
            'status' => 'nullable'
        ]);

        $import = new IncomingImport($request->except('_token', 'excel'));

        try {
            Excel::import($import, $request->file('excel'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }

        return redirect(route('manifests.incoming'))->withSuccess($import->getAbsoluteRowCount().'/'.$import->getTotalRowCount().' Bulk Incoming Manifest Created Successfully');
    }

    public function outgoingImport(Request $request) {
        $this->validate($request, [
            'excel' => 'required',
            'customer_view' => 'nullable',
            'status' => 'nullable',
            'office_type' => 'nullable',
            'receiver_id' => 'nullable'
        ]);

        $import = new OutgoingImport($request->except('_token', 'excel'));

        try {
            Excel::import($import, $request->file('excel'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }

        return redirect(route('manifests.outgoing'))->withSuccess($import->getAbsoluteRowCount().'/'.$import->getTotalRowCount().' Bulk Incoming Manifest Created Successfully');
    }

    public function getManifestSample() {
        $file = public_path().'/files/manifest_sample.xlsx';

        return Response::download($file, 'manifest_sample.xlsx');
    }
}
