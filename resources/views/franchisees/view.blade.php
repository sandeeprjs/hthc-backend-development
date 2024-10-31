@extends('layouts.app')




@section('content')


<div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h4>Partner Details</h4>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('franchisees.index') }}"> View Partners</a>
                </div>

            </div>

        </div>

    <div class="profile-card">
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-6">
                        <b>{{ __(' Partner Type') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->franchisee_type == 'BOOKING' ? 'BOOKING': ( $franchisee->franchisee_type == 'DELIVERY' ? 'Delivery': ( $franchisee->franchisee_type == 'BOTH' ? 'Both' : '') )}} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Partner Code') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->code }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Partner Name') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->enterprise_name }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Branch Code') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->branch->code }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Branch Name') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->branch->branch_name }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Contact Person') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->contact_person_name }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Origin Pincode') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->pincode->pincode }} @endif
                    </div>

                    <div class="col-6">
                        <b>{{ __(' Address') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->add_line_1 }} @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Mobile Number') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->mobile_number }} @endif
                    </div>
                    <div class="col-6">
                        @if(isset($franchisee->phone_number))
                            <b>{{ __(' Landline Number') }} </b> -
                            {{ $franchisee->phone_number }} @endif
                    </div>

                    <div class="col-6">
                        <b>{{ __(' Serviceable Pincode') }} </b> -
                        @foreach($franchisee->serviceablePins as $serviceablePin)
                            {{ $serviceablePin->pincodes->pincode }},
                        @endforeach
                    </div>



                </div>

            </div>
            <div class="col-md-3">
                <div class="profile-image" id="pop"  >
                    <img id="imageresource" src="{{ asset('storage/uploads/partners/photo/'.$franchisee->avatar) }}" alt="" />
                </div>
            </div>

        </div>
    </div>



            <div class="bank-card">
                <h4> Bank Details </h4>
                <div class="row">
                    <div class="col">
                        <b>{{ __(' Current Bank Name') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->current_bank_name }} @endif
                    </div>
                    <div class="col">
                        <b>{{ __(' Branch Name') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->branch_name }} @endif
                    </div>


                    <div class="col">
                        <b>{{ __(' Account Number') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->contact_person_name }} @endif
                    </div>
                    <div class="col">
                        <b>{{ __(' IFSC CODE') }} </b> -
                        @if(isset($franchisee)) {{ $franchisee->ifsc_code }} @endif
                    </div>

                </div>

            </div>


    <div class="bank-card" style="background: none">
        <h4>ID Proof </h4>
        <div class="row">
            <div class="col-md-4">
                <div class="proof" id="pop1"  >
                    <img id="imageresource1" src="{{ asset('storage/uploads/partners/idproof/'.$franchisee->doc_proof) }}" alt=""  />
                </div>
            </div>
        </div>
    </div>






        <br>
        @if(isset($franchisee->email))
        <div class="row">
            <div class="col-md-4">
                    <b>{{ __(' Email') }} </b> -
                    {{ $franchisee->email }}
            </div>

        </div>
        @endif

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
});
</script>
<style>
.modal-body {
  padding:3rem;
}
</style>
@endsection
