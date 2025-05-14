<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 

    <link rel="stylesheet" href="{{ asset('css/style.css') }}"><link rel="stylesheet" href="{{ asset('css/style2.css') }}">
</head>
<body style="overflow:hidden">
<div class="row lolol align-items-center" style="background-color: #880808; margin: 0; padding: 15px;">
        <div class="col-6">
            <h5 class="mb-0"><strong>Payroll System</strong></h5> 
        </div>
        <div class="col text-end">
            <div class="dropdown">
                <a class="btn btn-outline dropdown-toggle" style="color:white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}</a>
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
    <div class="container-fluid" >
        <div class="row flex-nowrap">
            <div id="sidebar" class="col-auto col-md-3 col-xl-2 px-0  sidebar-expanded" style="background-color: #222222; overflow:hidden; width: 200px;">
                <div class="d-flex flex-column align-items-center px-3 pt-2 text-white min-vh-90">                          
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center " id="menu">

                    <li>
                        <h5 class="nav-link align-middle px-2" onclick="toggleSidebar()" style="user-select: none; cursor: pointer;">  
                        <i class="bi bi-list"></i> <span class="sidebar-text align-items-center"><b>Payroll System</b></span>
                        </h5>
                    </li>
                          
                       <hr>
                    <li>
                        <a href="{{ route('dashboard2') }}" class="nav-link align-middle px-2" target="content-frame" onclick="saveIframePage('{{ route('dashboard2') }}')">
                             <i class="bi bi-speedometer"></i> <span class="sidebar-text">Dashboard</span></span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('positions.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('positions.index') }}')">
                            <i class="bi bi-briefcase"></i> <span class="sidebar-text">Positions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shifts.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('shifts.index') }}')">
                            <i class="bi bi-clock"></i> <span class="sidebar-text">Shift Types</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contributiontypes.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('contributiontypes.index') }}')">
                            <i class="bi bi-piggy-bank"></i> <span class="sidebar-text">Contribution Types</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('employees.index') }}')">
                            <i class="bi bi-people"></i> <span class="sidebar-text">Employees</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('payslips.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('payslips.index') }}')">
                            <i class="bi bi-receipt"></i> <span class="sidebar-text">Payslips</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('payslips.payrolls') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('payslips.payrolls') }}')">
                            <i class="bi bi-cash-stack"></i> <span class="sidebar-text">Payrolls</span>
                        </a>
                    </li>

                    <li class="mt-2">
                        <a href="{{ route('loans.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('loans.index') }}')">
                            <i class="bi bi-cash-coin"></i> <span class="sidebar-text">Loans</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('attendances.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('attendances.index') }}')">
                            <i class="bi bi-journal"></i> <span class="sidebar-text">Attendance Records</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('attendances.create') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('attendances.create') }}')">
                            <i class="bi bi-person-check"></i> <span class="sidebar-text">Employee attendance</span>
                        </a>
                    </li>
                   
                    </ul>
                    <hr>
                </div>
            </div>
            <div class="col px-0 d-flex">
                <iframe id="contentFrame" name="content-frame" width="100%" height="100%"></iframe>            
            </div>
        </div>
    </div>

    <script>

    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        let textElements = document.querySelectorAll(".sidebar-text");
        let dropdownMenu = document.querySelector(".dropdown-menu");

        if (sidebar.classList.contains("sidebar-expanded")) {
            sidebar.classList.remove("sidebar-expanded");
            sidebar.style.width = "60px";
            textElements.forEach(el => el.style.display = "none");
            dropdownMenu.style.left = "auto";

        } else {
            sidebar.classList.add("sidebar-expanded");
            sidebar.style.width = "200px";
            textElements.forEach(el => el.style.display = "inline");
        }
    }
        
    function saveIframePage(page) {
        localStorage.setItem('lastIframePage', page);
        document.getElementById('contentFrame').src = page;
        highlightActiveLink(page);
    }

    window.onload = function () {
        var lastPage = localStorage.getItem('lastIframePage') || '{{ route("dashboard2") }}';
        document.getElementById('contentFrame').src = lastPage;
        highlightActiveLink(lastPage);
    }

    function highlightActiveLink(activePage) {
        document.querySelectorAll(".nav-link").forEach(link => {
            link.classList.remove("active");
        });

        let matchedLink = document.querySelector(`.nav-link[href="${activePage}"]`);
        if (matchedLink) {
            matchedLink.classList.add("active");
        }
    }

    document.querySelectorAll('.card a').forEach(link => {
        link.addEventListener('click', function (event) {
            let targetPage = this.getAttribute('href');
            highlightActiveLink(targetPage);
        });
    });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>