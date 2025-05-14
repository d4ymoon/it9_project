<form action="{{ route('payslips.generate') }}" method="POST">
    @csrf

    <!-- Pay Period -->
    <div class="mb-3">
        <label for="period_type" class="form-label">Pay Period Type</label>
        <select class="form-select" name="period_type" id="period_type" required>
            <option value="monthly">Monthly</option>
            <option value="semi_monthly">Semi-Monthly</option>
        </select>
    </div>

    <!-- Semi-Monthly Option -->
    <div class="form-group mt-2" id="semiMonthlyOptions" style="display: none;">
        <label>Select Semi-Monthly Pay Period:</label>
        <div class="d-flex gap-2">
            <select name="pay_month" id="pay_month_semi" class="form-control" required>
                <option value="" disabled selected>Month</option>
                @foreach(range(1, 12) as $month)
                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}">
                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                    </option>
                @endforeach
            </select>

            <select name="pay_year" id="pay_year_semi" class="form-control" required>
                <option value="" disabled selected>Year</option>
                @foreach(range(now()->year, now()->year + 5) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>

            <select name="pay_period_choice" id="pay_period_choice" class="form-control" required>
                <option value="first_half">1st to 15th</option>
                <option value="second_half">16th to End</option>
            </select>
        </div>
    </div>

    <!-- Monthly Option -->
    <div class="form-group mt-2" id="monthlyOptions" style="display: none;">
        <label>Select Month:</label>
        <div class="d-flex gap-2">
            <select name="pay_month" id="pay_month" class="form-control" required>
                <option value="" disabled selected>Month</option>
                @foreach(range(1, 12) as $month)
                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}">
                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                    </option>
                @endforeach
            </select>

            <select name="pay_year" id="pay_year" class="form-control" required>
                <option value="" disabled selected>Year</option>
                @foreach(range(now()->year, now()->year + 5) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Generate Payslips</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current date
    const currentDate = new Date();
    const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
    const currentYear = currentDate.getFullYear();
    
    // Set default values
    document.getElementById('pay_month').value = currentMonth;
    document.getElementById('pay_year').value = currentYear;
    document.getElementById('pay_month_semi').value = currentMonth;
    document.getElementById('pay_year_semi').value = currentYear;

    // Show initial options
    updatePayOptions();
});

function updatePayOptions() {
    const isSemiMonthly = document.getElementById('period_type').value === 'semi_monthly';
    
    document.getElementById('semiMonthlyOptions').style.display = isSemiMonthly ? 'block' : 'none';
    document.getElementById('monthlyOptions').style.display = !isSemiMonthly ? 'block' : 'none';

    const monthlyInputs = document.querySelectorAll('#monthlyOptions select');
    const semiMonthlyInputs = document.querySelectorAll('#semiMonthlyOptions select');

    monthlyInputs.forEach(input => {
        input.disabled = isSemiMonthly;
        if (!isSemiMonthly) input.required = true;
    });

    semiMonthlyInputs.forEach(input => {
        input.disabled = !isSemiMonthly;
        if (isSemiMonthly) input.required = true;
    });
}

document.getElementById('period_type').addEventListener('change', updatePayOptions);
</script> 