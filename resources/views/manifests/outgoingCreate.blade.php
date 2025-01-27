@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="sb-page-header-title"><span>Outgoing Manifest</span></h1>
                    <p>Scan or manually enter Consignment Numbers (CN) for outgoing manifests below.</p>
                </div>
                <a class="btn btn-primary" href="{{ route('out-manifest-import') }}">Upload OM</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Form Start -->
                <form action="{{ route('manifests.outgoing_store') }}" method="POST" id="outgoing_manifest">
                    @csrf
                    <div class="form-group">
                        <label for="manifest_number">Scan or Enter CN <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('manifest_number') is-invalid @enderror"
                            id="manifest_number"
                            name="manifest_number"
                            placeholder="Enter or Scan Consignment Number"
                            autofocus>
                        <div id="errorMsgExistManifest" class="text-danger mt-2"></div>
                        @error('manifest_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <!-- Scanned CN List -->
                    <div class="scanned_consignments mt-4" style="display: none;">
                        <h5>Scanned CNs (<span id="scan_count">0</span>)</h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Consignment Number</th>
                                <th>Sender Branch</th>
                                <th>Receiver Branch</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="buildyourform"></tbody>
                        </table>
                    </div>

                    <!-- Remarks Section -->
                    <div class="form-group mt-3">
                        <label for="remarks">Manifest Status / Remarks</label>
                        <input
                            type="text"
                            class="form-control"
                            name="remarks"
                            id="remarks"
                            placeholder="Add remarks (optional)">
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" id="sender_id" name="sender_id" value="{{ $loggedOffice->code }}" />
                    <input type="hidden" id="receiver_id_text" name="receiver_id" value="" />
                    <input type="hidden" id="manifest_type" name="manifest_type" value="O" />

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary sbt-btn mt-3" id="submit_button" disabled>Submit</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .scanned_consignments {
            margin-top: 20px;
        }

        .table {
            margin-top: 10px;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        #errorMsgExistManifest {
            font-size: 14px;
            font-weight: bold;
            color: red;
        }

        .btn-danger {
            padding: 0.4em 0.6em;
            font-size: 14px;
        }

        #manifest_number {
            border: 2px solid #ced4da;
            padding: 10px;
            font-size: 16px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            const $inputField = $('#manifest_number');
            const $errorMsg = $('#errorMsgExistManifest');
            const $tableBody = $('#buildyourform');
            const $scanCount = $('#scan_count');
            const $submitButton = $('#submit_button');

            $inputField.on('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    const manifestNumber = $inputField.val().trim();
                    if (!manifestNumber) return;

                    $inputField.val('');
                    $inputField.focus();

                    let isDuplicate = false;
                    $tableBody.find('tr').each(function () {
                        if ($(this).find('.manifest_number').val() === manifestNumber) {
                            isDuplicate = true;
                            return false;
                        }
                    });

                    if (isDuplicate) {
                        $errorMsg.text(`Duplicate Entry: CN "${manifestNumber}" is already scanned.`);
                        return;
                    }

                    $.ajax({
                        url: "{{ url('/admin/booking-details') }}",
                        method: 'get',
                        data: {
                            manifest_number: manifestNumber,
                            manifest_type: 'O',
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function (response) {
                            if (response.status === 'failed') {
                                $errorMsg.text(response.message);
                            } else {
                                $('#sender_id').val(response.origin_branch_code); // Set sender ID
                                $('#receiver_id_text').val(response.dest_branch_code); // Set receiver ID

                                addRow(manifestNumber, response.origin_branch_code, response.dest_branch_code);
                            }
                        },
                        error: function () {
                            $errorMsg.text('Error validating CN. Try again.');
                        },
                    });
                }
            });

            function addRow(manifestNumber, originBranch, destBranch) {
                const rowCount = $tableBody.find('tr').length + 1;
                const row = `
                    <tr>
                        <td>${rowCount}</td>
                        <td>
                            <input type="hidden" class="manifest_number" name="manifest_number[]" value="${manifestNumber}">
                            ${manifestNumber}
                        </td>
                        <td>${originBranch || 'N/A'}</td>
                        <td>${destBranch || 'N/A'}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">Delete</button>
                        </td>
                    </tr>
                `;
                $tableBody.append(row);
                $scanCount.text(rowCount);
                $errorMsg.text('');
                $submitButton.prop('disabled', false);
                $('.scanned_consignments').show();
            }

            $tableBody.on('click', '.remove-row', function () {
                $(this).closest('tr').remove();

                $tableBody.find('tr').each(function (index) {
                    $(this).find('td:first-child').text(index + 1);
                });

                const rowCount = $tableBody.find('tr').length;
                $scanCount.text(rowCount);

                if (rowCount === 0) {
                    $('.scanned_consignments').hide();
                    $submitButton.prop('disabled', true);
                }
            });
        });
    </script>
@endsection
