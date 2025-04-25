<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Digital7';
            src: url('{{ asset('fonts/DS-DIGI.TTF') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
    
        #clock {
            font-family: 'Digital7', monospace;
            font-size: 5rem;
            color: #ff0000; 
            text-align: center;
            background-color: #000;
            padding: 10px 20px;
            border-radius: 10px;
            letter-spacing: 4px;
            width: fit-content;
            margin: 0 auto 20px auto;
        }

        #date {
            font-size: 24px;
            color: #333;
        }
        
        #shift-display {
            font-size: 24px;
            color: #333;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Employee Attendance Tracker</h3>   
        <div class="container mt-5" style="width: 500px;">
            <div id="clock" class="text-center"></div>
            <div id="date" class="text-center mb-1"></div>
            <div id="shift-display" class="text-center"></div>

            @error('employee_id')
                <div class="error alert alert-danger mx-5">{{ $message }}</div>
            @enderror

            @if(session('success'))
                <div class="alert alert-success mx-5">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mx-5">{{ session('error') }}</div>
            @endif
        </div>
       
        <form method="POST" action="{{ route('attendance.store') }}" style="width: 400px;" class="mx-auto mt-3 h2">
            @csrf
            <input type="hidden" id="shift" name="shift" value="">
            <div class="mb-3">
                <label for="employee_id" class="form-label h5">Employee ID:</label>
                <input type="number" class="form-control" id="employee_id" name="employee_id" required placeholder="Enter your ID" min="1">
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            
            // Determine the current shift
            let shift;
            if ((hours >= 0 && hours < 12) || (hours === 12 && minutes === 0)) {
                shift = 'morning';
            } else {
                shift = 'afternoon';
            }
            
            // Display the current shift
            document.getElementById('shift-display').innerText = `Current Shift: ${shift.toUpperCase()}`;
            document.getElementById('shift').value = shift;
            
            // Clock display
            const time = now.toLocaleTimeString();
            document.getElementById('clock').innerText = time;

            // Date display
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('date').innerText = dateString;
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>