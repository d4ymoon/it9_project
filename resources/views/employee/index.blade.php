<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }
        .container-fluid {
            height: 100vh;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }
        .row {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #sidebar {
            height: 100vh;
            overflow-y: auto;
            background-color: #222222;
            transition: width 0.3s;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            padding: 0;
             scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;  /* IE and Edge */

        }

        #sidebar::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}
        #content-wrapper {
            margin-left: 200px;
            height: 100vh;
            transition: margin-left 0.3s;
            flex: 1;
            padding: 0;
            position: relative;
        }
        #contentFrame {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        .sidebar-collapsed #content-wrapper {
            margin-left: 60px;
        }
        /* Custom scrollbar for sidebar */
        #sidebar::-webkit-scrollbar {
            width: 5px;
        }
        #sidebar::-webkit-scrollbar-track {
            background: #333;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: #666;
            border-radius: 5px;
        }
    </style>
</head>
<body style="">
    <div class="container-fluid">
        <div class="row">
            <div id="sidebar" class="col-auto px-0 sidebar-expanded" style="width: 200px;">
                <div class="text-center my-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 150px;">
                </div>
                <div class="d-flex flex-column align-items-center px-3 pt-2 text-white">  
                    
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center " id="menu">

                    <li class="text-center">
                        <h5 class="nav-link align-middle px-2" style="user-select: none; cursor: pointer;">  
                        <span class="sidebar-text align-items-center"><b>Payroll System</b></span>
                        </h5>
                    </li>

                   
                       <hr>
        

                    <li>
                        <a href="{{ route('employee.payslips.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('employee.payslips.index') }}')">
                            <i class="bi bi-receipt"></i> <span class="sidebar-text">Payslips</span>
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('employee.loans.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('employee.loans.index') }}')">
                            <i class="bi bi-cash-coin"></i> <span class="sidebar-text">Loans</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('employee.attendance.index') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('employee.attendance.index') }}')">
                            <i class="bi bi-journal"></i> <span class="sidebar-text">Attendance Records</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('employee.attendance.create') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('employee.attendance.create') }}')">
                            <i class="bi bi-person-check"></i> <span class="sidebar-text">Employee attendance</span>
                        </a>
                    </li>
                    <hr>
                     <li>
                        <a href="{{ route('profile.edit') }}" class="nav-link px-2 align-middle" target="content-frame" onclick="saveIframePage('{{ route('profile.edit') }}')">
                            <i class="bi bi-person"></i> <span class="sidebar-text">Profile</span>
                        </a>
                    </li>
                          
                    
                    <form method="POST" action="{{ route('logout') }}" class=" nav-link px-2 align-middle" onsubmit="return confirmLogout(event)">
                        @csrf
                        <button type="submit" class=" text-start text-white bg-transparent border-0 w-100 px-0">
                            <i class="bi bi-box-arrow-right"></i> <span class="sidebar-text">Sign Out</span>
                        </button>
                    </form>
                    </ul>

                    
                    <hr>
                </div>
            </div>
            <div id="content-wrapper">
                <iframe id="contentFrame" name="content-frame"></iframe>
            </div>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        let contentWrapper = document.getElementById("content-wrapper");
        let textElements = document.querySelectorAll(".sidebar-text");
        let dropdownMenu = document.querySelector(".dropdown-menu");

        if (sidebar.classList.contains("sidebar-expanded")) {
            sidebar.classList.remove("sidebar-expanded");
            sidebar.style.width = "60px";
            contentWrapper.style.marginLeft = "60px";
            textElements.forEach(el => el.style.display = "none");
            document.body.classList.add("sidebar-collapsed");
        } else {
            sidebar.classList.add("sidebar-expanded");
            sidebar.style.width = "200px";
            contentWrapper.style.marginLeft = "200px";
            textElements.forEach(el => el.style.display = "inline");
            document.body.classList.remove("sidebar-collapsed");
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

     function confirmLogout(event) {
        if (!confirm("Are you sure you want to sign out?")) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>