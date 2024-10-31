@extends('layouts.app')


@section('content')
    <!-- ./Section header -->
    <!-- Main content -->
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Employees</span></h1>
                </div>
                <div >
                    <a class="btn btn-primary" href="{{ route('employees.create') }}">Add New Employee </a>
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
                           <form class="form-inline" action="{{ route('employees.index') }}">
                               <div class="form-group mr-sm-2 mb-2">

                                        <select class="form-control  @error('filter_by') is-invalid @enderror"
                                                        name="filter_by" id="filter_by" required>
                                                    <option value=''>Filter By</option>
                                                    <option value='BR'
                                                            @if(request()->input('filter_by') == 'BR')  selected @endif >
                                                        Branch Code
                                                    </option>
                                                    <option value='FR'
                                                            @if(request()->input('filter_by') == 'FR')  selected @endif >
                                                         Partner Code
                                                    </option>
                                                    <option value='EMPC'
                                                            @if(request()->input('filter_by') == 'EMPC')  selected @endif >
                                                        Employee Code
                                                    </option>
                                                    <option value='EMPN'
                                                            @if(request()->input('filter_by') == 'EMPN')  selected @endif >
                                                        Employee Name
                                                    </option>
                                                     
                                                    <option value='MBL'
                                                            @if(request()->input('filter_by') == 'MBL')  selected @endif >
                                                        Employee Mobile
                                                    </option>
                                        </select>
                                </div>
                               
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" placeholder="Filter Value" class="form-control" id="filter_val" name="filter_val" value="{{ request()->input('filter_val') }}" required>
                                </div>
                              
                                <button type="submit" class="btn btn-primary mr-2 mb-2">
                                            {{ __('Search') }}
                                        </button>
                                <a title="Reset" href="{{route('employees.index')}}" class="btn btn-group mb-2 btn-outline-dark">Reset</a>

                            </form>

                        </div>
                        <div class="table-responsive">
                            <div class="card">
                                <table id="listDataTable" class="table table-bordered  list_view_table display responsive no-wrap" width="100%">
                                    <thead>
                                    <tr>
                                        <th width="5%">#</th>                                        <th width="10%">Employee Code</th>
                                        <th width="25%"> Name </th>
                                        <th width="15%"> Mobile Number</th>
                                        <th width="25%">Email</th>
                                        <th width="35%">User Role</th> 
                                        <th width="10%">Office Code </th>
                                        <th width="10%">Office Type </th>
                                        <th class="notexport" width="15%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($employees))
                                        @php
                                        $index = $employees->firstItem()
                                        @endphp
                                        @foreach($employees as $employee)
                                            <tr>
                                                <td>{{ $index++ }}</td>
                                                <td>{{ $employee->username }}</td>
                                                <td>{{ $employee->first_name}} {{$employee->last_name}}</td>
                                                <td>{{ $employee->mobile_number }}</td>
                                                <td>{{ $employee->email }}</td>
                                                <td>@if($employee->roles)
                                                        @foreach($employee->roles as $role)
                                                           {{ $role->name }}
                                                        @endforeach
                                                    @endif
                                               </td>
                                                <td> @if($employee->office_type == 'FR'){{ $employee->fr_code }} @else {{ $employee->br_code }}  @endif</td>
                                                <td>@if($employee->office_type == 'FR'){{ 'Partner' }} @elseif($employee->office_type == 'BR') {{ 'Branch' }} @elseif($employee->office_type == 'HO') {{ 'Head Office' }} @endif</td>
                                                
                                                <td>
                                                     @if($employee->user_type != 'admin')
                                                    <div class="btn-group">
                                                        <a title="Edit" href="{{ route('employees.edit',$employee->id)}}" class="btn btn-secondary btn-sm">Edit</a>
                                                        <a title="View" href="{{ route('employee.view',$employee->id)}}" class="btn btn-secondary btn-sm">View</a>
                                                    </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            {{ $employees->links() }}
                    </div>
                    </div>

                </div>
            </div>
        </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function(){
                $(".successMessage").delay(6000).slideUp(300);
                $('#filter_by').change(function(){

                     var filter_by = $.trim($('#filter_by option:selected').text());
                     $('#filter_val').attr("placeholder", 'Enter ' + filter_by);
                })
            });
        
        </script>
        @endsection



