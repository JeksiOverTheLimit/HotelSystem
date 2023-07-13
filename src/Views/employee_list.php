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
    <?php include_once "navigation.php"; ?>
    </nav>
    <h1 class="text-center">All Employees?</h1>
    <div class="container mt-3">
        <table class="table table-primary table-striped">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Egn</th>
                    <th>Phone Number</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($employeess as $employee) { ?>
                    <tr>
                        <td><?php echo $employee['firstName']; ?></td>
                        <td><?php echo $employee['lastName']; ?></td>
                        <td><?php echo $employee['egn']; ?></td>
                        <td><?php echo $employee['phoneNumber']; ?></td>
                        <td><?php echo $employee['country']; ?></td>
                        <td><?php echo $employee['city']; ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class='dropdown-item' href="#" onclick="showDeletePopup(<?php echo $employee['id']; ?>)">Delete</a></li>
                                    <li><a class='dropdown-item' href="../Controllers/EmployeeController.php?Edit&editId=<?php echo $employee['id']; ?>">Edit</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
    </div>
    <div id='overlay'></div>
    <div id='form-container'>
        <form id='delete-form' method='POST'>
            <p class='text-center'>Are you sure you want to delete this country?</p>
            <input type='hidden' name='employeeId' value="<?php echo $employee['id']; ?>">
            <input type='submit' name='delete' value='Delete'>
            <input type='button' name='cancel' value='Cancel' onclick='hideDeletePopup()'>
        </form>
    </div>
    </div>
<?php } ?>
</tbody>
</table>
</body>

</html>
<script>
    function showDeletePopup(employeeId) {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'block';
        formContainer.style.display = 'block';

        var deleteForm = document.getElementById('delete-form');
        deleteForm.action = "../Controllers/EmployeeController.php?deleteId=" + employeeId;
    }

    function hideDeletePopup() {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'none';
        formContainer.style.display = 'none';
    }
</script>