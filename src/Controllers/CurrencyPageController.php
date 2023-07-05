<?php

declare(strict_types=1);


include_once "../Models/Currency.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/CurrencyRepository.php";
include_once "../Database/Repositories/PaymentRepository.php";
include_once "../Models/Payment.php";

$callController = new CurrencyPageController();

class CurrencyPageController
{
    private const VIEW_PATH = "../Views/currencies.html";
    private const VIEW_LIST_PATH = "../Views/CurrencyList.html";
    private CurrencyRepository $currenciesRepository;
    private PaymentRepository $paymentsRepository;

    public function __construct()
    {
        $this->currenciesRepository = new CurrencyRepository();
        $this->paymentsRepository = new PaymentRepository();

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
        $currency = $this->currenciesRepository->findById(intval($currencyId));
        require_once "../Views/currency_form.php";
    }

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $name = htmlspecialchars($_POST['name']);

        $this->currenciesRepository->create($name);

        header("Location: ../Controllers/CurrencyPageController.php?CurrencyList");
    }


    private function showAllCurrencies(): void
    {
        $currencies = $this->currenciesRepository->getAllCurrencies();
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
            header("Location: ../Controllers/CurrencyPageController.php?CurrencyList");
            exit();
        }

        $currencyId = intval($_POST['currencyId']);
        $name = htmlspecialchars($_POST['name']);

        $this->currenciesRepository->update($currencyId, $name);

        header("Location: ../Controllers/CurrencyPageController.php?CurrencyList");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/CurrencyPageController.php?CurrencyList");
            exit();
        }

        $currencyId = intval($_POST['currencyId']);
        $payments = $this->paymentsRepository->findByCurrencyId($currencyId);

        foreach ($payments as $payment) {
            $paymentId = $payment->getReservationId();
            $this->paymentsRepository->delete($paymentId);
        }
        $this->currenciesRepository->delete($currencyId);

        header("Location: ../Controllers/CurrencyPageController.php?CurrencyList");
    }
}
