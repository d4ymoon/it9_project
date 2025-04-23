<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Positions</title>
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
                    data-bs-target="#newpositionmodal">
                    + Add New Position
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

        <div class="row mt-2">
            <div class="col">
                <table class="table table-striped table-hover table-bordered ">
                    <thead>
                        <tr>
                            <th>Position Name</th>
                            <th>Base Salary</th>
                            <th style="width:275px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $position)
                            <tr>
                                <td>{{ $position->name }}</td>
                                <td>{{ $position->salary }}</td>
                                <td class="text-nowrap" style="width:275px">

                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editPositionModal{{ $position->id }}">
                                        Edit position
                                    </button>

                                    <form action="{{ route('positions.destroy', $position->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this position?');"
                                        style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Edit Position Modal -->
                            <div class="modal fade" id="editPositionModal{{ $position->id }}" tabindex="-1"
                                aria-labelledby="editPositionModalLabel{{ $position->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editPositionModalLabel{{ $position->id }}">Edit
                                                Position</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <form action="{{ route('positions.update', $position->id) }}" method="POST"
                                            id="positionForm{{ $position->id }}" 
                                            data-initial-salary="{{ $position->salary }}"
                                            data-employee-count="{{ $position->employees_count }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name{{ $position->id }}"
                                                        class="form-label">Name</label>
                                                    <input type="text" class="form-control"
                                                        id="name{{ $position->id }}" name="name"
                                                        value="{{ $position->name }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="salary{{ $position->salary }}" class="form-label">Salary
                                                        </label>
                                                    <input type="text" class="form-control"
                                                        id="salary{{ $position->id }}" name="salary"
                                                        value="{{ $position->salary }}" required
                                                         oninput="checkSalaryChange({{ $position->id }})">
                                                </div>

                                        
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary"  onclick="return confirmUpdate({{ $position->id }})">Update
                                                    Position</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        @endforeach


                    </tbody>
                </table>

            </div>
        </div>
    </div>

    

    <!--- NEW POSITION MODAL --->
    <div class="modal fade" id="newpositionmodal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px; width: 100%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 h5" id="exampleModalLabel">Add new position</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('position.store') }}" method="POST">
                        @csrf
                        <div class="row mt-3">
                            <div class="col">
                                <label for="name" class="form-label h5">Position Name:</label>
                            </div>
                            <div class="col">
                                <input class="form-control" type="text" name="name" id="name" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col">
                                <label for="contact_number" class="form-label h5">Base Salary:</label>
                            </div>
                            <div class="col">
                                <input class="form-control" type="number" name="salary" id="salary"
                                    step="0.01" required>
                            </div>
                        </div>

                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const changedSalaries = {};
    
        function checkSalaryChange(id) {
            const form = document.getElementById('positionForm' + id);
            const initial = parseFloat(form.getAttribute('data-initial-salary'));
            const current = parseFloat(document.getElementById('salary' + id).value);
    
            changedSalaries[id] = (initial !== current);
        }
    
        function confirmUpdate(id) {
            const form = document.getElementById('positionForm' + id);
            const count = form.getAttribute('data-employee-count');
            
            if (changedSalaries[id]) {
                return confirm(`You changed the salary. This will affect ${count} employee(s).\nAre you sure you want to proceed?`);
            }
    
            return true;
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>