@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Pincodes</span></h1>
                </div>
                <div >
                    <button onclick="window.location.href = '{{ route('pincodes.create') }}'" class="btn btn-primary ">{{ 'Add New Pincode' }}</button>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">
                {!! session()->get('success')!!}
            </div>
        @endif


        <div class="row m-0">
            <form class="form-inline" action="{{ route('pincodes.index') }}">
                <div class="form-group mb-2">
                    <input type="text" class="form-control" id="pincode_search" name="pincode" value="{{ request()->input('pincode') }}" placeholder="Pincode">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <input type="text" class="form-control" id="city_search" name="city" value="{{ request()->input('city') }}" placeholder="City">
                </div>

                <button type="submit" class="btn btn-primary mb-2">{{ 'Search' }}</button>
                <a href="{{ route('pincodes.index') }}" class="btn mb-2 mx-sm-2 btn-light btn-outline-secondary">{{ 'Reset' }}</a>
            </form>
            <div class="card w-100">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Pincode</th>
                        <th scope="col">Area</th>
                        <th scope="col">City</th>
                        <th scope="col">District</th>
                        <th scope="col">State</th>
                        <th scope="col">Serviceable</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                      $index = $pincodes->firstItem()
                    @endphp
                    @foreach($pincodes as $pincode)
                        <tr>
                            <th scope="row">{{ $index++ }}</th>
                            <td>{{ $pincode->pincode }}</td>
                            <td>{{ $pincode->area_name }}</td>
                            <td>{{ $pincode->city }}</td>
                            <td>{{ $pincode->district }}</td>
                            <td>{{ $pincode->state }}</td>
                            <td>{{ $pincode->serviceable ? 'Yes' : 'No' }}</td>
                            <td><button onclick="window.location.href ='{{ route('pincodes.edit', $pincode->id) }}'" class="btn btn-sm btn-secondary">Edit</button></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            {!! $pincodes->links() !!}
        </div>
    </div>
@endsection
