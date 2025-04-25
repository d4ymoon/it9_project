<form action="{{ route('payrolls1.generate') }}" method="POST">
    @csrf

    <!-- Payroll Frequency -->
    <div class="form-group">
        <label for="pay_frequency">Pay Frequency:</label>
        <select name="pay_frequency" id="pay_frequency" class="form-control" required>
            <option value="monthly" selected>Monthly</option>
            <option value="semi_monthly">Semi-Monthly</option>
        </select>
    </div>

   <!-- Semi-Monthly Option (Month and 1st-15th / 16th-End) -->
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



   <!-- Monthly Option (Just Pick the Month) -->
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

    <button type="submit" class="btn btn-primary mt-3">Generate Payroll</button>
</form>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get current date
        const currentDate = new Date();
        
        // Set current month (0-11, so add 1 to make it 1-12)
        const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0'); // Ensure 2 digits (01, 02, ...)
        
        // Set current year
        const currentYear = currentDate.getFullYear();
        
        // Set the default values for month and year selects
        document.getElementById('pay_month').value = currentMonth;
        document.getElementById('pay_year').value = currentYear;

        // Call update function to show the monthly fields when page loads
        updatePayOptions();
    });

    // Function to toggle options based on selected pay frequency
    function updatePayOptions() {
    const isSemiMonthly = document.getElementById('pay_frequency').value === 'semi_monthly';

    // Toggle visibility
    document.getElementById('semiMonthlyOptions').style.display = isSemiMonthly ? 'block' : 'none';
    document.getElementById('monthlyOptions').style.display = !isSemiMonthly ? 'block' : 'none';

    // Disable/enable relevant inputs
    const monthlyInputs = document.querySelectorAll('#monthlyOptions select');
    const semiMonthlyInputs = document.querySelectorAll('#semiMonthlyOptions select');

    monthlyInputs.forEach(input => {
        input.disabled = isSemiMonthly;
    });

    semiMonthlyInputs.forEach(input => {
        input.disabled = !isSemiMonthly;
    });
}

    // Run when dropdown changes
    document.getElementById('pay_frequency').addEventListener('change', updatePayOptions);
</script>
