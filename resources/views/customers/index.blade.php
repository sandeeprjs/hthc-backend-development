@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Customers</span></h1>

                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('customers.create') }}">
                        Create New Customer</a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12">


                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif

                            <div class="filter_form">
                           <form class="form-inline" action="{{ route('customers.index') }}">
                                <div class="form-group mr-sm-2 mb-2">
                                    <input type="text" placeholder="Customer Code" class="form-control" id="customer_code" name="customer_code" value="{{ request()->input('customer_code') }}">
                                </div>
                                <div class="form-group mr-sm-2 mb-2">
                                    <input type="text" placeholder="Customer Name" class="form-control" id="customer_name" name="customer_name" value="{{ request()->input('customer_name') }}">
                                </div>
                                <div class="form-group mr-sm-3 mb-2">
                                    <input type="text" placeholder="Mobile Number" class="form-control" id="mobile_number" maxlength="10" name="mobile_number" value="{{ request()->input('mobile_number') }}">
                                </div>


                                <button type="submit" class="btn btn-primary mr-3  mb-2">
                                            {{ __('Search') }}
                                        </button>
                                <a title="Reset" href="{{route('customers.index')}}" class="btn btn-group  mb-2 btn-outline-dark">Reset</a>

                            </form>

                        </div>

                            <div class="card">
                            <table class="table table-bordered">
                                <tr>
                                    <th>No</th>
                                    <th>Code</th>
                                    <th>Customer Name</th>
                                    <th>City</th>
                                    <th>Email</th>
                                    <th>Mobile Number</th>
                                    <th>Pincode</th>
                                    <th>Action</th>
                                </tr>
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $customer->code }}</td>
                                        <td>{{ $customer->customer_name }}</td>
                                        <td>{{ $customer->city }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->mobile_number }}</td>
                                        <td>
                                        @if($customer->pincode)
                                        {{ $customer->pincode->pincode }}
                                        @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('customers.destroy',$customer->id) }}"
                                                  method="POST">


                                                <a class="btn btn-secondary btn-sm"
                                                   href="{{ route('customers.edit',$customer->id) }}">Edit</a>

                                                <!-- @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button> -->
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            </div>
                            {!! $customers->links() !!}
                        </div>
                    </div>

            </div>
        </div>
    </div>







@endsection
