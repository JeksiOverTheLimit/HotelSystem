<?php

declare(strict_types=1);

include_once "../Models/Payment.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/PaymentRepository.php";
include_once "../Models/Currency.php";
include_once "../Database/Repositories/CurrencyRepository.php";
include_once "../Models/Reservation.php";
include_once "../Database/Repositories/ReservationRepository.php";

$payment = new PaymentPageController();

class PaymentPageController
{
    private const VIEW_PATH = "../Views/Payment.html";
    private const VIEW_LIST_PATH = "../Views/PaymentLists.html";
    private PaymentRepository $paymentsRepository;
    private CurrencyRepository $currenciesRepository;
    private ReservationRepository $reservationsRepository;

    public function __construct()
    {
        $this->paymentsRepository = new PaymentRepository();
        $this->currenciesRepository = new CurrencyRepository();
        $this->reservationsRepository = new ReservationRepository();

        switch (true) {
            case isset($_POST['submit']):
                $this->create();
                break;
            case isset($_POST['update']):
                $this->update();
                break;
            case isset($_POST['delete']):
                $this->delete();
                break;
            case isset($_GET['Payment']):
                echo $this->showPaymentPage();
                break;
            case isset($_GET['PaymentLists']):
                echo $this->showPaymentLists();
                break;
                case isset($_GET['Edit']):
                    echo $this->showUpdatePage();
                    break;
        }
        
    }

    private function showPaymentPage()
    {
        $reservationOptions = $this->generateReservationsSelectMenu();
        $currencyOptions = $this->generateCurrecySelectMenu();
        require_once '../Views/Payment.php';
    }

    private function showUpdatePage(){
        $paymentId = $_GET['editId'];
        $payment = $this->paymentsRepository->findById(intval($paymentId));
        $reservationOptions = $this->generateReservationsSelectMenu($payment->getReservationId());
        $currencyOptions = $this->generateCurrecySelectMenu($payment->getCurrencyId());
        require_once '../Views/payment_form.php';
    }

    private function showPaymentLists()
    {
        $payments = $this->paymentsRepository->getAllPayments();
        $paymentss = [];
        foreach($payments as $payment){
            $paymentId = $payment->getReservationId();
            $paymentPrice = $payment->getPrice();
            $currencyForPayment = $this->currenciesRepository->findById($payment->getCurrencyId());
            $currency = $currencyForPayment->getName();
            $paymentDate = $payment->getPaymentDate();

            $paymentss[] = [
                'id' => $paymentId,
                'price' => $paymentPrice,
                'currency' => $currency,
                'date' => $paymentDate
            ];
        }
        require_once "../Views/PaymentLists.php";
    }

    private function generateReservationsSelectMenu(int $selectedReservationId = null) : ?array
    {
        $reservations = $this->reservationsRepository->getAllReservations();

        $selectMenu = [];

        foreach ($reservations as $reservation) {
            $reservationId = $reservation->getId();
            $reservationStartingDate = $reservation->getStartingDate();
            $reservationFinalDate = $reservation->getFinalDate();
            $selected = ($selectedReservationId !== null && $selectedReservationId === $reservationId) ? "selected" : "";
            $selectMenu[] = [
                'id' => $reservationId,
                'startingDate' => $reservationStartingDate,
                'finalDate' => $reservationFinalDate,
                'selected' => $selected
            ];
        }
        return $selectMenu;
}


    private function generateCurrecySelectMenu(int $selectedCurrencyId = null): ?array
    {
        $currencies = $this->currenciesRepository->getAllCurrencies();
        $selectMenu = [];

        foreach ($currencies as $currency) {
            $currencyId = $currency->getId();
            $currencyName = $currency->getName();
            $selected = ($selectedCurrencyId !== null && $selectedCurrencyId === $currencyId) ? "selected" : "";
            
            $selectMenu[] = [
                'id' => $currencyId,
                'name' => $currencyName,
                'selected' => $selected
            ];
        }

        return $selectMenu;
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

    private function update()
    {
        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/PaymentPageController.php?PaymentLists");
            exit();
        }

        $reservationId = htmlspecialchars($_POST['reservationId']);
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
