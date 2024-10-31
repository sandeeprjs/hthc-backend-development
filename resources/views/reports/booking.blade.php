@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Booking Report</span></h1>

                </div>
               
            </div>
        </div>

        <div class="row justify-content-center">
        <div class="table-responsive col-12 mt-3">
                <div class="card">

                    <table class="table">
                        <thead>
                        <tr rowspan="2">
                            <th rowspan="2" scope="col">#</th>
                            <th scope="col">Branch Code.</th>
                            <th scope="col">Branch Name</th>
                            <th scope="col" colspan="2">Total Booking</th>
                            <th scope="col" colspan="2">Total Weight</th>
                            <th scope="col" colspan="2">Total Amount</th>
                        </tr>
                        <tr colspan="2">
                            
                            <th></th>
                            <th></th>
                            <th>Dox</th>
                            <th>Non Dox</th>
                            <th>Dox</th>
                            <th>Non Dox</th>
                            <th>Dox</th>
                            <th>Non Dox</th>
                          
                            <th></th>
                        </tr>
                        
                        </thead>
                        <tbody>
                        @php
                            $index = $bookings->firstItem()
                        @endphp
                        @foreach($bookings as $booking)
                        <tr>
                                <th scope="row">{{ $index++ }}</th>
                                <td>{{ $booking->BranchDetails->code }}</td>
                                <td>{{ $booking->BranchDetails->branch_name }}</td>
                                <td>{{ $booking->dox }}</td>
                                <td>{{ $booking->nondox }}</td>
                                <td>{{ $booking->totalWeight }}</td>
                                <td>{{ $booking->totalWeight }}</td>
                                <td>{{ $booking->totalAmount }}</td>
                              
                        </tr>

                        @endforeach
                       
                        </tbody>
                    </table>
                </div>
               
            </div>
        </div>
    </div>







@endsection
