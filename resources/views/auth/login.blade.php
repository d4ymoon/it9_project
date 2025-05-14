<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
        }
        .btn-login {
            width: 100%;
        }
        .btn-attendance {
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="card-title">Welcome Back Admin</h3>
                    <p class="text-muted">Please sign in to continue</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ $errors->first() }}</strong>
                    </div>
                @endif

                <form method="POST" action="/login">
                    @csrf
                    <div class="mb-3">
                        <input name="login" 
                               type="text" 
                               class="form-control" 
                               placeholder="Email or Employee ID" 
                               required 
                               autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <input name="password" 
                               type="password" 
                               class="form-control" 
                               placeholder="Password" 
                               required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login mb-2">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
