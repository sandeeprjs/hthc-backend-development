@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4>Import Bookings</h4>
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('bookings.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="excel">Upload Excel File</label>
                        <input type="file" name="excel" id="excel" class="form-control @error('excel') is-invalid @enderror" required>
                        @error('excel')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Import Bookings</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <h5>Download Sample File</h5>
                <a href="{{ route('bookings.getBulkBookingSample') }}" class="btn btn-secondary">Download Sample</a>
            </div>
        </div>
    </div>
@endsection
