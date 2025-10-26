<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 20px;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 15px 20px;
        }
        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 10px;
        }
        .invoice-title {
            font-family: 'Inter', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #000;
            margin-bottom: 5px;
        }
        .logo-section {
            text-align: right;
            margin-bottom: 5px;
        }
        .logo-section img {
            max-width: 120px;
            height: auto;
        }
        .company-info {
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
        }
        .bill-to-section {
            margin-bottom: 10px;
        }
        .bill-to-label {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 3px;
        }
        .bill-to-content {
            font-size: 9px;
            line-height: 1.5;
        }
        .invoice-details {
            text-align: right;
            font-size: 9px;
            line-height: 1.6;
        }
        .invoice-details-label {
            display: inline-block;
            width: 90px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 5px 0;
        }
        .items-table thead {
            border-bottom: 1px solid #000;
        }
        .items-table th {
            padding: 4px 3px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 5px 3px;
            font-size: 8px;
            vertical-align: top;
            border-bottom: 1px solid #e0e0e0;
        }
        .items-table .worker-name {
            width: 12%;
        }
        .items-table .basic-salary {
            width: 8%;
            text-align: right;
        }
        .items-table .ot-col {
            width: 8%;
            text-align: right;
        }
        .items-table .transactions {
            width: 12%;
            text-align: right;
        }
        .items-table .gross-salary,
        .items-table .deductions,
        .items-table .net-salary,
        .items-table .total-cost {
            width: 8%;
            text-align: right;
        }
        .description-main {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .description-sub {
            font-size: 7px;
            color: #666;
        }
        .ot-hours {
            font-weight: bold;
        }
        .ot-amount {
            font-size: 7px;
            color: #666;
        }
        .transaction-item {
            font-size: 7px;
            margin-bottom: 2px;
        }
        .advance-payment {
            color: #28a745;
        }
        .deduction {
            color: #dc3545;
        }
        .totals-section {
            margin-top: 8px;
            text-align: right;
        }
        .total-row {
            margin-bottom: 5px;
            font-size: 9px;
        }
        .total-label {
            display: inline-block;
            width: 100px;
            text-align: right;
            padding-right: 15px;
        }
        .total-value {
            display: inline-block;
            width: 100px;
            text-align: right;
        }
        .grand-total {
            background-color: #000;
            color: #fff;
            padding: 8px 15px;
            margin-top: 5px;
            display: inline-block;
            min-width: 220px;
        }
        .grand-total .total-label {
            font-weight: bold;
            text-transform: uppercase;
        }
        .grand-total .total-value {
            font-weight: bold;
            font-size: 11px;
        }
        .signature-section {
            margin-top: 15px;
            text-align: right;
        }
        .signature-label {
            font-size: 8px;
            color: #666;
            margin-bottom: 10px;
        }
        .signature-line {
            font-family: 'Brush Script MT', cursive;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .footer {
            position: fixed;
            bottom: 15px;
            left: 30px;
            right: 30px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
        }
        .penalty-notice {
            background-color: #fef4d2;
            border-left: 4px solid #ffd045;
            padding: 5px 8px;
            margin: 7px 0;
            font-size: 7px;
            border-bottom-right-radius: 1px;
            border-top-right-radius: 1px;
        }
        .payment-status {
            background-color: #d6e9db;
            border-left: 4px solid #43c862;
            padding: 5px 8px;
            margin: 7px 0;
            font-size: 7px;
            border-bottom-right-radius: 1px;
            border-top-right-radius: 1px;
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
            e-Salary Management System
        </div>

        <!-- Invoice Purpose -->
        <div style="background-color: #f5f5f5; padding: 8px 10px; margin-bottom: 10px; border-left: 4px solid #000; font-size: 10px; font-weight: bold;">
            PAYROLL PAYMENT FOR: {{ strtoupper($invoice->month_year) }}
        </div>

        <!-- Bill To and Invoice Details -->
        <table style="width: 100%; margin-bottom: 8px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="bill-to-section">
                        <div class="bill-to-label">Bill To:</div>
                        <div class="bill-to-content">
                            <strong>{{ $contractor ? ($contractor->company_name ?? $contractor->name) : $invoice->contractor_clab_no }}</strong><br>
                            CLAB No: {{ $invoice->contractor_clab_no }}<br>
                            {{ $contractor ? $contractor->email : '' }}
                        </div>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <table style="font-size: 9px; line-height: 1.4; margin-left: auto; display: inline-table;">
                        <tr>
                            <td style="text-align: right; font-weight: bold; padding: 1px 8px 1px 0; white-space: nowrap;">Invoice No:</td>
                            <td style="text-align: left; padding: 1px 0;">INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-weight: bold; padding: 1px 8px 1px 0; white-space: nowrap;">Issue date:</td>
                            <td style="text-align: left; padding: 1px 0;">{{ $invoice->submitted_at ? $invoice->submitted_at->format('d/m/Y') : now()->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-weight: bold; padding: 1px 8px 1px 0; white-space: nowrap;">Due date:</td>
                            <td style="text-align: left; padding: 1px 0;">{{ $invoice->payment_deadline->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-weight: bold; padding: 1px 8px 1px 0; white-space: nowrap;">Period:</td>
                            <td style="text-align: left; padding: 1px 0;">{{ $invoice->month_year }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-weight: bold; padding: 1px 8px 1px 0; white-space: nowrap;">Reference:</td>
                            <td style="text-align: left; padding: 1px 0;">{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="worker-name">WORKER</th>
                    <th class="basic-salary">BASIC<br>SALARY</th>
                    <th class="ot-col">OT<br>NORMAL</th>
                    <th class="ot-col">OT<br>REST</th>
                    <th class="ot-col">OT<br>PUBLIC</th>
                    <th class="transactions">TRANSACTIONS</th>
                    <th class="gross-salary">GROSS<br>SALARY</th>
                    <th class="deductions">DEDUCTIONS<br>(EPF+SOCSO)</th>
                    <th class="net-salary">NET<br>SALARY</th>
                    <th class="total-cost">TOTAL<br>PAYMENT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->workers as $worker)
                <tr>
                    <td class="worker-name">
                        <div class="description-main">{{ $worker->worker_name }}</div>
                        <div class="description-sub">ID: {{ $worker->worker_id }}</div>
                    </td>
                    <td class="basic-salary">{{ number_format($worker->basic_salary, 2) }}</td>
                    <td class="ot-col">
                        <div class="ot-hours">{{ $worker->ot_normal_hours }}h</div>
                        <div class="ot-amount">RM {{ number_format($worker->ot_normal_pay, 2) }}</div>
                    </td>
                    <td class="ot-col">
                        <div class="ot-hours">{{ $worker->ot_rest_hours }}h</div>
                        <div class="ot-amount">RM {{ number_format($worker->ot_rest_pay, 2) }}</div>
                    </td>
                    <td class="ot-col">
                        <div class="ot-hours">{{ $worker->ot_public_hours }}h</div>
                        <div class="ot-amount">RM {{ number_format($worker->ot_public_pay, 2) }}</div>
                    </td>
                    <td class="transactions">
                        @php
                            $workerTransactions = $worker->transactions ?? collect([]);
                            $advancePayments = $workerTransactions->where('type', 'advance_payment');
                            $deductions = $workerTransactions->where('type', 'deduction');
                        @endphp
                        @if($workerTransactions->count() > 0)
                            @if($advancePayments->count() > 0)
                                <div class="advance-payment transaction-item">
                                    <strong>Advance:</strong>
                                    @foreach($advancePayments as $transaction)
                                        <div>RM {{ number_format($transaction->amount, 2) }}</div>
                                    @endforeach
                                </div>
                            @endif
                            @if($deductions->count() > 0)
                                <div class="deduction transaction-item">
                                    <strong>Deduction:</strong>
                                    @foreach($deductions as $transaction)
                                        <div>RM {{ number_format($transaction->amount, 2) }}</div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div style="text-align: center;">-</div>
                        @endif
                    </td>
                    <td class="gross-salary">{{ number_format($worker->gross_salary, 2) }}</td>
                    <td class="deductions">{{ number_format($worker->total_deductions, 2) }}</td>
                    <td class="net-salary">{{ number_format($worker->net_salary, 2) }}</td>
                    <td class="total-cost">
                        <div style="font-weight: bold;">{{ number_format($worker->total_payment, 2) }}</div>
                        <div class="ot-amount">(+{{ number_format($worker->total_employer_contribution, 2) }} contrib.)</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Notices and Totals Side by Side -->
        <table style="width: 100%; margin-top: 8px;">
            <tr>
                <td style="width: 70%; vertical-align: top; padding-right: 15px;">
                    <!-- Important Notice about OT -->
                    <div style="background-color: #ddeafd; border-left: 4px solid #2b80ff; padding: 5px 8px; margin-bottom: 7px; font-size: 7px; border-top-right-radius: 1px; border-bottom-right-radius: 1px;">
                        <strong>IMPORTANT - DEFERRED OT PAYMENT:</strong> The overtime hours shown above are recorded for {{ $invoice->month_year }}, but they will be paid in the following month's payroll. This month's payment includes basic salary plus previous month's overtime.
                    </div>

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
                </td>
                <td style="width: 30%; vertical-align: top;">
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
                </td>
            </tr>
        </table>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-label">Issued by, signature:</div>
            <div class="signature-line">{{ config('app.name') }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>{{ config('app.name') }}</strong> | e-Salary Management System | Generated: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
