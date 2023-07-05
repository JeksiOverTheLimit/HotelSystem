<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../assets/styles/styles.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/nav.js"></script>
</head>

<body>
    <main>
    <nav class='navbar navbar-expand-sm bg-dark navbar-dark' id = "navigation-placeholder">
    </nav>
        <h1 class="text-center">You want to make a currency?</h1>
        <div class="container">
            <form action="../Controllers/CountryPageController.php" method="post">
                <div class="guest-fields mb-3">
                    <input type="hidden" name="countryId" value="<?php echo isset($_GET['editId']) ? $_GET['editId'] : ''; ?>">
                    <label for="name" class="form-label">Name of Currency</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo isset($_GET['editId']) ? $currency->getName() : ''; ?>">
                </div>

                <div class="mb-3">
                    <button class="btn btn-primary" type="submit" name="<?php echo isset($_GET['editId'])  ? 'update' : 'submit'; ?>">Send</button>
                    <a href="../Controllers/CurrencyPageController.php?CurrencyList" class="btn btn-danger">Cancel</a>  
                </div>
            </form>
        </div>
    </main>
</body>
</html>