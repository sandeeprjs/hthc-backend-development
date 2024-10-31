@extends('layouts.app')

@section('content')
    <!-- ./Section header -->
    <!-- Main content -->
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Partners</span></h1>
                  
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('franchisees.create') }}"> Add New Partner</a>
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
                           <form class="form-inline" action="{{ route('franchisees.index') }}">
                                <div class="form-group mb-2">
                                <select class="form-control  @error('filter_by') is-invalid @enderror"
                                                        name="filter_by" id="filter_by" required>
                                                    <option value=''>Filter By</option>
                                                    <option value='CD'
                                                            @if(request()->input('filter_by') == 'CD')  selected @endif >
                                                        Partner Code
                                                    </option>
                                                    <option value='NM'
                                                            @if(request()->input('filter_by') == 'NM')  selected @endif >
                                                         Partner Name
                                                    </option>
                                                    <option value='TY'
                                                            @if(request()->input('filter_by') == 'TY')  selected @endif >
                                                        Partner Type
                                                    </option>
                                                    <option value='MBL'
                                                            @if(request()->input('filter_by') == 'MBL')  selected @endif >
                                                        Mobile Number
                                                    </option>
                                                   
                                        </select>
                                   
                                </div>
                                <div class="form-group mx-sm-3 mb-2 ">
                                    <select id="partner_type" class="form-control partner-type-fld" name="filter_value">
                                            <option value="">-- Select --</option>
                                            <option value="BOOKING" @if(request()->input('filter_value') == 'BOOKING')  selected @endif > Booking </option>
                                            <option value="DELIVERY" @if(request()->input('filter_value') == 'DELIVERY')  selected @endif > Delivery </option>
                                            <option value="BOTH" @if(request()->input('filter_value') == 'BOTH')  selected @endif > Both </option>
                                    </select>
                                    <input type="text" placeholder="Filter Value" class="form-control" id="filter_value" name="filter_val" value="{{ request()->input('filter_val') }}">
                                </div>


                                <button type="submit" class="btn btn-primary mr-2 mb-2">
                                            {{ __('Search') }}
                                        </button>
                                <a title="Reset" href="{{route('franchisees.index')}}" class="btn btn-group btn-outline-dark mb-2">Reset</a>

                            </form>

                        </div>
                        <div class="table-responsive">
                            <div class="card">
                        <table id="listDataTable" class="table table-bordered  list_view_table display responsive no-wrap" width="100%">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Partner Name</th>
                                <th>Branch Name</th>
                                <th>Contact Person</th>
                                <th>Mobile Number</th>
                                <th>Origin Pincode</th>
                                <th>Partner Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($franchisees))
                            @php
                              $index = $franchisees->firstItem()
                            @endphp

                            @foreach($franchisees as $franchisee)
                                <tr>
                                    <td>{{ $index++ }}</td>
                                    <td>{{ $franchisee->code }}</td>
                                    <td>{{ $franchisee->enterprise_name }}</td>
                                    <td>{{ $franchisee->branch->code }} [ {{ $franchisee->branch->branch_name }} ]</td>
                                    <td>{{ $franchisee->contact_person_name }}</td>
                                    <td>{{ $franchisee->mobile_number }}</td>
                                    <td>{{ $franchisee->pincode->pincode }}</td>
                                    <td>{{ $franchisee->franchisee_type }}</td>
                                    <td>
                                         <div class="btn-group">
                                            <a title="Edit" href="{{route('franchisees.edit',$franchisee->id)}}" class="btn btn-secondary btn-sm">Edit</a>
                                            <a title="View" href="{{route('franchisee.view',$franchisee->id)}}" class="btn btn-secondary btn-sm">View</a>
                                         </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                           </table>

                           {{ $franchisees->links() }}
                           @endif
                            </div>
                    </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
        </div>
        <style>
        select#partner_type{
            display:none;
        }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                $(".successMessage").delay(6000).slideUp(300);

                setTimeout(() => {
                    var filter_by = $('#filter_by option:selected').val();
                    if(filter_by == 'TY'){
                        $('#filter_by').trigger('change');
                    }
                },0);

                

                $('#filter_by').change(function(){
                    var fVal = $(this).val();
                    if(fVal == 'TY'){
                         $('#filter_value').hide();
                         $('#partner_type').show();
                    }else{
                        $('#filter_value').show();
                         $('#partner_type').hide();
                        var filter_by = $.trim($('#filter_by option:selected').text());
                        $('#filter_value').attr("placeholder", 'Enter ' + filter_by);
                    }
                })
            });
        </script>
        </div>
        @endsection



