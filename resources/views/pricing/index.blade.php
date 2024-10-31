@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Pricing</span></h1>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('pricing.create') }}">Add New Price</a>
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
                                    <th>#</th>
                                    <th>From Weight (KGs)</th>
                                    <th>To Weight (KGs)</th>
                                    <th>Price (INR)</th>
                                    <th>Additional Weight (KGs)</th>
                                    <th>Additional Price (INR)</th>
                                    <th>Consignment Type</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($pricings as $pricing)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ $pricing->from_weight_kgs }}</td>
                                        <td>{{ $pricing->to_weight_kgs }}</td>
                                        <td>{{ $pricing->price }}</td>
                                        <td>{{ $pricing->addl_weight }}</td>
                                        <td>{{ $pricing->addl_price }}</td>
                                        <td class="text-capitalize">{{ $pricing->consg_type }}</td>
                                        <td>
                                            <form action="{{ route('pricing.destroy',$pricing->id) }}" method="POST">
                                                <a class="btn btn-secondary btn-sm" href="{{ route('pricing.edit',$pricing->id) }}">Edit</a>
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
    {!! $pricings->links() !!}
@endsection
