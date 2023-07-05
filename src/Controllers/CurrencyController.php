<?php

declare(strict_types=1);


include_once "../Models/Currency.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/CurrencyRepository.php";
include_once "../Database/Repositories/PaymentRepository.php";
include_once "../Models/Payment.php";

$callController = new CurrencyController();

class CurrencyController
{
    private const VIEW_PATH = "../Views/currencies.html";
    private const VIEW_LIST_PATH = "../Views/CurrencyList.html";
    private CurrencyRepository $currencyRepository;
    private PaymentRepository $paymentRepository;

    public function __construct()
    {
        $this->currencyRepository = new CurrencyRepository();
        $this->paymentRepository = new PaymentRepository();

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
            case isset($_GET['Currency']):
                echo $this->showCurrencyPage();
                break;
            case isset($_GET['CurrencyList']):
                echo $this->showAllCurrencies();
                break;
            case isset($_GET['Edit']):
                echo $this->showUpdatePage();
                break;
        }
    }

    private function showCurrencyPage()
    {
        require_once "../Views/currencies.php";
    }

    private function showUpdatePage()
    {
        $currencyId = $_GET['editId'];
        $currency = $this->currencyRepository->findById(intval($currencyId));
        require_once "../Views/currency_form.php";
    }

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $name = htmlspecialchars($_POST['name']);

        $this->currencyRepository->create($name);

        header("Location: ../Controllers/CurrencyController.php?CurrencyList");
    }


    private function showAllCurrencies(): void
    {
        $currencies = $this->currencyRepository->getAllCurrencies();
        require_once '../Views/CurrencyList.php';
    }

    private function update()
    {
        $isPostIncome = isset($_POST['update']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/CurrencyController.php?CurrencyList");
            exit();
        }

        $currencyId = intval($_POST['currencyId']);
        $name = htmlspecialchars($_POST['name']);

        $this->currencyRepository->update($currencyId, $name);

        header("Location: ../Controllers/CurrencyController.php?CurrencyList");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/CurrencyController.php?CurrencyList");
            exit();
        }

        $currencyId = intval($_POST['currencyId']);
        $payments = $this->paymentRepository->findByCurrencyId($currencyId);

        foreach ($payments as $payment) {
            $paymentId = $payment->getReservationId();
            $this->paymentRepository->delete($paymentId);
        }
        $this->currencyRepository->delete($currencyId);

        header("Location: ../Controllers/CurrencyController.php?CurrencyList");
    }
}
