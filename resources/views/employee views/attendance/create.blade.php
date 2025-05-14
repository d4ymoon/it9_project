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

        .attendance-info {
            font-size: 18px;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Employee Attendance Tracker</h2>   
        <div class="container mt-5" style="width: 500px;">
            <div id="clock" class="text-center"></div>
            <div id="date" class="text-center mb-1"></div>
            <div id="shift-display" class="text-center"></div>
            <div id="attendance-phase" class="text-center attendance-info"></div>

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
       
        <form method="POST" action="{{ route('attendances.store') }}" style="width: 400px;" class="mx-auto mt-3 h2">
            @csrf
            <input type="hidden" id="current_time" name="current_time" value="">
            <input type="hidden" id="shift" name="shift" value="">
            <div class="mb-3">
                <label for="employee_id" class="form-label h5">Employee ID:</label>
                <input type="number" class="form-control" id="employee_id" name="employee_id" required placeholder="Enter your ID" min="1" value="{{ old('employee_id') }}">
                @error('employee_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Log Attendance</button>
        </form>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            
            // Format current time for the hidden input
            const timeString = now.toLocaleTimeString('en-US', { hour12: false });
            document.getElementById('current_time').value = timeString;
            
            // Determine the current phase of attendance
            let attendancePhase = '';
            if (hours < 8) {
                attendancePhase = 'Before Shift (First Half starts at 8:00 AM)';
            } else if (hours < 12) {
                attendancePhase = 'First Half of Shift (8:00 AM - 12:00 PM)';
            } else if (hours === 12) {
                attendancePhase = 'Break Time (12:00 PM - 1:00 PM)';
            } else if (hours < 17) {
                attendancePhase = 'Second Half of Shift (1:00 PM - 5:00 PM)';
            } else {
                attendancePhase = 'After Shift Hours (Overtime if logged in during shift)';
            }
            document.getElementById('attendance-phase').innerText = attendancePhase;
            
            // Determine the current shift
            let shift;
            if (hours < 12) {
                shift = 'first_half';
            } else {
                shift = 'second_half';
            }
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