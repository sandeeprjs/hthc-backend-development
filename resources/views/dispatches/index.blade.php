@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Dispatches</span></h1>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('dispatches.create') }}">Add New Dispatches</a>
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
                                <div class="card">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Destination</th>
                                        <th scope="col">Consignment Number</th>
                                        <th scope="col">Vehicle No</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($dispatches as $dispatch)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ $dispatch->dest_office_id }}</td>
                                            <td>{{ $dispatch->consg_number }}</td>
                                            <td>{{ $dispatch->vehicle_number }}</td>
                                            <td>{{ $dispatch->status }}</td>
                                            <td>
                                                <form action="{{ route('dispatches.destroy',$dispatch->id) }}" method="POST">
                                                    <a class="btn btn-secondary btn-sm" href="{{ route('dispatches.edit',$dispatch->id) }}">{{'Edit'}}</a>

{{--                                                    @csrf--}}
{{--                                                    @method('DELETE')--}}

{{--                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>--}}
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                    </div>
                </div>
        </div>
    </div>


    {!! $dispatches->links() !!}




@endsection
