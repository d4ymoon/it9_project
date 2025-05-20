<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
<body style="background-color: #f8f9fa">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Employee Attendance Tracker</h2>   
        <div class="container mt-5" style="width: 500px;">
            <div id="clock" class="text-center"></div>
            <div id="date" class="text-center mb-1"></div>
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
       
        <form method="POST" action="{{ route('attendances.store') }}" style="width: 500px;" class="mx-auto mt-3">
            @csrf
            <input type="hidden" name="date" id="current_date" value="{{ now()->format('Y-m-d') }}">
            <input type="hidden" name="time_in" id="time_in">
            <input type="hidden" name="break_out" id="break_out">
            <input type="hidden" name="break_in" id="break_in">
            <input type="hidden" name="time_out" id="time_out">
            <input type="hidden" name="type" id="attendance_type">

            <div class="mb-3">
                <label for="employee_id" class="form-label h5">Employee ID:</label>
                <input type="number" class="form-control" id="employee_id" name="employee_id" required placeholder="Enter your ID" min="1" value="{{ old('employee_id') }}">
                @error('employee_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="row text-center g-2">
                <div class="col-3">
                    <button type="button" class="btn btn-success w-100" onclick="setAttendanceType('time_in')" id="time_in_btn">
                        <i class="bi bi-box-arrow-in-right"></i><br>Time In
                    </button>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-warning w-100" onclick="setAttendanceType('break_out')" id="break_out_btn">
                        <i class="bi bi-cup-hot-fill"></i><br>Break Out
                    </button>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-info w-100" onclick="setAttendanceType('break_in')" id="break_in_btn">
                        <i class="bi bi-arrow-bar-left"></i><br>Break In
                    </button>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-danger w-100" onclick="setAttendanceType('time_out')" id="time_out_btn">
                        <i class="bi bi-box-arrow-right"></i><br>Time Out
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            
            // Update clock display
            document.getElementById('clock').innerText = `${hours}:${minutes}:${seconds}`;
            
            // Update date display
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('date').innerText = dateString;
        }

        function setAttendanceType(type) {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour12: false });
            
            // Set the appropriate time field based on type
            document.getElementById(type).value = timeString;
            document.getElementById('attendance_type').value = type;
            
            // Disable the button that was just clicked
            document.getElementById(type + '_btn').disabled = true;
            
            // Submit the form
            document.querySelector('form').submit();
        }

        // Check if there are any error messages
        window.onload = function() {
            const errorDiv = document.querySelector('.alert-danger');
            if (errorDiv) {
                // If there's an error, re-enable the button
                const type = document.getElementById('attendance_type').value;
                if (type) {
                    document.getElementById(type + '_btn').disabled = false;
                }
            }
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>