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
    private PaymentsRepository $paymentsRepository;
    private CurrenciesRepository $currenciesRepository;
    private ReservationsRepository $reservationsRepository;

    public function __construct()
    {
        $this->paymentsRepository = new PaymentsRepository();
        $this->currenciesRepository = new CurrenciesRepository();
        $this->reservationsRepository = new ReservationsRepository();

        echo $this->showCountryPage();
        $this->create();
        $this->update();
        $this->delete();
    }

    public function showCountryPage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $navigation = $this->generateNavigation();
        $allPayments = $this->showAllPayments();
        $allReservations = $this->generateReservationsSelectMenu();
        $allCurrencies = $this->generateCurrecySelectMenu();
        $generateEditPopup = $this->generateUpdatePopupForm();

        $result = sprintf($file, $navigation,$allReservations,$allCurrencies ,$allPayments, $generateEditPopup);

        return $result;
    }

    private function generateNavigation(): string
    {
        $nav = '';
        $nav .= "<nav class='navbar navbar-expand-sm bg-dark navbar-dark'>";
        $nav .= "<div class='container-fluid'>";
        $nav .= '<ul class="navbar-nav me-auto mb-2 mb-lg-0">';
        $nav .= "<li class = 'nav-item'><a class='nav-link active' href='HomePageController.php'>Home</a></li>";

        $nav .= '<li class="nav-item dropdown">';
        $nav .= '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Employee</a>';
        $nav .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
        $nav .= '<li><a class="dropdown-item" href="EmployeePageController.php?Employees">Create</a></li>';
        $nav .= '<li><a class="dropdown-item" href="EmployeePageController.php?EmployeeLists">Lists</a></li>';
        $nav .= '</ul></li>';

        $nav .= '<li class="nav-item dropdown">';
        $nav .= '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Payments</a>';
        $nav .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
        $nav .= '<li><a class="dropdown-item" href="PaymentPageController.php?Payment">Create</a></li>';
        $nav .= '<li><a class="dropdown-item" href="PaymentPageController.php?PaymentLists">Lists</a></li>';
        $nav .= '</ul></li>';

        $nav .= '<li class="nav-item dropdown">';
        $nav .= '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Currency</a>';
        $nav .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
        $nav .= '<li><a class="dropdown-item" href="CurrencyPageController.php?Currency">Create</a></li>';
        $nav .= '<li><a class="dropdown-item" href="CurrencyPageController.php?CurrencyList">Lists</a></li>';
        $nav .= '</ul></li>';

        $nav .= '<li class="nav-item dropdown">';
        $nav .= '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Rooms</a>';
        $nav .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
        $nav .= '<li><a class="dropdown-item" href="RoomPageController.php?Rooms">Create</a></li>';
        $nav .= '<li><a class="dropdown-item" href="RoomPageController.php?RoomLists">Lists</a></li>';
        $nav .= '</ul></li>';

        $nav .= '<li class="nav-item dropdown">';
        $nav .= '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Country</a>';
        $nav .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
        $nav .= '<li><a class="dropdown-item" href="CountryPageController.php?Country">Create</a></li>';
        $nav .= '<li><a class="dropdown-item" href="CountryPageController.php?CountryList">Lists</a></li>';
        $nav .= '</ul></li>';

        $nav .= '<li class="nav-item dropdown">';
        $nav .= '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Reservations</a>';
        $nav .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
        $nav .= '<li><a class="dropdown-item" href="ReservationPageController.php?Reservation">Create</a></li>';
        $nav .= '<li><a class="dropdown-item" href="ReservationPageController.php?ReservationLists">Lists</a></li>';
        $nav .= '</ul></li>';

        $nav .= "</ul>";
        $nav .= "</div>";
        $nav .= "</nav>";

        return $nav;
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
            $option = sprintf($optionTemplate, $reservationId, $selected, $reservationId,$reservationStartingDate, $reservationFinalDate);
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
        $form .= "<form method='POST' action='../Controllers/CountryPageController.php'>";
        $form .= "<input type='hidden' name='countryId' value='" . $payment->getReservationId() . "'>";
        
        $currency = $this->currenciesRepository->findById($payment->getCurrencyId());
        $form .= "<label for='currency'>Currency</label>";
        $form .= "<input type='text' name='currency' id='currency' value='" . $currency->getName() . "'>";
        
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

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $name = htmlspecialchars($_POST['name']);

        $this->paymentsRepository->create($name);

        header("Location: ../Controllers/CountryPageController.php");
    }

    private function showAllPayments(): string
    {
        $payments = $this->paymentsRepository->getAllPayments();
        $result = '';

        $result .= "<div class='container mt-3'>";
        $result .= "<table class ='table table-striped'>";
        $result .= "<tr>";
        $result .= "<th>Currency Name</th>";
        $result .= "</tr>";

        foreach ($payments as $payment) {
            $result .= "<tr>";
            $result .= "<td>" . $payment->getReservationId() . "</td>";
            $result .= "<td>" . $payment->getPrice() . "</td>";
            $result .= "<td>" . $payment->getCurrencyId() . "</td>";

            $result .= "<td>";
            $result .= "<a href='../Controllers/CountryPageController.php?deleteId=" . $payment->getReservationId() . "'>Delete</a>";
            $result .= " | ";
            $result .= "<a href='../Controllers/CountryPageController.php?editId=" . $payment->getReservationId() . "'>Edit</a>";
            $result .= "</td>";

            $result .= "</tr>";
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
            header("Location: ../Controllers/CurrencyPageController.php");
            exit();
        }

        $countryId = intval($_POST['countryId']);
        $name = htmlspecialchars($_POST['name']);

        $this->paymentsRepository->update($countryId, $name);

        header("Location: ../Controllers/CountryPageController.php");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_GET['deleteId']);

        if (!$isPostIncome) {
            return '';
        }

        $paymentId = intval($_GET['deleteId']);
        $this->paymentsRepository->delete($paymentId);

        header("Location: ../Controllers/CountryPageController.php");
    }
}
