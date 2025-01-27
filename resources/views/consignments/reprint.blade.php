<html>
<body onload="window.print()">
<div class="container">
    <div class="row">
        <div class="no-break">
            @php
                // Create an instance of the barcode generator
                $barcodeGenerator = new \Milon\Barcode\DNS1D();
            @endphp
            @foreach($consignments as $consignment)
                <div class="column">
                    <img
                        src="data:image/png;base64, {{ $barcodeGenerator->getBarcodePNG($consignment->consg_number, 'C128', 1, 50, array(1, 1, 1), true) }}"
                        alt="barcode"
                        class="barcodeImage"
                    />
                </div>
            @endforeach
        </div>
    </div>
    <button onclick="window.print()" class="btn btn-success noPrint">Print</button>
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
    }

    .column {
        float: left;
        width: 25%;
        height: 48px;
        padding-top: 15px;
        padding-bottom: 11px;
        margin-bottom: 8px;
        text-align: center;
        margin-top: 10px;
    }

    /* Clearfix (clear floats) */
    .row::after {
        content: "";
        clear: both;
        display: table;
    }

    .barcodeImage {
        display: block;
        max-width: 100%;
    }

    .no-break {
        page-break-inside: avoid;
    }
</style>
</html>
