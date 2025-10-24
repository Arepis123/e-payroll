<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Submission Reminder</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 15px;
        }
        .contractor-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .contractor-info p {
            margin: 5px 0;
            font-size: 14px;            
            color: #000;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 5px 0;
            color: #856404;
        }
        .message-content {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            line-height: 1.2;
            color: #000;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        a.btn {
            display: inline-block;
            padding: 12px 30px;
            background: #138B85;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 10px 0;
        }

        a.btn:link,
        a.btn:visited,
        a.btn:hover,
        a.btn:active {
            color: #ffffff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Payroll Submission Reminder</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="contractor-info">
                <p><strong>Contractor:</strong> {{ $contractorName }}</p>
                <p><strong>CLAB No:</strong> {{ $contractorClabNo }}</p>
            </div>

            <div class="warning-box">
                <p><strong>Pending Submission</strong></p>
                <p>{{ $pendingWorkers }} of {{ $totalWorkers }} workers not submitted for {{ $periodMonth }}</p>
            </div>

            <div class="message-content">
                {!! nl2br(e($reminderMessage)) !!}
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/client/timesheet') }}" class="btn">
                    Submit Payroll Now
                </a>
            </div>

            <p style="font-size: 12px; color: #6c757d; margin-top: 20px;">
                If you have already submitted the payroll, please disregard this message. For any questions or assistance, please contact the CLAB administration.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>e-Salary CLAB System</strong></p>
            <p>This is an automated reminder. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} e-Salary CLAB. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
