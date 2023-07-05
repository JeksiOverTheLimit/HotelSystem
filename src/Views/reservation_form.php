<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../assets/styles/styles.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/ReservationSelectMenu.js"></script>
    <script src="../../assets/js/nav.js"></script>
</head>

<body>
    <nav class='navbar navbar-expand-sm bg-dark navbar-dark' id = "navigation-placeholder">
    </nav>

    <main>
        <h1 class="text-center">You want to make a reservation?</h1>

        <div class="container">
            <form action="../Controllers/ReservationPageController.php" method="post" id="reservationForm">
                <div class="mb-3">
                <input type="hidden" name="reservationId" value="<?php echo isset($_GET['reservationId']) ? $_GET['reservationId'] : ''; ?>">
                <label for="employees" class="form-label">Employees</label>
                <select class="form-select" name="employeeId" id="employees">
                    <?php foreach ($employeeSelectMenu as $employee) { ?>
                        <option value='<?php echo $employee['id']; ?>' <?php echo $employee['selected'] ? 'selected' : ''; ?>>
                            <?php echo $employee['name']; ?>
                        </option>
                    <?php } ?>
                </select>
                </div>

                <div class="mb-3">
                <label for="roooms" class="form-label">Rooms</label>
                <select class="form-select" name="roomId" id="rooms">
                <?php foreach ($roomSelectMenu as $room) { ?>
                        <option value='<?php echo $room['id']; ?>' <?php echo $room['selected'] ? 'selected' : ''; ?>>
                            <?php echo $room['name'] . ' - ' . $room['types'] . ' - ' . $room['extra'] ; ?>
                        </option>
                    <?php } ?>
                </select>
                </div>

                <div class="mb-3">
                    <label for="startingDate" class="form-label">Starting Date</label>
                    <input type="date" class="form-control" name="startingDate" id="startingDate" value="<?php 
                    echo isset($_GET['reservationId']) ? $reservationStartingDate : '' ;?>">
                </div>

                <div class="mb-3">
                    <label for="finalDate" class="form-label">Final Date</label>
                    <input type="date" class="form-control" name="finalDate" id="finalDate" value="<?php echo isset($_GET['reservationId']) ? $reservationFinalDate : '' ;?>">
                </div>

                <div class="mb-3">
               <label for="status" class="form-label">Status</label>
               <select class="form-select" name="statusId" id="status">
               <?php foreach ($statusSelectMenu as $status) { ?>
                        <option value='<?php echo $status['id']; ?>' <?php echo $status['selected'] ? 'selected' : ''; ?>>
                            <?php echo $status['name'] ;?>
                        </option>
                    <?php } ?>
                </select>
                </div>

                <fieldset id="guestContainer">
                    <legend>Data for Guest</legend>

                    <input type="hidden" name="guestId" value="<?php echo isset($_GET['guestId']) ? $_GET['guestId'] : ''; ?>">
                    <div class="guest-fields mb-3">
                        <label for="firstName" class="form-label ">First Name</label>
                        <input type="text" class="form-control" name="firstName" id="firstName" value="<?php echo isset($_GET['guestId']) ? $guestFirstName :''; ?>">
                    </div>

                    <div class="guest-fields mb-3">
                        <label for="lastName" class="form-label ">Last Name</label>
                        <input type="text" class="form-control" name="lastName" id="lastName" value="<?php echo isset($_GET['guestId']) ? $guestLastName :''; ?>">
                    </div>

                    <div class="guest-fields mb-3">
                        <label for="egn" class="form-label ">EGN</label>
                        <input type="text" class="form-control" name="egn" id="egn" value="<?php echo isset($_GET['guestId']) ? $guestEGN :''; ?>">
                    </div>

                    <div class="guest-fields mb-3">
                        <label for="phoneNumber" class="form-label ">Phone</label>
                        <input type="text" class="form-control" name="phoneNumber" id="phoneNumber" value="<?php echo isset($_GET['guestId']) ? $guestPhone :''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="countries" class="form-label">Contries</label>';
                        <select  class = "form-select" id="countries" name="Country" onchange="fetchCitiesByCountry()" >';
                        <option value='' selected>Изберете държава</option>";
                        <?php foreach ($countrySelectMenu as $country) { ?>
                        <option value='<?php echo $country['id']; ?>' <?php echo $country['selected'] ? 'selected' : ''; ?>>
                            <?php echo $country['name'] ;?>
                        </option>
                    <?php } ?>
                </select>
                    </div>
        
                    <div class="mb-3">
                       <label for="cities" class="form-label">Cities</label>;
                        <select class="form-select" id="cities" name="City">
                        <?php foreach ($citySelectMenu as $city) { ?>
                        <option value='<?php echo $city['id']; ?>' <?php echo $city['selected'] ? 'selected' : ''; ?>>
                            <?php echo $city['name'] ;?>
                        </option>
                    <?php } ?>
                </select>
                        </select>
                    </div>
                </fieldset>
                <div class="mb-3">
                <button class="btn btn-primary" type="submit" name="<?php echo isset($_GET['reservationId']) && isset($_GET['guestId'])  ? 'update' : 'submit'; ?>">Send</button>
        </form>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
