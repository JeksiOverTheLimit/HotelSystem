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
    <nav class='navbar navbar-expand-sm bg-dark navbar-dark' id="navigation-placeholder">
    </nav>

    <main>
        <h1 class="text-center">All Currencies!</h1>
        <div class="container">
            <div class="container mt-3">
                <table class="table table-primary table-striped">
                    <thead>
                        <tr>
                            <th>Currency Name</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                        foreach ($currencies as $currency) { ?>
                            <tr>
                                <td><?php echo $currency->getName(); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class='dropdown-item' href="#" onclick="showDeletePopup(<?php echo $currency->getId(); ?>)">Delete</a></li>
                                            <li><a class='dropdown-item' href="../Controllers/CurrencyPageController.php?Edit&editId=<?php echo $currency->getId(); ?>">Edit</a></li>
                                        </ul>
                                    </div>
                                    <div id='overlay'></div>
                                    <div id='form-container'>
                                        <form id='delete-form' method='POST'>
                                            <p class='text-center'>Are you sure you want to delete this country?</p>
                                            <input type='hidden' name='currencyId' value="<?php $currency->getId(); ?>">
                                            <input type='submit' name='delete' value='Delete'>
                                            <input type='button' name='cancel' value='Cancel' onclick='hideDeletePopup()'>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>

</html>
<script>
    function showDeletePopup(currencyId) {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'block';
        formContainer.style.display = 'block';

        var deleteForm = document.getElementById('delete-form');
        deleteForm.action = "../Controllers/CurrencyPageController.php?deleteId=" + currencyId;
    }

    function hideDeletePopup() {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'none';
        formContainer.style.display = 'none';
    }
</script>