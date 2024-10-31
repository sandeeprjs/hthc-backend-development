@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

@section('content')
<div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span> {{ $type == "I" ? 'Incoming' : "Outgoing" }} Manifest</span></h1>

                </div>
                <a class="btn btn-primary" href="{{ route('manifests.index') }}"> View Manifests</a>

            </div>

        </div>

        <div class="row">
            <div class="col-md-8">

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="">

                    <form action="{{ route('manifests.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group required">
                                    <label>Consignment Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="manifest_number"
                                           placeholder="AWB Number" id="manifest_number" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group required">
                                    <label>Origin<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="origin_branch_id"
                                           placeholder="Origin Place" id="origin_branch_id" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group required">
                                    <label>Destination<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="dest_branch_id"
                                           placeholder="Destination Branch" id="dest_branch_id" required>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group sender_div required">
                                    <label>Sender <span class="text-danger">*</span></label>
                                           @if($type=='O')
                                            <input type="text" class="form-control" name="sender_code"
                                                placeholder="Sender Office Code" id="sender_id_text" 
                                                value="{{ $loggedOffice->code }}" readonly />
                                            @endif   
                                            @if($type == 'I')
                                            <select id="sender_id" name="sender_id" class="form-control @error('sender_code') is-invalid @enderror">
                                                            @if(isset($branchFranchisees))
                                                            <option value=""> select </option>
                                                            @foreach($branchFranchisees as $branch)
                                                             
                                                                @if(isset($loggedOffice))
                                                                            <option value="{{$branch->code}}"  >
                                                                            {{$branch->code}}
                                                                            </option>
                                                                @elseif(old('sender_id'))
                                                                            <option value="{{$branch->code}}" @if($branch->id == old('sender_id')) selected ="selected" @endif >
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
                                    @if($type == 'I')
                                    <input type="text" class="form-control" name="receiver_code"
                                           placeholder="Receiver Office Code" id="receiver_id_text" 
                                           value="{{ $loggedOffice->code }}" readonly/>
                                    @endif
                                    @if($type == 'O')
                                    <select id="receiver_id_select" name="receiver_id" class="form-control @error('receiver_code') is-invalid @enderror">
                                                            @if(isset($branchFranchisees))
                                                             
                                                             @foreach($branchFranchisees as $branch)
                                                              
                                                                 <!-- @if(isset($loggedOffice))
                                                                             <option value="{{$branch->id}}" @if($branch->id == $loggedOffice->id) selected ="selected" @endif >
                                                                             {{$branch->code}}
                                                                             </option>
                                                                 @elseif(old('sender_id')) -->
                                                                             <option value="{{$branch->code}}" @if($branch->id == old('receiver_id')) selected ="selected" @endif >
                                                                             {{$branch->code}}
                                                                             </option>
                                                                 <!-- @endif -->

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
                                           placeholder="" id="remarks">
                                </div>
                                <div class="form-group pl-4">
                                    <input type="checkbox" name="last_mile_delivery"
                                       
                                      value="1" class="form-check-input" id="last_outgoing">
                                    <label class="form-check-label" for="exampleCheck1">{{'Last Mile Delivery'}}</label>
                                </div> 
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12">
                            
                                <button type="submit" class="btn btn-primary">Submit</button>

                                <input type="text" id="sender_type" name="sender_type" value="" />

                                <!-- <input type="text" id="loggedOfficeCode" value="{{ $loggedOffice->code }}" />
                                <input type="text" id="loggedOfficeId" name="logged_office_id" value="{{ $loggedOffice->code }}" />
                                <input type="text" id="loggedOfficePincode" name="logged_office_pincode" value="{{ $loggedOffice->pincode_id }}" /> -->
                                <input type="text" id="manifest_type" name="manifest_type" value="{{ $type }}" />
                             
                            </div>
                        </div>

                    </form>

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
    .select2-results__message{
        display:none;
    }
    </style>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script text="javascript">

$('document').ready(function(){
            
            // $('#manifest_type').change(function(){
               
            //     var manifestType = $(this).val();
            //     var loggedOffice = $('#loggedOfficeCode').val();
            //     if(manifestType=='I'){
            //         $('#receiver_id_text').show();
            //         $('#receiver_id_text').val(loggedOffice);
            //         $('#receiver_id_select option:selected').val(loggedOffice)
            //         $('#receiver_id_text').attr('readonly',true);
            //         $(".receiver_div .select2").hide();

            //         $('#sender_id_text').hide();
            //         $('#sender_id_text').val();
            //         $(".sender_div .select2").show();
                   
                    
            //     }
            //     if(manifestType=='O'){
            //         $('#sender_id_text').show();
            //         $('#sender_id_text').val(loggedOffice);
            //         $('#sender_id_text').attr('readonly',true);
            //         $(".sender_div .select2").hide();

            //         $('#receiver_id_text').hide();
            //         $('#receiver_id_text').val('');
            //         $(".receiver_div .select2").show();
            //     }
            // });

            $('#manifest_number').change(function(){
                  var manifestNumber =  $(this).val();
                 
                    jQuery.ajax({
                            url: "{{ url('/admin/booking-details') }}",
                            method: 'get',
                            data: {
                                manifest_number: manifestNumber,
                            },
                            success: function(result){
                                
                                $("#dest_branch_id").val(result.dest_branch_id);
                                $("#origin_branch_id").val(result.origin_branch_id);
                                $("#sender_type").val(result.booking_office_type);
                               // $('#sender_id>option:eq('+result.booking_branch_code+')').attr('selected', true);
                               /// $("#sender_id").val(result.booking_branch_code).trigger("change");
                            }
                    });
            })


      });

        $('#sender_id').select2({
            placeholder: "Choose Sender",
            minimumInputLength: 2,
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
            placeholder: "Choose Receiver",
            minimumInputLength: 2,
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