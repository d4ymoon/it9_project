<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shifts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="default-padding theme1">


    <div class="container-fluid">
        <!--- Search, Reset, Add --->
        <div class="row mt-2">
            <form action="" method="GET" class="col-auto d-flex justify-content-start align-items-center">
                <label for="searchInput" class="label me-2">Search:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" name="query" style="width: 190px;">
                    <button class="btn btn-dark"><i class="bi bi-search"></i></button>
                </div>

            </form>
            <div class="col px-0 d-flex align-items-center">

            </div>
            <div class="col-4 d-flex justify-content-end">
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                    data-bs-target="#newShiftmodal">
                    + Add New Shift
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert mt-2 alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert mt-2 alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert mt-2 alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert mt-2 alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mt-2">
            <div class="col">
                <table class="table table-striped table-hover table-bordered ">
                    <thead>
                        <tr>
                            <th>Shift Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Break Start Time</th>
                            <th>Break End Time</th>
                            <th style="width:275px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shifts as $shift)
                            <tr>
                                <td>{{ $shift->name }}</td>
                                <td>{{ $shift->start_time }}</td>
                                <td>{{ $shift->end_time }}</td>
                                <td>{{ $shift->break_start_time ?? 'N/A' }}</td>
                                <td>{{ $shift->break_end_time ?? 'N/A' }}</td>
                                <td class="text-nowrap" style="width:275px">

                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editShiftModal{{ $shift->id }}">
                                        Edit shift
                                    </button>

                                    <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this shift?');"
                                        style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Edit Shift Modal -->
                <div class="modal fade" id="editShiftModal{{ $shift->id }}" tabindex="-1" aria-labelledby="editShiftModalLabel{{ $shift->id }}" aria-hidden="true">
                    <div class="modal-dialog modal modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editShiftModalLabel{{ $shift->id }}">Edit Shift: {{ $shift->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                            
                                <!-- Shift Name -->
                                <div class="row mt-3">
                                    <div class="col">
                                        <label for="name" class="form-label">Shift Name:</label>
                                    </div>
                                    <div class="col">
                                        <input class="form-control" type="text" name="name" id="name" value="{{ $shift->name }}" required>
                                    </div>
                                </div>
                            
                                <!-- Shift Start Time -->
                <div class="row mt-3">
                    <div class="col">
                        <label for="shift_start_time" class="form-label">Shift Start Time:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="time" name="start_time" id="shift_start_time" value="{{ old('start_time', $shift->start_time ?? '') }}" required>
                    </div>
                </div>

                <!-- Shift End Time -->
                <div class="row mt-3">
                    <div class="col">
                        <label for="shift_end_time" class="form-label">Shift End Time:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="time" name="end_time" id="shift_end_time" value="{{ old('end_time', $shift->end_time ?? '') }}" required>
                    </div>
                </div>

                <!-- Break Start Time -->
                <div class="row mt-3">
                    <div class="col">
                        <label for="break_start_time" class="form-label">Break Start Time:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="time" name="break_start_time" id="break_start_time" value="{{ old('break_start_time', $shift->break_start_time ?? '') }}">
                    </div>
                </div>

                <!-- Break End Time -->
                <div class="row mt-3">
                    <div class="col">
                        <label for="break_end_time" class="form-label">Break End Time:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="time" name="break_end_time" id="break_end_time" value="{{ old('break_end_time', $shift->break_end_time ?? '') }}">
                    </div>
                </div>
            
                <!-- Shift Description (Optional) -->
                <div class="row mt-3">
                    <div class="col">
                        <label for="description" class="form-label">Description:</label>
                    </div>
                    <div class="col">
                        <textarea class="form-control mb-3" name="description" id="description" rows="2">{{ $shift->description }}</textarea>
                    </div>
                </div>
            
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>

        </div>
    </div>
</div>

                        @endforeach


                    </tbody>
                </table>

            </div>
        </div>
    </div>

    

    <!--- NEW SHIFT MODAL --->
    <div class="modal fade" id="newShiftmodal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add new shift</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('shifts.store') }}" method="POST">
                        @csrf
                    
                        <!-- Shift Name -->
                        <div class="row mt-3">
                            <div class="col">
                                <label for="name" class="form-label">Shift Name:</label>
                            </div>
                            <div class="col">
                                <input class="form-control" type="text" name="name" id="name" required>
                            </div>
                        </div>
                    
                        <!-- Shift Start Time -->
<div class="row mt-3">
    <div class="col">
        <label for="shift_start_time" class="form-label">Shift Start Time:</label>
    </div>
    <div class="col">
        <input class="form-control" type="time" name="start_time" id="shift_start_time" required>
    </div>
</div>

<!-- Shift End Time -->
<div class="row mt-3">
    <div class="col">
        <label for="shift_end_time" class="form-label">Shift End Time:</label>
    </div>
    <div class="col">
        <input class="form-control" type="time" name="end_time" id="shift_end_time" required>
    </div>
</div>

<hr>

<!-- Break Start Time (Optional) -->
<div class="row mt-3">
    <div class="col">
        <label for="break_start_time" class="form-label">Break Start Time:</label>
    </div>
    <div class="col">
        <input class="form-control" type="time" name="break_start_time" id="break_start_time">
    </div>
</div>

<!-- Break End Time (Optional) -->
<div class="row mt-3">
    <div class="col">
        <label for="break_end_time" class="form-label">Break End Time:</label>
    </div>
    <div class="col">
        <input class="form-control" type="time" name="break_end_time" id="break_end_time">
    </div>
</div>

                    
                        <!-- Shift Description (Optional) -->
                        <div class="row mt-3">
                            <div class="col">
                                <label for="description" class="form-label">Description:</label>
                            </div>
                            <div class="col">
                                <textarea class="form-control mb-3" name="description" id="description" rows="2"></textarea>
                            </div>
                        </div>
                    
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>