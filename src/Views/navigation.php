<!DOCTYPE html>
<html>

<head>
</head>

<body>
        <div class='container-fluid'>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class='nav-item'><a class='nav-link active' href='HomeController.php'>Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Employee</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="EmployeeController.php?Employees">Create</a></li>
                        <li><a class="dropdown-item" href="EmployeeController.php?EmployeeLists">Lists</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Payments</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="PaymentController.php?Payment">Create</a></li>
                        <li><a class="dropdown-item" href="PaymentController.php?PaymentLists">Lists</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Currency</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="CurrencyController.php?Currency">Create</a></li>
                        <li><a class="dropdown-item" href="CurrencyController.php?CurrencyList">Lists</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Rooms</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="RoomController.php?Rooms">Create</a></li>
                        <li><a class="dropdown-item" href="RoomController.php?RoomLists">Lists</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Country</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../Controllers/CountryController.php?Create">Create</a></li>
                        <li><a class="dropdown-item" href="../Controllers/CountryController.php?CountryList">Lists</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Reservations</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="ReservationController.php?Reservation">Create</a></li>
                        <li><a class="dropdown-item" href="ReservationController.php?ReservationLists">Lists</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
</body>
</html>