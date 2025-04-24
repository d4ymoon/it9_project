<form action="{{ route('payrolls1.generate') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary">Generate Payroll</button>
</form>