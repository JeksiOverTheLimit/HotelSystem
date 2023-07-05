<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../assets/styles/styles.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/CallCitySelectMenu.js"></script>
</head>

<body>
    <nav class='navbar navbar-expand-sm bg-dark navbar-dark' id="navigation-placeholder">
    <?php include_once "Navigations.php"; ?>
    </nav>

    <main>
        <h1 class="text-center">All reservation!</h1>
        <form method="POST" action="ReservationController.php?ReservationLists">

            <div class="mb-3">
                <label for="startingDate">StartingDate</label>
                <input type="date" name="startingDate" class="form-control" id="startingDate">
            </div>

            <div class="mb-3">
                <label for="finalDate">FinalDate</label>
                <input type="date" name="finalDate" class="form-control" id="finalDate">
            </div>

            <div class="mb-3">
                
            </div>

            <div class="mb-3">
                <label for="countries" class="form-label">Contries</label>';
                <select class="form-select" id="countries" name="Country" onchange="fetchCitiesByCountry()">';
                    
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">Филтрирай</button>

        </form>
        <div class="container">
            <div class="container mt-3">
                <table class="table table-primary table-striped">
                    <thead>
                        <tr>
                            <th>Employee FirstName</th>
                            <th>Employee LastNamer</th>
                            <th>Number Of room</th>
                            <th>Starting Date</th>
                            <th>Final Date</th>
                            <th>Reservation status</th>
                            <th>Guest Names</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                   <?php  foreach ($reservations as $reservation) { ?>
                    <tbody>
                        <tr>
                            <td><?php echo $reservation['employeeFirstName']; ?> </td>
                            <td><?php echo $reservation['employeeLastName']; ?></td>
                            <td><?php echo $reservation['roomNumber']; ?></td>
                            <td><?php echo $reservation['startingDate']; ?></td>
                            <td><?php echo $reservation['finalDate']; ?></td>
                            <td><?php echo $reservation['roomStatus']; ?></td>
                            <td><a href="../Controllers/ReservationController.php?guestPrivatePageId=<?php echo $reservation['guestId']?>"><?php echo $reservation['guestName']; ?></a></td>
                            <td><?php echo $reservation['guestCountry']; ?></td>
                            <td><?php echo $reservation['guestCity'];?></td>
                            <td>
                                <div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class='dropdown-item' href="#" onclick="showDeletePopup(<?php echo $reservation['id']; ?>)">Delete</a></li>
                                        <li><a class='dropdown-item' href='../Controllers/ReservationController.php?Edit&reservationId=<?php echo $reservation['id'];?>&guestId=<?php echo $reservation['guestId'];?>'>Edit</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <div id='overlay'></div>
                        <div id='form-container'>
                            <form id='delete-form' method='POST'>
                                <p class='text-center'>Are you sure you want to delete this country?</p>
                                <input type='submit' name='delete' value='Delete'>
                                <input type='button' name='cancel' value='Cancel' onclick='hideDeletePopup()'>
                            </form>
                        </div>
                    <?php } ?>
                </table>
            </div>
            <div class="content"></div>
        </div>
    </main>
</body>
</html>
<script>
    function showDeletePopup(reservationId) {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'block';
        formContainer.style.display = 'block';

        var deleteForm = document.getElementById('delete-form');
        deleteForm.action = "../Controllers/ReservationController.php?deleteId=" + reservationId;
    }

    function hideDeletePopup() {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'none';
        formContainer.style.display = 'none';
    }
</script>