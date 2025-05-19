                    @if($payslip->loan_deductions > 0)
                        <tr>
                            <td colspan="2">Loan Deductions</td>
                            <td class="text-end">₱{{ number_format($payslip->loan_deductions, 2) }}</td>
                        </tr>
                        @foreach($payslip->loan_details as $loan)
                            <tr>
                                <td colspan="2" class="ps-4">
                                    {{ $loan['type'] }} Loan
                                    <small class="d-block text-muted">
                                        Principal: ₱{{ number_format($loan['amount'], 2) }} | 
                                        Interest: ₱{{ number_format($loan['interest'], 2) }}
                                    </small>
                                </td>
                                <td class="text-end">₱{{ number_format($loan['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endif 