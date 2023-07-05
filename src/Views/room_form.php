<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../assets/styles/styles.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <nav class='navbar navbar-expand-sm bg-dark navbar-dark' id = "navigation-placeholder">
    <?php include_once "Navigations.php"; ?>
    </nav>
    <main>
        <h1 class="text-center">You want to make a rooms?</h1>

        <div class="container">
        <form action ='../Controllers/RoomController.php' method="post">

            <input type="hidden" name="roomId" value="<?php echo isset($_GET['editId']) ? $_GET['editId'] : ''; ?>">
            <div class="mb-3">
                <label for="number" class="form-label">Number</label>
                <input type="text" class="form-control" name="number" id="number" value="<?php echo isset($_GET['editId']) ? $room->getNumber() : '';?>">
            </div>

            <div class="mb-3">
            <label for="types" class="form-label">Types</label>
            <select class="form-select" name="typeId" id="types">
            <option value='' selected>Изберете Tип</option>";
                    <?php foreach ($typeOptions as $typeOption) { ?>
                        <option value='<?php echo $typeOption['id']; ?>' <?php echo $typeOption['selected'] ? 'selected' : ''; ?>>
                            <?php echo $typeOption['name'] ;?>
                        </option>

                        <?php } ?>
                </select>
            </div>

            <div class="mb-3">
    <label class="form-label">Extras</label>
    <?php foreach ($extraOptions as $extraOption) { ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="extraIds[]" value="<?php echo $extraOption['id'] ?>" <?php echo $extraOption['checked'] ?>>
            <label class="form-check-label" for="extra_<?php echo $extraOption['id'] ?>"><?php echo $extraOption['name'] ?></label>  
        </div>
    <?php } ?>  
</div>
            

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" name="price" id="price" value="<?php echo isset($_GET['editId']) ? $room->getPrice() : '';?>">
            </div>

            <div class="mb-3">
            <button class="btn btn-primary" type="submit" name="<?php echo isset($_GET['editId'])  ? 'update' : 'submit'; ?>">Send</button>
            </div>
        </form>
        </div>
    </main>
</body>
</html>