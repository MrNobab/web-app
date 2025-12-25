<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #000;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a3c7a; /* Dark Blue from logo */
            text-transform: uppercase;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .title-bar {
            text-align: center;
            font-weight: bold;
            font-style: italic;
            margin: 10px 0;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 100px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .products-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }
        .products-table td {
            border: 1px solid #000;
            padding: 5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .totals-section {
            width: 100%;
            margin-top: 5px;
        }
        .total-row td {
            padding: 3px 5px;
            font-weight: bold;
        }
        .summary-box {
            border: 1px solid #000;
            width: 40%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .summary-box td {
            border: 1px solid #000;
            padding: 3px 5px;
        }
        .warranty-section {
            margin-top: 20px;
            font-size: 10px;
        }
        .footer-signatures {
            width: 100%;
            margin-top: 60px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td width="40%">
                <!-- Dynamic Store Logo -->
                @if($invoice->store->logo_path)
                    <img src="{{ public_path('storage/' . $invoice->store->logo_path) }}" class="logo">
                @else
                    <!-- Fallback if no logo -->
                    <h1>{{ $invoice->store->name }}</h1>
                @endif
            </td>
            <td width="60%" class="text-right">
                <div class="company-name">{{ $invoice->store->name }}</div>
                <div>{{ $invoice->store->address }}</div>
                <div>{{ $invoice->store->city }} {{ $invoice->store->zip_code }}</div>
                <div>E-mail: {{ $invoice->store->email }}</div>
                <div>Mobile: {{ $invoice->store->phone }}</div>
            </td>
        </tr>
    </table>

    <div class="title-bar">Sales Invoice</div>

    <!-- Info Section (Invoice Details) -->
    <table class="info-table">
        <tr>
            <!-- Left Column -->
            <td width="55%">
                <table>
                    <tr>
                        <td class="label">Invoice No.</td>
                        <td>: {{ $invoice->id }}</td> <!-- Or use a custom invoice number column -->
                    </tr>
                    <tr>
                        <td class="label">Sold to</td>
                        <td>: {{ $invoice->customer->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Address</td>
                        <td>: {{ $invoice->customer->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Phone No</td>
                        <td>: {{ $invoice->customer->phone }}</td>
                    </tr>
                </table>
            </td>
            <!-- Right Column -->
            <td width="45%">
                <table>
                    <tr>
                        <td class="label">Store Location</td>
                        <td>: Showroom</td>
                    </tr>
                    <tr>
                        <td class="label">Date</td>
                        <td>: {{ $invoice->date }}</td>
                    </tr>
                    <tr>
                        <td class="label">Sales Person</td>
                        <td>: Retail</td> 
                    </tr>
                    <tr>
                        <td class="label">Entry By</td>
                        <td>: Admin</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Products Table -->
    <table class="products-table">
        <thead>
            <tr>
                <th width="8%">Sl. No.</th>
                <th>Product Description</th>
                <th width="10%">Quantity</th>
                <th width="15%">Unit Price</th>
                <th width="15%">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <span class="font-bold">{{ $item->product->name }}</span><br>
                    <!-- Mockup details - Add these fields to Product model if you want them dynamic -->
                    <span style="font-size: 10px;">
                        SN: {{ $item->product->id }}<br> <!-- Using ID as Serial for now -->
                        Warranty: 365 Days
                    </span>
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->row_total, 2) }}</td>
            </tr>
            @endforeach
            <!-- Empty rows to fill space if needed -->
        </tbody>
    </table>

    <!-- Totals Section -->
    <table width="100%">
        <tr>
            <td width="60%"></td>
            <td width="40%">
                <table width="100%" style="margin-top: 5px; border-top: 1px solid #000;">
                    <tr class="total-row">
                        <td class="text-right">Total Amount</td>
                        <td class="text-right">{{ number_format($invoice->subtotal, 0) }}</td>
                    </tr>
                    <tr class="total-row" style="border-bottom: 1px solid #000;">
                        <td class="text-right">Discount</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr class="total-row">
                        <td class="text-right">Total Amount</td>
                        <td class="text-right">{{ number_format($invoice->total, 0) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- In Words & Summary -->
    <div style="margin-top: 10px;">
        <strong>IN WORD:</strong> {{ $amountInWords }}
    </div>

    <table class="summary-box">
        <tr>
            <td>Previous Due</td>
            <td class="text-right">0</td>
        </tr>
        <tr>
            <td>Sale Amount</td>
            <td class="text-right">{{ number_format($invoice->total, 0) }}</td>
        </tr>
        <tr>
            <td>Collection</td>
            <td class="text-right">{{ number_format($invoice->total, 0) }}</td>
        </tr>
        <tr>
            <td>Current Due</td>
            <td class="text-right">0</td>
        </tr>
    </table>

    <!-- Warranty Policy -->
    <div class="warranty-section">
        <strong>Warranty Policy-</strong>
        <ul style="padding-left: 15px; margin-top: 5px;">
            <li>Warranty will be void if there is any physical damage, Burn issue & Liquide damage.</li>
            <li>The product or warranty sticker is removed and sold goods are not refundable.</li>
            <li>Please keep the box and cash memo for warranty purpose.</li>
        </ul>
    </div>

    <!-- Signatures -->
    <table class="footer-signatures">
        <tr>
            <td width="50%">
                <div class="signature-line">Customer Signature</div>
            </td>
            <td width="50%" class="text-right">
                <div class="signature-line" style="float: right;">Authorized Signature & Company Stamp</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer-note">
        Thank you for shopping with us.<br>
        Software Developed & Maintained by <strong>YourSaaSName</strong>
    </div>

</body>
</html>