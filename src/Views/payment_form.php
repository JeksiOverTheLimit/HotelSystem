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
    <nav class='navbar navbar-expand-sm bg-dark navbar-dark' id = "navigation-placeholder">
    </nav>
    <h1 class="text-center">You want to Make a payment?</h1>
    <br>
    <div class="container">
        <form action ='../Controllers/PaymentPageController.php' method="post">
        <div class="mb-3">
                <input type="hidden" name='id' value="<?php echo isset($_GET['editId']) ? $_GET['editId'] : ''; ?>">
                <label for="reservation" class="form-label">Choose Reservation</label>
                <select class="form-select" id="reservation" name="reservationId" >
                    <option value='' selected>Изберете Резервация</option>";
                    <?php foreach ($reservationOptions as $reservationOption) { ?>
                        <option value='<?php echo $reservationOption['id']; ?>' <?php echo $reservationOption['selected'] ? 'selected' : ''; ?>>
                            <?php echo 'Номер на резервацията ' .  $reservationOption['id'] . ' Начална дата ' . $reservationOption['startingDate'] . ' Крайна дата ' . $reservationOption['finalDate']; ?>
                        </option>

                        <?php } ?>
                </select>
            </div>


            <div class="mb-3">
            <label for="reservation" class="form-label">Choose Currency</label>
                <select class="form-select" id="currency" name="currencyId" >
                    <option value='' selected>Изберете Валута</option>";
                    <?php foreach ($currencyOptions as $currencyOption) { ?>
                        <option value='<?php echo $currencyOption['id']; ?>' <?php echo $currencyOption['selected'] ? 'selected' : ''; ?>>
                            <?php echo $currencyOption['name']; ?>
                        </option>
                        <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" name="price" id="price" value="<?php echo isset($_GET['editId']) ? $payment->getPrice() : ''; ?>">

            </div>

            <div class="mb-3">
                <label for="paymentDate" class="form-label">Payment Date</label>
                <input type='date' class='form-control' name='paymentDate' id='paymentDate' value="<?php echo isset($_GET['editId']) ? $payment->getPaymentDate() : ''; ?>">
            </div>

            <div class="mb-3">
            </div>

            <button class="btn btn-primary" type="submit" name="<?php echo isset($_GET['editId'])  ? 'update' : 'submit'; ?>">Send</button>
        </form>
    </div>
    
</body>

</html>