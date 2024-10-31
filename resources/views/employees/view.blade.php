@extends('layouts.app')




@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />


<div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h4>Employee Details </h4>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('employees.index') }}"> View Employees</a>
                </div>
            </div>

        </div>

    <div class="profile-card">
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-6">
                        <b>{{ __(' Name') }} </b> -
                        @if(isset($employee)) {{ $employee->first_name}} {{ $employee->last_name }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Email') }} </b> -
                        {{ $employee->email }}
                    </div>
                    <div class="col-6">
                        <b>{{ __('Office Name') }} </b> -
                        @if($employee->office_type == 'BR' || $employee->office_type == 'HO')
                            @if(isset($branches->branch_name))
                                {{ $branches->branch_name }}
                            @endif
                        @endif
                        @if($employee->office_type == 'FR')
                            {{ $branches->enterprise_name }}
                        @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Office Type') }} </b> -
                        @if($employee->office_type == 'HO')
                            Head Office
                        @endif
                        @if($employee->office_type == 'BR')
                            Branch Office
                        @endif
                        @if($employee->office_type == 'FR')
                            Partner
                        @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Mobile Number') }} </b> -
                        @if(isset($employee)) {{ $employee->mobile_number }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Office Code') }} </b> -
                        @if($employee->office_type == 'BR' || $employee->office_type == 'HO')
                            {{ $employee->branch->code }}
                        @endif
                        @if($employee->office_type == 'FR')
                            {{ $employee->franchisee->code }}
                        @endif
                    </div>
                    <div class="col-6">
                        @if(isset($employee->phone_number))
                            <b>{{ __(' Landline Number') }} </b> -
                            {{ $employee->phone_number }} @endif
                    </div>

                </div>
            </div>
            <div class="col-md-3">
                 <div class="profile-image" id="pop">
                    <img id="imageresource" src="{{ asset('storage/uploads/employees/photo/'.$employee->avatar) }}" alt=""   />
                 </div>
            </div>
        </div>
    </div>






    <div class="bank-card">
        <h4> Bank Details </h4>
        <div class="row">
            <div class="col">
                     <b>{{ __(' Current Bank Name') }} </b> -
                    @if(isset($employee)) {{ $employee->current_bank_name }} @endif
            </div>
            <div class="col">
                    <b>{{ __(' Branch Name') }} </b> -
                    @if(isset($employee)) {{ $employee->branch_name }} @endif
            </div>




           <div class="col">
                    <b>{{ __(' Account Number') }} </b> -
                    @if(isset($employee)) {{ $employee->account_number }} @endif
            </div>
            <div class="col">
                     <b>{{ __(' IFSC CODE') }} </b> -
                    @if(isset($employee)) {{ $employee->ifsc_code }} @endif
            </div>

        </div>
    </div>

    <div class="bank-card" style="background: none">
        <h4>ID Proof </h4>
        <div class="row">
            <div class="col-md-4">
                <div class="proof" id="pop1">
                    <img id="imageresource1" src="{{ asset('storage/uploads/employees/idproof/'.$employee->doc_proof) }}" alt=""  width="100" />
                </div>
            </div>
        </div>
    </div>

</div>

{{--        <br>--}}
{{--        @if(isset($employee->email))--}}
{{--        <div class="row">--}}
{{--            <div class="col-md-4">--}}

{{--            </div>--}}

{{--        </div>--}}
{{--        @endif--}}



        <!-- Creates the bootstrap modal where the image will appear -->
        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        </div>
                        <div class="modal-body">
                        <img src="" id="imagepreview" style="width: 400px; " >
                        </div>

                </div>
            </div>
        </div>



</div>

<script type="text/javascript">
$(document).ready(function() {
                $("#pop").on("click", function() {
                   $('#imagepreview').attr('src', $('#imageresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
                   $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
                });
                $("#pop1").on("click", function() {
                   $('#imagepreview').attr('src', $('#imageresource1').attr('src')); // here asign the image to the modal when the user click the enlarge link
                   $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
                });
});

</script>
<style>
.modal-body {
  padding:3rem;
}
</style>
@endsection
