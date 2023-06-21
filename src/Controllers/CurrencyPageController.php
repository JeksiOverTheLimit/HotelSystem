<?php

declare(strict_types=1);


include_once "../Models/Currencies.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/CurrenciesRepository.php";

$callController = new CurrencyPageController();

class CurrencyPageController
{
    private const VIEW_PATH = "../Views/currencies.html";
    private const VIEW_LIST_PATH = "../Views/CurrencyList.html";
    private CurrenciesRepository $currenciesRepository;

    public function __construct()
    {
        $this->currenciesRepository = new CurrenciesRepository();

        $this->create();
        $this->update();
        $this->delete();

        if (isset($_GET['Currency'])){
        echo $this->showCurrencyPage();
        }

        if(isset($_GET['CurrencyList'])){
            echo $this->showCurrencyList();
        }


    }

    public function showCurrencyPage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $result = sprintf($file);

        return $result;
    }

    public function showCurrencyList(): string
    {
        $file = file_get_contents(self::VIEW_LIST_PATH);
        $allCurrencies = $this->showAllCurrencies();
        $generateEditPopup = $this->generateUpdatePopupForm();
        $generateDeletePopup = $this->generateDeletePopup();

        $result = sprintf($file, $allCurrencies, $generateEditPopup, $generateDeletePopup);

        return $result;
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

    private function generateUpdatePopupForm(): string
    {
        $isEditRequested = isset($_GET['editId']);

        if (!$isEditRequested) {
            return '';
        }

        $currencyId = intval($_GET['editId']);
        $currency = $this->currenciesRepository->findById($currencyId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/CurrencyPageController.php?CurrencyList'>";
        $form .= "<input type='hidden' name='currencyId' value='" . $currency->getId() . "'>";
        $form .= "<label for='firstName'>Currency Name:</label>";
        $form .= "<input type='text' name='name' value='" . $currency->getName() . "'>";
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

    private function showAllCurrencies(): string
    {
        $currencies = $this->currenciesRepository->getAllCurrencies();
        $result = '';
        $result .= '<div class="container mt-3">';
        $result .= '<table class ="table table-primary table-striped">';
        $result .= '<thead>';
        $result .= '<tr>';
        $result .= '<th>Currency Name</th>';
        $result .= '<th>Options</th>';
        $result .= '</tr>';
        $result .='</thead>';

        foreach ($currencies as $currency) {
            $result .= '<tbody>';
            $result .= '<tr>';
            $result .= '<td>' . $currency->getName() . '</td>';

            $result .= '<td><div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>';
            $result .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
            $result .= "<li><a class='dropdown-item' href='../Controllers/CurrencyPageController.php?CurrencyList&deleteId=" . $currency->getId() . "'>Delete</a></li>";
            $result .= "<li><a class='dropdown-item' href='../Controllers/CurrencyPageController.php?CurrencyList&editId=" . $currency->getId(). "'>Edit</a></li>";
            $result .= '</ul></div></td>';
            
            $result .= "</tr>";
            $result .='</tbody>';
        }

        $result .= "</table>";
        $result .= "</div>";

        return $result;
    }

    private function generateDeletePopup() : string
    {
        $isEditRequested = isset($_GET['deleteId']);

        if (!$isEditRequested) {
            return '';
        }

        $currencyId = intval($_GET['deleteId']);
        $currency = $this->currenciesRepository->findById($currencyId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/CurrencyPageController.php?CurrencyList'>";
        $form .= "<input type='hidden' name='currencyId' value='" . $currency->getId() . "'>";
        $form .= '<p class="text-center">Are you sure to delete this currency?</p>';
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
        $this->currenciesRepository->delete($currencyId);

        header("Location: ../Controllers/CurrencyPageController.php?CurrencyList");
    }

}
