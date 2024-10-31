@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

@section('content')
<div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Edit Manifest</span></h1>
                </div>
                @if($manifests->manifest_type == 'O')
                <a class="btn btn-primary" href="{{ route('manifests.outgoing') }}"> View Outgoing Manifests</a>
                @elseif($manifests->manifest_type == 'I')
                <a class="btn btn-primary" href="{{ route('manifests.incoming') }}"> View Incoming Manifests</a>
                @endif
            </div>

        </div>

        <div class="row">
            <div class="col-md-8">

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="">

                    <form id="editManifest" action="@if($manifests->manifest_type == 'O'){{ route('manifests.outgoing_update',$manifests->id) }} @else {{ route('manifests.incoming_update',$manifests->id) }} @endif" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group required">
                                    <label>Consignment Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="manifest_number"
                                           placeholder="Consignment Number" value="{{ $manifests->manifest_number }}" id="manifest_number" required>
                                </div>
                                <div id="errorMsgExistManifest" class="text-danger"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group required">
                                    <label>Origin<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="origin_branch_id"
                                           placeholder="Origin Place" value="{{ $manifests->booking->pincode->pincode }}" id="origin_branch_id" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group required">
                                    <label>Destination<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="dest_branch_id"
                                           placeholder="Destination Branch" value="{{ $manifests->booking->delivery->pincode->pincode }}" id="dest_branch_id" required>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group sender_div required">
                                    <label>Sender <span class="text-danger">*</span></label>
                                    @if($manifests->manifest_type == 'O')
                                    <input type="text" class="form-control" name="sender_id"
                                           placeholder="Sender Office Cod" id="sender_id" value="{{ $loggedOffice->code }}" readonly>
                                    @else
                                           <select id="sender_id" name="sender_id" class="form-control @error('sender_code') is-invalid @enderror">
                                                            @if(isset($branchFranchisees))
                                                            @foreach($branchFranchisees as $branch)
                                                             
                                                                @if(isset($loggedOffice))
                                                                            <option value="{{$branch->code}}" @if($branch->code == $manifests->sender_office) selected ="selected" @endif >
                                                                            {{$branch->code}}
                                                                            </option>
                                                                @elseif(old('sender_id'))
                                                                            <option value="{{$branch->code}}" @if($branch->code == old('sender_id')) selected ="selected" @endif >
                                                                            {{$branch->code}}
                                                                            </option>
                                                                @endif
                                                            @endforeach
                                                       
                                                        @endif
                                             </select>
                                @endif
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group receiver_div required">
                                    <label>Receiver <span class="text-danger">*</span></label>
                                    @if($manifests->manifest_type == 'I' )
                                    <input type="text" class="form-control" name="receiver_id"
                                           placeholder="Receiver Office Code" id="receiver_id_text" value="{{ $loggedOffice->code }}" readonly>
                                    @else
                                            <select id="receiver_id_select" name="receiver_id" class="form-control @error('receiver_code') is-invalid @enderror">
                                                            @if(isset($branchFranchisees))
                                                             
                                                             @foreach($branchFranchisees as $branch)
                                                              
                                                                 @if(isset($manifests->receiver_office))
                                                                             <option value="{{$branch->code}}" @if($branch->code == $manifests->receiver_office) selected ="selected" @endif >
                                                                             {{$branch->code}}
                                                                             </option>
                                                                 @elseif(old('sender_id'))
                                                                             <option value="{{$branch->code}}" @if($branch->code == old('receiver_id')) selected ="selected" @endif >
                                                                             {{$branch->code}}
                                                                             </option>
                                                                 @endif

                                                            @endforeach
                                                       
                                                        @endif
                                            </select>
                                @endif
                                    
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label>Manifest Status</label>
                                    <input type="text" class="form-control" name="remarks"
                                           placeholder="" value="{{ $manifests->remarks }}" id="remarks">
                                </div>
                               
                                <div class="form-group pl-4">
                                    <div class="form-group">
                                        <input type="checkbox" name="customer_view"
                                        value="1" class="form-check-input" id="customer_view" @if($manifests->customer_view == 1) {{ 'checked' }} @endif >
                                        <label class="form-check-label" for="customer_view ">{{'Customer Can Track'}}</label>
                                    </div>
                                </div>
                              
                                @if($manifests->manifest_type == 'O')
                                <div class="form-group pl-4">
                                    <input id="last_outgoing" type="checkbox" name="last_mile_delivery" 
                                    @if(isset($manifests)) 
                                        {{ $manifests->last_mile_delivery == '1' ? 'checked' : '' }} 
                                    @endif
                                      value="1" class="form-check-input" id="last_outgoing">
                                    <label class="form-check-label" for="exampleCheck1">{{'Last Mile Delivery'}}</label>
                                </div> 
                                <div class="form-group pl-4 col-md-4 delivery_div">
                                <label>Delivery Person</label>
                                <select id="delivery_user_id" name="delivery_user_id" class="form-control @error('delivery_user_id') is-invalid @enderror">
                                    <option value=''> Select Delivery Person</option>
                                    @if(isset($employees))
                                        @foreach($employees as $employee)
                                            <option value="{{$employee->id}}" @if($employee->id == $manifests->delivery_user_id  ) selected ="selected" @endif >
                                            {{ $employee->username }} [ {{$employee->first_name .' '. $employee->last_name}} ]
                                            </option>
                                    @endforeach
                                    @endif
                                </select>
                                </div>
                                @endif
                            </div>
                           

                            <div class="col-xs-12 col-sm-12 col-md-12 ">
                                <input type="hidden" id="manifest_type" name="manifest_type" value="{{ $manifests->manifest_type }}" />
                                <input type="hidden" id="loggedOfficeCode" value="{{ $loggedOffice->code }}" />
                                <input type="hidden" id="loggedOfficeId" name="logged_office_id" value="{{ $loggedOffice->code }}" />
                             
                            </div>
                        </div>

                    </form>
                    <div class="pt-lg-2">
                        <button form="editManifest" formmethod="POST" formaction="@if($manifests->manifest_type == 'O'){{ route('manifests.outgoing_update',$manifests->id) }} @else {{ route('manifests.incoming_update',$manifests->id) }} @endif" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                        <button for="deleteConfirm" class="btn btn-danger mx-sm-2" data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    {{ "Please confirm to delete this pincode" }}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                    <form action="@if($manifests->manifest_type == 'O'){{ route('manifests.outgoing_delete',$manifests->id) }} @else {{ route('manifests.incoming_delete',$manifests->id) }} @endif" method="POST" style="display:inline">
                                        @method('DELETE')
                                        {{ csrf_field() }}
                                        <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
    <style>
    .Incoming,.Outgoing {
        display:none;
    }
    /* #receiver_id_text, #sender_id_text{
        display:none;
    } */
    .delivery_div{
        display:none;
        padding-left:0 !important;
    }
    </style>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script text="javascript">

$('document').ready(function(){
     
           
            $('#last_outgoing').click(function(){
            if(this.checked){
                $('.delivery_div').show();
                $('.receiver_div').hide();
            }else{
                $('.delivery_div').hide();
                $('.receiver_div').show();
            }
            
        });
        setTimeout(() => {
            
                    if($('#last_outgoing').is(":checked")){
                        
                        $('.delivery_div').show();
                        $('.receiver_div').hide();
                    }else{
                        $('.delivery_div').hide();
                        $('.receiver_div').show();
                    }
        },10);

            $('#manifest_number').change(function(){
                  var manifestNumber =  $(this).val();
                  var manifest_type = $('#manifest_type').val();
                 alert(manifestNumber);
                    jQuery.ajax({
                            url: "{{ url('/admin/booking-details') }}",
                            method: 'get',
                            data: {
                                manifest_number: manifestNumber,
                                manifest_type: manifest_type,
                            },
                            success: function(result){
                                $("#dest_branch_id").val(result.dest_branch_id);
                                $("#origin_branch_id").val(result.origin_branch_id);
                                if(result.status == "failed"){
                                    $('#errorMsgExistManifest').text(result.message);
                                    $('#manifest_number').val('');
                                }
                            }
                    });
            })


      });

        $('#sender_id').select2({
            placeholder: "Choose Origin Pincode",
            // minimumInputLength: 2,
            ajax: {
                url: "{{ url('/admin/branch-franchisee') }}",
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

        $('#receiver_id_select').select2({
            placeholder: "Choose Origin Pincode",
            // minimumInputLength: 2,
            ajax: {
                url: "{{ url('/admin/branch-franchisee') }}",
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
      

      

    </script>
   
@endsection