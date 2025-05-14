<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style2.css') }}">
</head>
<body>
    <div class="row lolol align-items-center" style="background-color: #880808; margin: 0; padding: 15px;">
        <div class="col-6">
            <h5 class="mb-0"><strong>Payroll System</strong></h5>
        </div>
        <div class="col text-end">
            <div class="dropdown">
                <a class="btn btn-outline dropdown-toggle" style="color:white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0">
                            @csrf
                            <button type="submit" class="btn btn-link text-dark text-decoration-none w-100 text-start px-3">
                                <i class="bi bi-box-arrow-right"></i> Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 