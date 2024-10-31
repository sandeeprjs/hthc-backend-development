@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>

@section('content')
    <div class="container">

        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>@if(isset($employee)) Edit @else Add @endif Employee</span></h1>

                </div>
               <div>

                   <a class="btn btn-primary" href="{{ route('employees.index') }}">View Employees</a>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-8">
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            {{--                <div class="card-header">{{ __('Add New Employee') }} <span class="text-danger">*</span> </div>--}}


                            <div class="">

                                <form id="employee_form" method="POST" enctype="multipart/form-data"
                                      action="@if(isset($employee)){{ route('employees.update',$employee->id)}} @else {{ route('employees.store') }} @endif ">
                                        @if(isset($employee))
                                            @method('PUT')
                                        @endif
                                        @csrf
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="office_type" class="">{{ __('Office Type') }} <span
                                                        class="text-danger">*</span> </label>
                                                        <select  name="office_type" id="office_type"  required class="form-control @error('branch_type') is-invalid @enderror">
                                                    <option value="">Select Type</option>
                                                    <option value="HO" @if(isset($employee)){{ ($employee->office_type) == 'HO' ? 'selected':'' }}
                                                        @elseif (old('office_type') == 'HO' ) selected
                                                        @endif
                                                    >Head Office</option>
                                                    <option value="BR"  @if(isset($employee)){{ ($employee->office_type) == 'BR' ? 'selected':'' }}
                                                        @elseif (old('office_type') == 'BR' ) selected
                                                        @endif
                                                    >Branch Office</option>
                                                    <option value="FR"  @if(isset($employee)){{ ($employee->office_type) == 'FR' ? 'selected':'' }}
                                                        @elseif (old('office_type') == 'FR' ) selected
                                                        @endif
                                                    >Partner</option>
                                                
                                                    </select>
                                                    @error('branch_type')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                    @enderror
                                                </div>
                                        </div>

                                        <div class="col-4 office_code_section">

                                            <div class="form-group office_code_div">
                                                <label for="office_id" class="">{{ __(' Office Code ') }} <span
                                                        class="text-danger">*</span> </label>


                                                <select id="office_id" name="office_id"
                                                        class="form-control @error('office_id') is-invalid @enderror" required  @if(!isset($branches) && (old('office_id') == '')) disabled @endif >
                                                       <!-- @if(isset($branches))
                                                        @foreach($branches as $branch)
                                                           
                                                                @if(isset($employee))
                                                                            <option value="{{$branch->id}}" @if($branch->id == $employee->office_id) selected ="selected" @endif >
                                                                            {{$branch->code}}
                                                                            </option>
                                                                @elseif(old('office_id'))
                                                                            <option value="{{$branch->id}}" @if($branch->id == old('office_id')) selected ="selected" @endif >
                                                                            {{$branch->code}}
                                                                            </option>
                                                                @endif

                                                        @endforeach
                                                    @endif -->

                                                </select>

                                                @error('office_id')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="role_id"
                                                       class=" @error('role_id') is-invalid @enderror">{{ __('User Role') }}
                                                    <span class="text-danger">*</span> </label>
                                                    <select  name="role_id" id="role_id"  required class="form-control @error('user_role') is-invalid @enderror">
                                                    <option value="">Select Role</option>
                                                    @if($roles)
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->id }}" @if(isset($employee)){{ ($employee->user_role->role_id) == $role->id ? 'selected':'' }}
                                                                @elseif (old('role_id') == '1' ) selected
                                                                @endif
                                                            > {{ $role->name }} </option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                
                                                @error('role_id')
                                                            <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="first_name" class="">{{ __('First Name') }} <span
                                                        class="text-danger">*</span></label>

                                                <input id="first_name" type="text"
                                                       class="form-control @error('first_name') is-invalid @enderror"
                                                       name="first_name"
                                                       value="@if(isset($employee)){{$employee->first_name}}@else{{ old('first_name') }}@endif"
                                                       required
                                                       >

                                                @error('first_name')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="last_name" class="">{{ __(' Last Name') }} <span
                                                        class="text-danger">*</span></label>


                                                <input id="last_name" type="text"
                                                       class="form-control @error('last_name') is-invalid @enderror"
                                                       name="last_name"
                                                       value="@if(isset($employee)){{$employee->last_name}}@else{{ old('last_name') }}@endif"
                                                       required
                                                       >

                                                @error('last_name')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="mobile_number" class="">{{ __('Mobile Number') }} <span
                                                        class="text-danger">*</span></label>

                                                <input id="mobile_number" type="text"
                                                       class="form-control @error('mobile_number') is-invalid @enderror"
                                                       name="mobile_number"
                                                       value="@if(isset($employee)){{$employee->mobile_number}}@else{{ old('mobile_number') }}@endif"
                                                       required maxlength="10">

                                                @error('mobile_number')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="current_bank_name" class="">{{ __('Current Bank Name') }}

                                                </label>

                                                <input id="current_bank_name" type="text"
                                                       class="form-control @error('current_bank_name') is-invalid @enderror"
                                                       name="current_bank_name"
                                                       value="@if(isset($employee)){{$employee->current_bank_name}}@else{{ old('current_bank_name') }}@endif"
                                                       
                                                >

                                                @error('current_bank_name')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="branch_name" class="">{{ __('Branch name') }}

                                                </label>

                                                <input id="branch_name" type="text"
                                                       class="form-control @error('branch_name') is-invalid @enderror"
                                                       name="branch_name"
                                                       value="@if(isset($employee)){{$employee->branch_name}}@else{{ old('branch_name') }}@endif"
                                                       
                                                >

                                                @error('branch_name')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="account_number" class="">{{ __('Account Number') }}</label>

                                                <input id="account_number" type="text"
                                                       class="form-control @error('account_number') is-invalid @enderror"
                                                       name="account_number"
                                                       value="@if(isset($employee)){{$employee->account_number}}@else{{ old('account_number') }}@endif"
                                                       
                                                >

                                                @error('account_number')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="ifsc_code" class="">{{ __('IFSC Code') }}
{{--                                                    <span class="text-danger">*</span>--}}
                                                </label>

                                                <input id="ifsc_code" type="text"
                                                       class="form-control @error('ifsc_code') is-invalid @enderror"
                                                       name="ifsc_code"
                                                       value="@if(isset($employee)){{$employee->ifsc_code}}@else{{ old('ifsc_code') }}@endif"
                                                       
                                                >

                                                @error('ifsc_code')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="avatar" class="">{{ __('Profile Pic') }}
{{--                                                    <span class="text-danger">*</span>--}}
                                                </label>

                                                <input id="avatar" type="file"
                                                       class="form-control @error('avatar') is-invalid @enderror"
                                                       name="avatar"
                                                       value="@if(isset($employee)){{$employee->avatar}}@else{{ old('avatar') }}@endif"
                                                       
                                                >
                                                @error('avatar')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                               
                                            </div>
                                          
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="doc_proof" class="">{{ __('PAN / Aadhar / Voter ID proof') }}
{{--                                                    <span class="text-danger">*</span>--}}
                                                </label>

                                                <input id="doc_proof" type="file"
                                                       class="form-control @error('doc_proof') is-invalid @enderror"
                                                       name="doc_proof"
                                                       value="@if(isset($employee)){{$employee->doc_proof}}@else{{ old('doc_proof') }}@endif"
                                                       
                                                >

                                                @error('doc_proof')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                                
                                            </div>
                                          
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="email" class=" ">{{ __('Email') }} <span
                                                        class="text-danger">*</span> </label>


                                                <input id="email" type="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       name="email"
                                                       value="@if(isset($employee)){{$employee->email}}@else{{ old('email') }}@endif"
                                                       required>

                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12"></div>

                                        <div class="col-4">

                                            <div class="form-group">
                                                <label for="username" class="">{{ __('Username / Employee Code' ) }} <span
                                                        class="text-danger">*</span></label>

                                                <input id="username" type="text"
                                                       class="form-control @error('username') is-invalid @enderror"
                                                       name="username"
                                                       value="@if(isset($employee)){{$employee->username}}@else{{ $empCode }}@endif" readonly required>

                                                @error('username')
                                                   <span class="invalid-feedback" role="alert">
                                                      <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                               
                                            </div>
                                        </div>

                                      
                                            <div class="col-4">
                                                <div class="form-group">

                                                     @if(isset($employee))
                                                         <label for="password" class="">{{ __('Old Password') }}</label>
                                                     @else
                                                         <label for="password" class="">{{ __('Password') }}</label>
                                                     @endif
                                                    <input id="old_password" type="password"
                                                           class="form-control @error('password') is-invalid @enderror"
                                                           name="password"
                                                           value="{{ old('password') }}" >

                                                    @error('password')
                                                      <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                       </span>
                                                    @enderror
                                                    @if ($message = Session::get('passwordNotMatched'))
                                                    <div class="invalid-feedbac alert alert-danger">
                                                        <p>{{ $message }}</p>
                                                    </div>
                                                @endif
                                                </div>
                                            </div>
                                            @if(isset($employee))
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="new_password" class="">{{ __('New Password') }} </label>

                                                    <input id="new_password" type="password"
                                                           class="form-control @error('new_password') is-invalid @enderror"
                                                           name="new_password"
                                                           value="{{ old('new_password') }}" >

                                                    @error('new_password')
                                                      <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                       </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="password-confirm" class="">{{ __('Confirm Password') }}
                                                       </label>

                                                    <input id="password-confirm" type="password"
                                                           class="form-control @error('password_confirmation') is-invalid @enderror"
                                                           name="password_confirmation" value="{{ old('new_password') }}"  >
                                                    @error('password_confirmation')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        
                                        </div>
                                        @if(isset($franchisee))
                                            <input type="hidden" name='old_avatar' value="{{ $franchisee->avatar }}"/>
                                            <input type="hidden" name='old_doc_proof' value="{{$franchisee->doc_proof}}"/>
                                        @endif
                                        
                                </form>
                                <div class="pt-lg-2 col-md-12">
                              
                                            <button form="employee_form" formmethod="POST" type="submit" class="btn btn-primary">
                                                                @if(isset($employee)){{ __('Update') }} @else {{ __('Submit') }} @endif
                                            </button>
                                            @if(isset($employee))
                                            <button class="btn btn-danger"  data-toggle="modal" data-target="#deleteConfirmEMP">{{ 'Delete' }}</button>
                                            @endif
                            
                              </div>

                                @if(isset($employee))
                            

                                    <div class="modal fade" id="deleteConfirmEMP" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body">
                                                            {{ "Please confirm to delete the record" }}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline">
                                                               @csrf
                                                               @method('DELETE')
                                                                <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                            </div>

                        @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

   
    <script type="text/javascript">

    $('#office_id').select2({
            placeholder: "Choose office code",
           // minimumInputLength: 2,
            ajax: {
                url:  function() {

                        var office_type =$( "#office_type option:selected" ).val();
                        if (office_type == 'BR') {
                           return "{{ url('/admin/employee/findBranch?type=BR') }}"
                        } else if(office_type == 'FR'){
                            return "{{ url('/admin/employee/findBranch?type=FR') }}"
                        } else if(office_type == 'HO'){
                            return "{{ url('/admin/employee/findBranch?type=HO') }}"
                        }
                    },
               
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $(document).ready(function() {
               
                $('#office_type').change(function(){

                      var office_type =  $(this).val();
                      if(office_type!=''){
                            
                             $('#office_id').prop('disabled', false);
                      }else{
                      
                         $('#office_id').prop('disabled', true);
                      }
                })

                var  office_type =$( "#office_type option:selected" ).val();
               
                if(office_type !=''){
                                
                       var s;
                        $.ajax({
                             url:  "{{ url('/admin/employee/selectedBranch') }}",
                             dataType: 'json',
                             data: { 'type':office_type },
                             success: function (data) {
                                
                                  $.each(data,function (data,value) { 
                                    var sel = ''; 
                                      var dVal = value.id;
                                      var sVal = "{{old('office_id')}}"; 
                                     
                                      if(dVal == sVal){
                                           sel = 'selected';
                                      }
                                    s += '<option value="' + dVal + '"'+ sel +' >' + value.text + '</option>';  
              
                                })  
                                $("#office_id").html(s); 
                           
                             }
                       
                        });
                 }
            });
        
    </script>
@endsection

