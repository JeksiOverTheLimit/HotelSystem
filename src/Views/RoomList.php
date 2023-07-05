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
        <h1 class="text-center">All rooms!</h1>
        <div class="container">
            <form method="POST" action="RoomPageController.php?RoomLists">
                <select class="form-select" name="typeId">
                    <?php foreach ($typeOptions as $typeOption) { ?>
                        <option value='<?php echo $typeOption['id']; ?>' <?php echo $typeOption['selected'] ? 'selected' : ''; ?>>
                            <?php echo $typeOption['name']; ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="submit" class="btn btn-primary mt-3">Филтрирай</button>
            </form>
            <div class="mb-3">
                <div class="container mt-3">
                    <table class="table table-primary table-striped">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Extras</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roomOptions as $roomOption) { ?>
                                <tr>
                                    <td><?php echo $roomOption['number'] ?></td>
                                    <td><?php echo $roomOption['type'] ?></td>
                                    <td><?php echo $roomOption['price'] ?></td>


                                    <?php if (empty($roomOption['extras'])) { ?>
                                        <td>No Extras</td>
                                    <?php } else { ?>
                                        <td><?php echo implode(', ', $roomOption['extras']); ?></td>
                                    <?php } ?>

                                    <td>
                                        <div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">

                                                <li><a class='dropdown-item' href="#" onclick="showDeletePopup(<?php echo $roomOption['id']; ?>)">Delete</a></li>
                                                <li><a class='dropdown-item' href='../Controllers/RoomPageController.php?Edit&editId=<?php echo $roomOption['id']; ?>'>Edit</a></li>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                        </tbody>
                        <?php  } ?>
                </div>
            </table>
            </div>
        </div>
        <div id='overlay'></div>
                        <div id='form-container'>
                            <form id='delete-form' method='POST'>
                                <p class='text-center'>Are you sure you want to delete this country?</p>
                                <input type='submit' name='delete' value='Delete'>
                                <input type='button' name='cancel' value='Cancel' onclick='hideDeletePopup()'>
                            </form>
                        </div>
    </main>
</body>

</html>
<script>
    function showDeletePopup(roomId) {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'block';
        formContainer.style.display = 'block';

        var deleteForm = document.getElementById('delete-form');
        deleteForm.action = "../Controllers/RoomPageController.php?deleteId=" + roomId;
    }

    function hideDeletePopup() {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('form-container');

        overlay.style.display = 'none';
        formContainer.style.display = 'none';
    }
</script>