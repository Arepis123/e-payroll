<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 40px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: normal;
            color: #000;
            margin-bottom: 60px;
        }
        .logo-section {
            text-align: right;
            margin-bottom: 20px;
        }
        .logo-section img {
            max-width: 150px;
            height: auto;
        }
        .company-info {
            font-size: 10px;
            color: #666;
            margin-bottom: 30px;
        }
        .bill-to-section {
            margin-bottom: 30px;
        }
        .bill-to-label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .bill-to-content {
            font-size: 11px;
            line-height: 1.6;
        }
        .invoice-details {
            text-align: right;
            font-size: 11px;
            line-height: 1.8;
        }
        .invoice-details-label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0 20px 0;
        }
        .items-table thead {
            border-bottom: 1px solid #000;
        }
        .items-table th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 12px 5px;
            font-size: 11px;
            vertical-align: top;
            border-bottom: 1px solid #e0e0e0;
        }
        .items-table .worker-name {
            width: 20%;
        }
        .items-table .basic-salary,
        .items-table .gross-salary,
        .items-table .deductions,
        .items-table .net-salary,
        .items-table .employer-contrib,
        .items-table .total-cost {
            width: 13.33%;
            text-align: right;
        }
        .description-main {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .description-sub {
            font-size: 9px;
            color: #666;
        }
        .totals-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            margin-bottom: 8px;
            font-size: 11px;
        }
        .total-label {
            display: inline-block;
            width: 120px;
            text-align: right;
            padding-right: 20px;
        }
        .total-value {
            display: inline-block;
            width: 120px;
            text-align: right;
        }
        .grand-total {
            background-color: #000;
            color: #fff;
            padding: 12px 20px;
            margin-top: 5px;
            display: inline-block;
            min-width: 260px;
        }
        .grand-total .total-label {
            font-weight: bold;
            text-transform: uppercase;
        }
        .grand-total .total-value {
            font-weight: bold;
            font-size: 13px;
        }
        .signature-section {
            margin-top: 40px;
            text-align: right;
        }
        .signature-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 30px;
        }
        .signature-line {
            font-family: 'Brush Script MT', cursive;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
        .penalty-notice {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin: 20px 0;
            font-size: 10px;
        }
        .payment-status {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 10px 15px;
            margin: 20px 0;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header with Logo -->
        <div class="header">
            <div class="invoice-title">INVOICE</div>
            <div class="logo-section">
                <img src="{{ public_path('images/company-logo.png') }}" alt="Company Logo">
            </div>
        </div>

        <!-- Company Info -->
        <div class="company-info">
            {{ config('app.name') }}<br>
            E-Payroll Management System
        </div>

        <!-- Bill To and Invoice Details -->
        <table style="width: 100%; margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="bill-to-section">
                        <div class="bill-to-label">BILL TO:</div>
                        <div class="bill-to-content">
                            <strong>{{ $contractor->company_name ?? $contractor->name }}</strong><br>
                            CLAB No: {{ $contractor->contractor_clab_no }}<br>
                            {{ $contractor->email }}
                        </div>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="invoice-details">
                        <div><span class="invoice-details-label">Invoice No:</span> INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <div><span class="invoice-details-label">Issue date:</span> {{ $invoice->submitted_at ? $invoice->submitted_at->format('d/m/Y') : now()->format('d/m/Y') }}</div>
                        <div><span class="invoice-details-label">Due date:</span> {{ $invoice->payment_deadline->format('d/m/Y') }}</div>
                        <div><span class="invoice-details-label">Period:</span> {{ $invoice->month_year }}</div>
                        <div><span class="invoice-details-label">Reference:</span> {{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="worker-name">WORKER</th>
                    <th class="basic-salary">BASIC SALARY (RM)</th>
                    <th class="gross-salary">GROSS SALARY (RM)</th>
                    <th class="deductions">DEDUCTIONS (RM)</th>
                    <th class="net-salary">NET SALARY (RM)</th>
                    <th class="employer-contrib">EMPLOYER CONTRIB (RM)</th>
                    <th class="total-cost">TOTAL COST (RM)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->workers as $worker)
                <tr>
                    <td class="worker-name">
                        <div class="description-main">{{ $worker->worker_name }}</div>
                        <div class="description-sub">
                            ID: {{ $worker->worker_id }}<br>
                            Passport: {{ $worker->worker_passport }}
                        </div>
                    </td>
                    <td class="basic-salary">{{ number_format($worker->basic_salary, 2) }}</td>
                    <td class="gross-salary">{{ number_format($worker->gross_salary, 2) }}</td>
                    <td class="deductions">{{ number_format($worker->total_deductions, 2) }}</td>
                    <td class="net-salary">{{ number_format($worker->net_salary, 2) }}</td>
                    <td class="employer-contrib">{{ number_format($worker->total_employer_contribution, 2) }}</td>
                    <td class="total-cost">{{ number_format($worker->total_payment, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Penalty Notice -->
        @if($invoice->has_penalty)
        <div class="penalty-notice">
            <strong>LATE PAYMENT PENALTY APPLIED:</strong> This invoice is overdue. An 8% penalty has been added to the total amount.
        </div>
        @endif

        <!-- Payment Status -->
        @if($invoice->status === 'paid')
        <div class="payment-status">
            <strong>PAYMENT RECEIVED:</strong> This invoice was paid on {{ $invoice->payment->completed_at?->format('d/m/Y H:i') }}.
            @if($invoice->payment->transaction_id)
                Transaction ID: {{ $invoice->payment->transaction_id }}
            @endif
        </div>
        @endif

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span class="total-label">TOTAL (RM):</span>
                <span class="total-value">{{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            @if($invoice->has_penalty)
            <div class="total-row" style="color: #dc3545;">
                <span class="total-label">PENALTY 8% (RM):</span>
                <span class="total-value">+{{ number_format($invoice->penalty_amount, 2) }}</span>
            </div>
            @endif
            <div class="grand-total">
                <span class="total-label">TOTAL DUE (RM):</span>
                <span class="total-value">{{ number_format($invoice->has_penalty ? $invoice->total_with_penalty : $invoice->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-label">Issued by, signature:</div>
            <div class="signature-line">{{ config('app.name') }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>{{ config('app.name') }}</strong> | E-Payroll Management System | Generated: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
