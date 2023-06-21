<?php

declare(strict_types=1);

include_once "../Models/Payments.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/PaymentsRepository.php";
include_once "../Models/Currencies.php";
include_once "../Database/Repositories/CurrenciesRepository.php";
include_once "../Models/Reservations.php";
include_once "../Database/Repositories/ReservationsRepository.php";

$payment = new PaymentPageController();

class PaymentPageController
{
    private const VIEW_PATH = "../Views/Payment.html";
    private const VIEW_LIST_PATH = "../Views/PaymentLists.html";
    private PaymentsRepository $paymentsRepository;
    private CurrenciesRepository $currenciesRepository;
    private ReservationsRepository $reservationsRepository;

    public function __construct()
    {
        $this->paymentsRepository = new PaymentsRepository();
        $this->currenciesRepository = new CurrenciesRepository();
        $this->reservationsRepository = new ReservationsRepository();

        
        $this->create();
        $this->update();
        $this->delete();

        if (isset($_GET['Payment'])) {
            echo $this->showPaymentPage();
        }

        if (isset($_GET['PaymentLists'])) {
            echo $this->showPaymentLists();
        }
    }

    public function showPaymentPage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $allReservations = $this->generateReservationsSelectMenu();
        $allCurrencies = $this->generateCurrecySelectMenu();
        
        $result = sprintf($file, $allReservations, $allCurrencies);

        return $result;
    }

    public function showPaymentLists(): string
    {
        $file = file_get_contents(self::VIEW_LIST_PATH);
             
        $allEmployes = $this->showAllPayments();
        $generateEditPopup = $this->generateUpdatePopupForm();
        $generateDeletePopup = $this->generateDeletePopup();

        $result = sprintf($file,  $allEmployes, $generateEditPopup, $generateDeletePopup);

        return $result;
    }

    private function generateReservationsSelectMenu(int $selectedReservationId = null): string
    {
        $reservations = $this->reservationsRepository->getAllReservations();

        $selectMenus = '<label for="reservationId" class="form-label">Choose Reservation</label>';
        $selectMenus .= '<select class="form-select" name="reservationId" id="reservationId">';

        foreach ($reservations as $reservation) {
            $optionTemplate = "<option value='%s' %s>Номер на Резервацията:%s - Начална дата %s :: Крайна дата %s</option>";
            $reservationId = $reservation->getId();
            $reservationStartingDate = $reservation->getStartingDate();
            $reservationFinalDate = $reservation->getFinalDate();
            $selected = ($selectedReservationId !== null && $selectedReservationId === $reservationId) ? "selected" : "";
            $option = sprintf($optionTemplate, $reservationId, $selected, $reservationId, $reservationStartingDate, $reservationFinalDate);
            $selectMenus .= $option;
        }

        $selectMenus .= "</select>";

        return $selectMenus;
    }


    private function generateCurrecySelectMenu(int $selectedCurrencyId = null): string
    {
        $currencies = $this->currenciesRepository->getAllCurrencies();

        $selectMenus = '<label for="currencyId" class="form-label">Choose Currency</label>';
        $selectMenus .= '<select class="form-select" name="currencyId" id="currencyId">';

        foreach ($currencies as $currency) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $currencyId = $currency->getId();
            $currencyName = $currency->getName();
            $selected = ($selectedCurrencyId !== null && $selectedCurrencyId === $currencyId) ? "selected" : "";
            $option = sprintf($optionTemplate, $currencyId, $selected, $currencyName);
            $selectMenus .= $option;
        }

        $selectMenus .= "</select>";

        return $selectMenus;
    }
    private function generateUpdatePopupForm(): string
    {
        $isEditRequested = isset($_GET['editId']);

        if (!$isEditRequested) {
            return '';
        }

        $paymentId = intval($_GET['editId']);
        $payment = $this->paymentsRepository->findById($paymentId);


        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/PaymentPageController.php?PaymentLists'>";
        $form .= "<input type='hidden' name='paymentId' value='" . $payment->getReservationId() . "'>";
        $currency = $this->currenciesRepository->findById($payment->getCurrencyId());
       
        $form .=  $this->generateCurrecySelectMenu($currency->getId());

        $form .= "<label for='price'>Price:</label>";
        $form .= "<input type='text' name='price' id='price' value='" . $payment->getPrice() . "'>";
        $form .= '<label for="paymentDate" class="form-label">Payment Date</label>';
        $form .= "<input type='date' class='form-control' name='paymentDate' id='paymentDate' value=" . $payment->getPaymentDate() . ">";
        $form .= "<br>";
        $form .= "<input type='submit' name='update' value='update'>";
        $form .= "<input type='submit' name='cancel' value='cancel'>";
        $form .= "</form>";
        $form .= "</div>";

        $form .= "<script>";
        $form .= "document.getElementById('overlay').style.display = 'block';";
        $form .= "document.getElementById('form-container').style.display = 'block';";
        $form .=  "document.getElementById('submitBTN').value = 'Edit Phone';";
        $form .=  "document.getElementById('newNumber').style.display = 'block';";
        $form .= "</script>";

        return $form;
    }

    private function generateDeletePopup(): string
    {
        $isEditRequested = isset($_GET['deleteId']);

        if (!$isEditRequested) {
            return '';
        }

        $paymentId = intval($_GET['deleteId']);
        $payment = $this->paymentsRepository->findById($paymentId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/PaymentPageController.php?PaymentLists'>";
        $form .= "<input type='hidden' name='reservationId' value='" . $payment->getReservationId() . "'>";
        $form .= '<p class="text-center">Are you sure to delete this payment?</p>';
        $form .= "<input type='submit' name='delete' value='delete'>";
        $form .= "<input type='submit' name='cancel' value='cancel'>";
        $form .= "</form>";
        $form .= "</div>";

        $form .= "<script>";
        $form .= "document.getElementById('overlay').style.display = 'block';";
        $form .= "document.getElementById('form-container').style.display = 'block';";
        $form .=  "document.getElementById('submitBTN').value = 'Edit Phone';";
        $form .=  "document.getElementById('newNumber').style.display = 'block';";
        $form .= "</script>";

        return $form;
    }

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $reservationId = htmlspecialchars($_POST['reservationId']);
        $currencyId = htmlspecialchars($_POST['currencyId']);
        $price = htmlspecialchars($_POST['price']);
        $paymentDate = htmlspecialchars($_POST['paymentDate']);

        $this->paymentsRepository->create(intval($reservationId), intval($currencyId), floatval($price), $paymentDate);

        header("Location: ../Controllers/PaymentPageController.php?PaymentLists");
    }

    private function showAllPayments(): string
    {
        $payments = $this->paymentsRepository->getAllPayments();
        $result = '';

        $result .= "<div class='container mt-3'>";
        $result .= '<table class ="table table-primary table-striped">';
        $result .= "<thead>";
        $result .= "<tr>";
        $result .= "<th>Payments Name</th>";
        $result .= "<th>Price</th>";
        $result .= "<th>Currency</th>";
        $result .= "<th>Opstions</th>";
        $result .= "</tr>";
        $result .= "</thead>";

        foreach ($payments as $payment) {
            $result .= "<tbody>";
            $result .= "<tr>";
            $result .= "<td>" . $payment->getReservationId() . "</td>";
            $result .= "<td>" . $payment->getPrice() . "</td>";
            $currency = $this->currenciesRepository->findById($payment->getCurrencyId());
            $result .= "<td>" . $currency->getName() . "</td>";

            $result .= '<td><div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>';
            $result .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';

            $result .= "<li><a class='dropdown-item' href='../Controllers/PaymentPageController.php?PaymentLists&deleteId=" . $payment->getReservationId() . "'>Delete</a></li>";
            $result .= "<li><a class='dropdown-item' href='../Controllers/PaymentPageController.php?PaymentLists&editId=" . $payment->getReservationId() . "'>Edit</a></li>";
            $result .= '</ul></div></td>';

            $result .= "</tr>";
            $result .="</tbody>";
        }

        $result .= "</table>";
        $result .= "</div>";

        return $result;
    }

    private function update()
    {
        $isPostIncome = isset($_POST['update']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/PaymentPageController.php?PaymentLists");
            exit();
        }

        $reservationId = htmlspecialchars($_POST['paymentId']);
        $currencyId = htmlspecialchars($_POST['currencyId']);
        $price = htmlspecialchars($_POST['price']);
        $paymentDate = htmlspecialchars($_POST['paymentDate']);
        $this->paymentsRepository->update(intval($reservationId), intval($currencyId), floatval($price), $paymentDate);

        header("Location: ../Controllers/PaymentPageController.php?PaymentLists");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $paymentId = intval($_POST['reservationId']);
        $this->paymentsRepository->delete($paymentId);

        header("Location: ../Controllers/PaymentPageController.php?PaymentLists");
    }
}
