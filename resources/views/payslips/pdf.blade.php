<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $employee->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .status {
            text-align: right;
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            background-color: {{ $payslip->payment_status === 'paid' ? '#28a745' : '#ffc107' }};
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .section {
            margin-bottom: 20px;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .net-pay {
            background-color: #cfe2ff;
            font-size: 1.1em;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid black;
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Company Name</h2>
        <p>123 Company Street, City, Country</p>
    </div>

    <div class="status">
        <div class="status-badge">
            {{ ucfirst($payslip->payment_status) }}
        </div>
    </div>

    <div class="section">
        <h3>Employee Information</h3>
        <table>
            <tr>
                <td width="30%">Name:</td>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <td>Position:</td>
                <td>{{ $employee->position->name }}</td>
            </tr>
            <tr>
                <td>Employee ID:</td>
                <td>{{ $employee->id }}</td>
            </tr>
            <tr>
                <td>Pay Period:</td>
                <td>{{ str_replace('_to_', ' to ', $payslip->pay_period) }}</td>
            </tr>
            <tr>
                <td>Hours Worked:</td>
                <td>{{ $payslip->hours_worked }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Earnings</h3>
        <table>
            <tr>
                <td>Basic Pay</td>
                <td align="right">₱{{ number_format($payslip->basic_pay, 2) }}</td>
            </tr>
            <tr>
                <td>Overtime Pay</td>
                <td align="right">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Earnings</td>
                <td align="right">₱{{ number_format($payslip->basic_pay + $payslip->overtime_pay, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Deductions</h3>
        <table>
            <tr>
                <td>Contributions</td>
                <td align="right">₱{{ number_format($payslip->total_deductions - $payslip->loan_deductions, 2) }}</td>
            </tr>
            <tr>
                <td>Loan Deductions</td>
                <td align="right">₱{{ number_format($payslip->loan_deductions, 2) }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td align="right">₱{{ number_format($payslip->tax, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Deductions</td>
                <td align="right">₱{{ number_format($payslip->total_deductions + $payslip->tax, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr class="net-pay">
                <td><strong>Net Pay</strong></td>
                <td align="right"><strong>₱{{ number_format($payslip->net_salary, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Payment Details</h3>
        <table>
            <tr>
                <td width="30%">Payment Method:</td>
                <td>
                    @if($employee->payment_method === 'bank')
                        Bank Transfer<br>
                        Bank: {{ $employee->bank_name }}<br>
                        Account Number: {{ substr($employee->bank_acct, 0, -4) }}****
                    @else
                        Cash
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <div class="signature-line">
            <p>Prepared by</p>
        </div>
        <div class="signature-line">
            <p>Received by</p>
        </div>
    </div>
</body>
</html> 