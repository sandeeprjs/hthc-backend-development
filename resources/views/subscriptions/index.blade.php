@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Plans</span></h1>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('subscriptions.create') }}">Add New Plans</a>
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
                                        <th scope="col">Name</th>
                                        <th scope="col">Consignment type</th>
                                        <th scope="col">Price (in INR)</th>
                                        <th scope="col">Delivery Time</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($subscriptions as $subscription)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ $subscription->name }}</td>
                                            <td class="text-capitalize">{{ $subscription->consg_type }}</td>
                                            <td>{{ $subscription->price }}</td>
                                            <td>{{ $subscription->max_delivery_time }} hours</td>
                                            <td>
                                                <form action="{{ route('subscriptions.destroy',$subscription->id) }}" method="POST">
                                                    <a class="btn btn-secondary btn-sm" href="{{ route('subscriptions.edit',$subscription->id) }}">{{'Edit'}}</a>

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


    {!! $subscriptions->links() !!}




@endsection
