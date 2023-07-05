<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/nav.js"></script>
</head>

<body>
    <nav class='navbar navbar-expand-sm bg-dark navbar-dark' id="navigation-placeholder">
    </nav>
    <div class="container">
        <div class="container mt-3">
            <h2>Guest Information</h2>
            <table class="table table-primary">
                <tbody>
                    <tr>
                        <?php foreach($guestInformations as $guestInformation) { ?>
                        <th>First Name</th>
                        <td><?php echo $guestInformation['firstName']; ?></td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td><?php echo $guestInformation['lastName'] ?></td>
                    </tr>
                    <tr>
                        <th>Phone Number</th>
                        <td><?php echo $guestInformation['phone']; ?></td>
                    </tr>
                    <tr>
                        <th>EGN</th>
                        <td><?php echo $guestInformation['egn'] ?></td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td><?php echo $guestInformation['country'] ?></td>
                    </tr>
                    <tr>
                        <th>EGN</th>
                        <td><?php echo $guestInformation['city'] ?></td>
                    </tr>
                </tbody>
                <?php } ?>
            </table>

            <h2>Existing Reservations</h2>
            <table class="table table-primary table-striped">
                <thead>
                    <tr>
                        <th>Number Of room</th>
                        <th>Starting Date</th>
                        <th>Final Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guestReservationsDate as $guestReservationDate) { ?>
                        <tr>
                            <td><?php echo $guestReservationDate['number']; ?></td>
                            <td><?php echo $guestReservationDate['startingDate']; ?></td>
                            <td><?php echo $guestReservationDate['finalDate']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>