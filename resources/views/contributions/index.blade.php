<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contribution Types</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="default-padding theme1">

    <div class="container-fluid">
        <!--- Search and Add Contribution Type --->
        <div class="row mt-2">
            <form action="" method="GET" class="col-auto d-flex justify-content-start align-items-center">
                <label for="searchInput" class="label me-2">Search:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" name="query" style="width: 190px;">
                    <button class="btn btn-dark"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContributionType">
                    + Add Contribution Type
                </button>
            </div>
        </div>

        <!--- Contribution Types Table --->
        <div class="row mt-2">
            <div class="col">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contributionTypes as $contributionType)
                            <tr>
                                <td>{{ $contributionType->id }}</td>
                                <td>{{ $contributionType->name }}</td>
                                <td class="text-nowrap">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editContributionTypeModal{{ $contributionType->id }}">
                                        Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('contributiontypes.destroy', $contributionType->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this contribution type?');"
                                        style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
     <!-- Add Contribution Type Modal -->
        <div class="modal fade" id="addContributionType" tabindex="-1" aria-labelledby="addContributionTypeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm  modal-dialog-centered">
              <div class="modal-content">
                
                <div class="modal-header">
                  <h5 class="modal-title" id="addContributionTypeModalLabel">Add New Contribution Type</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('contributiontypes.store') }}" method="POST">
                  @csrf
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="contribution_type_name" class="form-label">Contribution Type Name</label>
                      <input type="text" name="name" class="form-control" id="contribution_type_name" placeholder="e.g. SSS" required>
                    </div>
                  </div>
                  
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>
