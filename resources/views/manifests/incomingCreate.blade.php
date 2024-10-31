@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

@section('content')
<div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span> Incoming Manifest</span></h1>

                </div>
                <a class="btn btn-primary" href="{{ route('in-manifest-import') }}"> Upload IM</a>

            </div>

        </div>

        <div class="row">
            <div class="col-md-8">

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    <form action="{{ route('manifests.incoming_store') }}" method="POST" id="incoming_manifest">
                        @csrf

                        <div class="row">
                       
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group ">
                                    <label>Consignment Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control scan_manifest @error('manifest_number') is-invalid  @enderror" name="manifest_number"
                                          value="{{ old('manifest_number') }}" placeholder="Consignment Number" id="manifest_number"  >
                                            @error('manifest_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div id="errorMsgExistManifest" class="text-danger"></div>
                                </div>

                            </div>                           
                            <div class="col-xs-12 col-sm-12 col-md-8"> 
                                <div class="form-group ">

                                <div class="pr-0 text-right">
                                 Count    <div id = "scan_count" class="badge badge-pill badge-outline-info"> 0 </div>
                                </div>

                               
                                  <button  type="submit" class="btn btn-primary sbt-btn">Submit</button>
                                </div>
                            </div>
                            </div>
                            <div class="col-12 pl-0 scanned_consignments">
                            
                                <div class="table-responsive col-12 mt-3 mb-3">
                                    <div class="card">
                                        <table class="table js-serial">
                                            <thead>

                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Consg. No.</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="buildyourform">

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 im-remarks">
                                <div class="form-group">
                                    <label>Manifest Status</label>
                                    <input type="text" class="form-control" name="remarks"
                                    value="{{ old('remarks') }}"  placeholder="" id="remarks">
                                </div>
                            </div>
                           <!--- <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group pl-4">
                                    <input type="checkbox" name="customer_view"
                                      value="1" class="form-check-input" id="customer_view" checked>
                                    <label class="form-check-label" for="customer_view ">{{'Customer Can Track'}}</label>
                                </div>
                            </div>
                            </div> -->

                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <input type="hidden" id="sender_id" name="sender_id" value="" />
                                <input type="hidden" id="receiver_id_text" name="receiver_id" value="{{ $loggedOffice->code }}"/>
                                <input type="hidden" id="manifest_type" name="manifest_type" value="I" />
                               


                            </div>
                        </div>

                    </form>

                    <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    {{ "Please confirm to delete the record" }}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="removeCancelled()">{{ 'Close' }}</button>
                                    <button type="button" class="btn btn-secondary btn-ok" onClick="removeConfirmed()"  data-dismiss="modal">{{ 'Confirm' }}</button>
                                    <input  type="hidden" id="remConfirmation" class="" value="" />
                                    
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
    span#scan_count {
       margin-left: 50px;
    }
    .badge {
     border-radius: 0;
     font-size: 15px;
     line-height: 1;
     padding: .375rem .5625rem;
     font-weight: bold
 }

 .badge-outline-primary {
     color: #405189;
     border: 1px solid #405189
 }
    .badge.badge-pill {
     border-radius: 10rem
 }

 .badge-outline-info {
     color: #3da5f4;
     border: 2px solid #3da5f4
 }
    .Incoming,.Outgoing {
        display:none;
    }
    .select2-results__message{
        display:none;
    }
    .hidden_area{
        display:none;
    }
    #buildyourform{
        max-height:400px;
        overflow:auto;
        width:500px;

    }
    .scan_manifest_hide{
        display:none;
    }
    .scanned_consignments{
        display:none;
    }
    input.scan_manifest{
        border:none;
    }
    .sbt-btn{
        float:right;
        margin-top:20px;
        ma{rgin-right:20px;
    }
    .col-xs-12.col-sm-12.col-md-12.im-remarks {
         margin-left: -15px !important;
    }
    
    </style>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script text="javascript">

    $('document').ready(function(){





            $('.scan_manifest').change(function(event){

                event.preventDefault();
                  var manifestNumber =  $(this).val();

                  $(this).val('');
                  $(this).focus();
                  $('input[name^="manifest_number"]').each(function(e) {
                    var scanned = $(this).val();
                    if(scanned != ''){
                        if(scanned.trim() == manifestNumber.trim() ){

                            $('#errorMsgExistManifest').text('Already scanned this consignment - ' + manifestNumber );
                            e.preventDefault();
                        }
                    }
                });


                    jQuery.ajax({
                            url: "{{ url('/admin/booking-details') }}",
                            method: 'get',
                            data: {
                                manifest_number: manifestNumber,
                                manifest_type: 'I'
                            },
                            success: function(result){

                                $('.scanned_consignments').show();
                                $('#errorMsgExistManifest').text('');
                                $("#dest_branch_id").val('');
                                $("#origin_branch_id").val('');
                                $("#sender_id").val('');
                                $("#sender_type").val('');
                                $('#manifest_number').prop('required',false);
                                if(result.status == "success"){
                                    $("#dest_branch_id").val(result.dest_branch_id);
                                    $("#origin_branch_id").val(result.origin_branch_id);
                                    $("#sender_id").val(result.booking_office_code);
                                    $("#sender_type").val(result.booking_office_type);
                                    var lastField = $("#buildyourform tr:first");
                                    var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
                                    var fieldWrapper = $("<tr class=\" table fieldwrapper\" id=\"field" + intId + "\">");
                                    fieldWrapper.data("idx", intId);
                                   // var sno =  $("<td>"+intId+"</td>");
                                    var fName = $("<td><input type=\"text\" class=\"scan_manifest\" name=\"manifest_number[]\" value="+manifestNumber+" readonly/> </td>");
                                    var removeButton = $("<td><input type=\"button\" class=\"remove\" value=\"-\" data-toggle=\"modal\" data-target=\"#deleteConfirm\"/> </td></tr>");
                                    var origin = $("<input type=\"text\" class=\"scan_manifest_hide\" name=\"origin_branch_id[]\" value="+result.origin_branch_id+" />");
                                    var destination = $("<input type=\"text\" class=\"scan_manifest_hide\" name=\"dest_branch_id[]\" value="+result.dest_branch_id+" />");

                                    removeButton.click(function(res) {
                                         $('#remConfirmation').val($(this).parent().attr('id'));
                                    });
                                   // fieldWrapper.append(sno);
                                    fieldWrapper.append(fName);
                                    fieldWrapper.append(removeButton);
                                    fieldWrapper.append(origin);
                                    fieldWrapper.append(destination);
                                // fieldWrapper.append(fType);

                                    $("#buildyourform").prepend(fieldWrapper);
                                    // $("#buildyourform").append(fieldWrapper);

                                    $('.js-serial tr td:first-child').each(function(i){
                                    $(this).before('<td>'+(i+1)+'</td>');
                                    if(i > 0){
                                        $(this).remove();
                                    }
                                 });
                                var scannedCount = parseInt($("#scan_count").text()) + 1;
                                   $("#scan_count").text(scannedCount);

                                }
                                if(result.status == "failed"){
                                    $('#errorMsgExistManifest').text(result.message);
                                    $('#manifest_number').val('');
                                }
                                
                                //addRowCount('js-serial');

                            }
                    });


            })
            $(document).on("keypress", 'form', function (e) {
                var code = e.keyCode || e.which;

                if (code == 13) {

                    e.preventDefault();
                    $('.scan_manifest').change();
                    return false;
                }
            });



    });
    
    function removeConfirmed(){
        
       var rem = $('#remConfirmation').val();
       $('#'+rem).remove();
       var scannedCount = parseInt($("#scan_count").text()) - 1;
       $("#scan_count").text(scannedCount);
    }

    function addRowCount(tableAttr) {
        $(tableAttr).each(function(){
            $('th:first-child, thead td:first-child', this).each(function(){
            var tag = $(this).prop('tagName');
            $(this).before('<'+tag+'>#</'+tag+'>');
            });
            $('td:first-child', this).each(function(i){
            $(this).before('<td>'+(i+1)+'</td>');
            });
        });
    }
  
    // $('#sender_id').select2({
    //         placeholder: "Choose Sender",
    //         minimumInputLength: 2,
    //         ajax: {
    //             url: "{{ url('/admin/branch-franchisee') }}",
    //             dataType: 'json',
    //             data: function (params) {
    //                 return {
    //                     q: $.trim(params.term)
    //                 };
    //             },
    //             processResults: function (data) {
    //                 return {
    //                     results: data
    //                 };
    //             },
    //             cache: true
    //         }
    // });

    // $('#receiver_id_select').select2({
    //         placeholder: "Choose Receiver",
    //         minimumInputLength: 2,
    //         ajax: {
    //             url: "{{ url('/admin/branch-franchisee') }}",
    //             dataType: 'json',
    //             data: function (params) {
    //                 return {
    //                     q: $.trim(params.term)
    //                 };
    //             },
    //             processResults: function (data) {
    //                 return {
    //                     results: data
    //                 };
    //             },
    //             cache: true
    //         }
    // });

    </script>

@endsection
