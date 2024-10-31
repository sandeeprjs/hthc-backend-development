<?php

namespace App\Exports;

use App\Consignment;
use Maatwebsite\Excel\Concerns\FromCollection;

class ConsignmentsExport implements FromCollection
{

    protected $request;
    protected $batch_id;
    public function __construct($batch_id)
    {
        $this->batch_id = $batch_id;
       
    }
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $consignments = Consignment::where('batch_id',$this->batch_id)->orderBy('created_at', 'asc')->get();
        $result = [];
        if($consignments){
           foreach($consignments as $key => $consignment){
               
                $result[] = [
                            $consignment->consg_number
                            
                ];
              
            }
       }
       return collect($result); 
    }
    public function headings(): array
    {
        return [
            'Consignment Number'
        ];
    }
}
