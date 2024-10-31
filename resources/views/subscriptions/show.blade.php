@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="row">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-left">
                                <h2>Show plan</h2>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-primary" href="{{ route('subscriptions.index') }}"> Back</a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Name</strong>
                                {{ $subscription->name }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Consignment type</strong>
                                {{ $subscription->consg_type }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Price</strong>
                                {{ $subscription->price }} Rs
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Delivery Time</strong>
                                {{ $subscription->max_delivery_time }} hours
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
