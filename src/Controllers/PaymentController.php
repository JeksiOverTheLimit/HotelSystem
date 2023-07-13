<?php

declare(strict_types=1);

include_once "../Models/Payment.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/PaymentRepository.php";
include_once "../Models/Currency.php";
include_once "../Database/Repositories/CurrencyRepository.php";
include_once "../Models/Reservation.php";
include_once "../Database/Repositories/ReservationRepository.php";
include_once "../Services/PaymentValidationService.php";

$payment = new PaymentController();

class PaymentController
{
    private PaymentRepository $paymentRepository;
    private CurrencyRepository $currencyRepository;
    private ReservationRepository $reservationRepository;
    private PaymentValidationService $paymentValidationService;

    public function __construct()
    {
        $this->paymentRepository = new PaymentRepository();
        $this->currencyRepository = new CurrencyRepository();
        $this->reservationRepository = new ReservationRepository();
        $this->paymentValidationService = new PaymentValidationService();

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
            case isset($_GET['Payment']) || isset($_GET['reservationId']):
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
        $reservationId = isset($_GET['reservationId']) ? intval($_GET['reservationId']) : null;
        $reservationOptions = isset($_GET['reservationId']) ? $this->generateReservationsSelectMenu($reservationId) : $this->generateReservationsSelectMenu();
        $price = isset($_GET['reservationId']) ? $this->reservationRepository->findById($reservationId)->getPrice() : '';
        $currencyOptions = $this->generateCurrecySelectMenu();
        require_once '../Views/payment.php';
    }

    private function showUpdatePage()
    {
        $paymentId = $_GET['editId'];
        $payment = $this->paymentRepository->findById(intval($paymentId));
        $reservationOptions = $this->generateReservationsSelectMenu($payment->getReservationId());
        $currencyOptions = $this->generateCurrecySelectMenu($payment->getCurrencyId());
        require_once '../Views/payment_form.php';
    }

    private function showPaymentLists()
    {
        $payments = $this->paymentRepository->getAllPayments();
        $paymentss = [];
        foreach ($payments as $payment) {
            $paymentId = $payment->getReservationId();
            $paymentPrice = $payment->getPrice();
            $currencyForPayment = $this->currencyRepository->findById($payment->getCurrencyId());
            $currency = $currencyForPayment->getName();
            $paymentDate = $payment->getPaymentDate();

            $paymentss[] = [
                'id' => $paymentId,
                'price' => $paymentPrice,
                'currency' => $currency,
                'date' => $paymentDate
            ];
        }
        require_once "../Views/payment_list.php";
    }

    private function generateReservationsSelectMenu(int $selectedReservationId = null): ?array
    {
        $reservations = $this->reservationRepository->getAllReservations();

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
        $currencies = $this->currencyRepository->getAllCurrencies();
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

        $this->validateInputField($reservationId, $currencyId, $price, $paymentDate);
        $this->paymentRepository->create(intval($reservationId), intval($currencyId), floatval($price), $paymentDate);

        header("Location: ../Controllers/PaymentController.php?PaymentLists");
    }

    private function validateInputField($reservationId, $currencyId, $price, $paymentDate){
         $this->paymentValidationService->validateReservationId($reservationId);
         $this->paymentValidationService->validateCurrencyId($currencyId);
         $this->paymentValidationService->validatePrice($price);
         $this->paymentValidationService->validatePaymentDate($paymentDate);
    }

    private function update()
    {
        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/PaymentController.php?PaymentLists");
            exit();
        }

        $reservationId = htmlspecialchars($_POST['reservationId']);
        $currencyId = htmlspecialchars($_POST['currencyId']);
        $price = htmlspecialchars($_POST['price']);
        $paymentDate = htmlspecialchars($_POST['paymentDate']);

        $this->validateInputField($reservationId, $currencyId, $price, $paymentDate);
        $this->paymentRepository->update(intval($reservationId), intval($currencyId), floatval($price), $paymentDate);

        header("Location: ../Controllers/PaymentController.php?PaymentLists");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $paymentId = intval($_POST['reservationId']);
        $this->paymentRepository->delete($paymentId);

        header("Location: ../Controllers/PaymentController.php?PaymentLists");
    }
}
