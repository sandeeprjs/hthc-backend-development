<html>
    <body onload="window.print()">
        <div class="container">
            <div class="row">
                <div class="no-break">
                @foreach($barCodeImages as $image)
                    <div class="column">
                        <img src="data:image/png;base64,{{ $image }}" alt="barcode"  />
                    </div>
                @endforeach
                </div>
            </div>
            <button onclick="window.print()" class="btn btn-success noprint">Print</button>
        </div>
    </body>
    <style>
        @media print {
            .noprint {
                display: none;
            }
        }

        @page {
            margin: 0cm;
            padding: 0cm;
            padding-top: 20px;
        }

        /*.printLayout {*/
        /*    margin: 10px;*/
        /*}*/
        .column {
            float: left;
            width: 25%;
            height: 60px;
            padding-top: 16px;
            padding-bottom: 16px;
            text-align: center;
        }

        /* Clearfix (clear floats) */
        .row::after {
            content: "";
            clear: both;
            display: table;
        }

        .barcodeImage {
            display: block;
            max-width: 25%;
        }
        .no-break {
            page-break-inside: avoid;
        }

        /*@media screen {*/

        /*}*/
    </style>
</html>
