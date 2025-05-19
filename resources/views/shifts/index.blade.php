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

<body class="default-padding theme1" style="background-color: 	#f8f9fa">


    <div class="container-fluid">
        <!-- Search and Add Row -->
        <div class="row mt-2 align-items-end">
            <div class="col-auto">
                <form action="{{ route('shifts.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-auto">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Shift name...">
                            <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-auto">
                        <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Add Button -->
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newShiftModal">
                    <i class="bi bi-plus-circle"></i> Add Shift
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
                            <th style="width:">ID</th>
                            <th style="width:">Name</th>
                            <th style="width:">Start Time</th>
                            <th style="width:">End Time</th>
                            <th style="width:">Break Start</th>
                            <th style="width:">Break End</th>
                            <th style="width:">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shifts as $shift)
                            <tr>
                                <td>{{ $shift->id }}</td>
                                <td>{{ $shift->name }}</td>
                                <td>{{ $shift->start_time }}</td>
                                <td>{{ $shift->end_time }}</td>
                                <td>{{ $shift->break_start }}</td>
                                <td>{{ $shift->break_end }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editShiftModal{{ $shift->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Are you sure you want to delete this shift?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> 
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Edit Shift Modal -->
                            <div class="modal fade" id="editShiftModal{{ $shift->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Shift</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name{{ $shift->id }}" class="form-label">Shift Name</label>
                                                    <input type="text" class="form-control" id="name{{ $shift->id }}" 
                                                           name="name" value="{{ $shift->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="start_time{{ $shift->id }}" class="form-label">Start Time</label>
                                                    <input type="time" class="form-control" id="start_time{{ $shift->id }}" 
                                                           name="start_time" value="{{ $shift->start_time }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="end_time{{ $shift->id }}" class="form-label">End Time</label>
                                                    <input type="time" class="form-control" id="end_time{{ $shift->id }}" 
                                                           name="end_time" value="{{ $shift->end_time }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="break_start{{ $shift->id }}" class="form-label">Break Start</label>
                                                    <input type="time" class="form-control" id="break_start{{ $shift->id }}" 
                                                           name="break_start" value="{{ $shift->break_start }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="break_end{{ $shift->id }}" class="form-label">Break End</label>
                                                    <input type="time" class="form-control" id="break_end{{ $shift->id }}" 
                                                           name="break_end" value="{{ $shift->break_end }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $shifts->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    

    <!--- NEW SHIFT MODAL --->
    <div class="modal fade" id="newShiftModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                <label for="start_time" class="form-label">Shift Start Time:</label>
                            </div>
                            <div class="col">
                                <input class="form-control" type="time" name="start_time" id="start_time" required>
                            </div>
                        </div>

                        <!-- Shift End Time -->
                        <div class="row mt-3">
                            <div class="col">
                                <label for="end_time" class="form-label">Shift End Time:</label>
                            </div>
                            <div class="col">
                                <input class="form-control" type="time" name="end_time" id="end_time" required>
                            </div>
                        </div>

                        <!-- Break Start Time -->
                        <div class="row mt-3">
                            <div class="col">
                                <label for="break_start" class="form-label">Break Start Time:</label>
                            </div>
                            <div class="col">
                                <input class="form-control" type="time" name="break_start" id="break_start" required>
                            </div>
                        </div>

                        <!-- Break End Time -->
                        <div class="row mt-3">
                            <div class="col">
                                <label for="break_end" class="form-label">Break End Time:</label>
                            </div>
                            <div class="col">
                                <input class="form-control" type="time" name="break_end" id="break_end" required>
                            </div>
                        </div>
                    
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Shift</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>