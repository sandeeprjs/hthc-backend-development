@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Modes</span></h1>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('modes.create') }}">Add New modes</a>
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
                                        <th scope="col">Code</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Description</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($modes as $mode)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ $mode->code }}</td>
                                            <td class="text-capitalize">{{ $mode->name }}</td>
                                            <td>{{ $mode->type }}</td>
                                            <td>{{ $mode->description }}</td>
                                            <td>
                                                <form action="{{ route('modes.destroy',$mode->id) }}" method="POST">
                                                    <a class="btn btn-secondary btn-sm" href="{{ route('modes.edit',$mode->id) }}">{{'Edit'}}</a>

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


    {!! $modes->links() !!}




@endsection
