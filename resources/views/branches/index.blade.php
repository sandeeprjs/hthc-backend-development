@extends('layouts.app')

@section('content')
    <!-- ./Section header -->
    <!-- Main content -->
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Branches</span></h1>

                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('branches.create') }}">Add New Branch </a>
                </div>

            </div>

        </div>
        @if ($message = Session::get('success'))
                                    <div class="successMessage alert alert-success">
                                        <p>{{ $message }}</p>
                                    </div>
        @endif
    <div class="row">

            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <div class="box-tools pull-right">

                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body margin-top-20">

                        <div class="filter_form">
                           <form class="form-inline" action="{{ route('branches.index') }}">
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" placeholder="Branch Code" class="form-control" id="branch_code" name="branch_code" value="{{ request()->input('branch_code') }}">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" placeholder="Branch Name" class="form-control" id="branch_name" name="branch_name" value="{{ request()->input('branch_name') }}">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" placeholder="Pincode" class="form-control" id="pincode" name="pincode" value="{{ request()->input('pincode') }}">
                                </div>



                                <button type="submit" class="btn btn-primary mr-3 mb-2">
                                            {{ __('Search') }}
                                        </button>
                                <a title="Reset" href="{{route('branches.index')}}" class="btn btn-group btn-outline-dark  mb-2">Reset</a>

                            </form>

                        </div>
                        <div class="table-responsive">
                            <div class="card">
                                <table id="listDataTable" class="table table-bordered list_view_table display responsive no-wrap" width="100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Branch Code</th>
                                        <th>Branch Name</th>
                                        <th>Address</th>
                                        <th>Contact Person</th>
                                        <th>Mobile Number</th>
                                        <th>Origin Pincode</th>
                                        <th>Email</th>
                                        <th>City</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($branches))
                                    @php
                                    $index = $branches->firstItem()
                                    @endphp
                                    @foreach($branches as  $branch)
                                        <tr>
                                            <td>{{ $index++ }}</td>
                                            <td>{{ $branch->code }}</td>
                                            <td>{{ $branch->branch_name }}</td>
                                            <td>{{ $branch->add_line_1}}<br>{{$branch->add_line_2}}</td>
                                            <td>{{ $branch->incharge_name }}</td>
                                            <td>{{ $branch->mobile_number }}</td>
                                            <td>{{ $branch->pincode }}</td>
                                            <td>{{ $branch->email }}</td>
                                            <td>{{ $branch->city }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a title="Edit" href="{{route('branches.edit',$branch->id)}}" class="btn btn-secondary btn-sm">Edit</a>
                                                </div>
                                            </td>
                                    </tr>
                                    @endforeach
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                    </div>
                    </div>
                    <div>
                    {{ $branches->links() }}
                    </div>





                    <!-- /.box-body -->
                </div>
            </div>
        </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function(){
                $(".successMessage").delay(6000).slideUp(300);

                $("#pincode").keyup(function () {
                    this.value = this.value.replace(/[^0-9\.]/g,'');
                });

            });
        </script>
        @endsection



