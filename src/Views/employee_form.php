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
    <h1 class="text-center">You want to register a employee?</h1>
    <div class="container">
        <form action="../Controllers/EmployeeController.php" method="post">
            <div class="mb-3">
                <input type="hidden" name="employeeId" value="<?php echo isset($_GET['editId']) ? $employee->getId() : '' ?>">
            </div>

            <div class="mb-3">
                <label for="names" class="form-label">Names</label>
                <input type="text" class="form-control" name="names" id="names" value="<?php echo isset($_GET['editId']) ? $employee->getFirstName() . ' ' . $employee->getLastName() : '' ?>">
            </div>
        
            <div class="mb-3">
                <label for="egn" class="form-label">EGN</label>
                <input type="text" class="form-control" name="egn" id="egn" value="<?php echo isset($_GET['editId']) ? $employee->getEgn() : '' ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" name="email" id="email" value="<?php echo isset($_GET['editId']) ? $employee->getEmail() : '' ?>">
            </div>

            <div class="mb-3">
                <label for="phoneNumber" class="form-label">PhoneNumber</label>
                <input type="text" class="form-control" name="phone" id="phoneNumber" value="+359<?php echo isset($_GET['editId']) ? $employee->getPhoneNumber() : '' ?>">
            </div>

            <div class="mb-3">
                <label for="countries" class="form-label">Contries</label>
                <select class="form-select" id="countries" name="Country" onchange="fetchCitiesByCountry()">

                    <option value='' selected>Изберете държава</option>";
                    <?php foreach ($countryOptions as $countryOption) { ?>
                        <option value='<?php echo $countryOption['id']; ?>' <?php echo $countryOption['selected'] ? 'selected' : ''; ?>>
                            <?php echo $countryOption['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="cities" class="form-label">Cities</label>
                <select class="form-select" id="cities" name="City">
                    <?php foreach ($cityOptions as $cityOption) : ?>
                        <option value="<?php echo $cityOption['id']; ?>" <?php if ($cityOption['selected']) echo 'selected'; ?>><?php echo $cityOption['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn btn-primary" type="submit" name="<?php echo isset($_GET['editId'])  ? 'update' : 'submit'; ?>">Send</button>
        </form>
    </div>
</body>

</html>