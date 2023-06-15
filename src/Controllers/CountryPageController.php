<?php

declare(strict_types=1);

include_once "../Models/Countries.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/CountriesRepository.php";

$callController = new CountryPageController();

class CountryPageController
{
    private const VIEW_PATH = "../Views/Country.html";
    private const VIEW_LIST_PATH = "../Views/CountryList.html";
    private CountriesRepository $countriesRepository;

    public function __construct()
    {
        $this->countriesRepository = new CountriesRepository();

        $this->create();
        $this->update();
        $this->delete();

        if (isset($_GET['Country'])) {
            echo $this->showCountryPage();
        }

        if (isset($_GET['CountryList'])) {
            echo $this->showCountryList();
        }
    }

    public function showCountryPage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $navigation = $this->generateNavigation();

        $result = sprintf($file, $navigation);

        return $result;
    }

    public function showCountryList(): string
    {
        $file = file_get_contents(self::VIEW_LIST_PATH);
        $navigation = $this->generateNavigation();
        $allCountries = $this->showAllCountries();
        $generateEditPopup = $this->generateUpdatePopupForm();
        $generateDeletePopup = $this->generateDeletePopup();

        $result = sprintf($file, $navigation, $allCountries, $generateEditPopup, $generateDeletePopup);
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


    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $name = htmlspecialchars($_POST['name']);

        $this->countriesRepository->create($name);

        header("Location: ../Controllers/CountryPageController.php?CountryList");
    }

    private function generateUpdatePopupForm(): string
    {
        $isEditRequested = isset($_GET['editId']);

        if (!$isEditRequested) {
            return '';
        }

        $countryId = intval($_GET['editId']);
        $country = $this->countriesRepository->findById($countryId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/CountryPageController.php?CountryList'>";
        $form .= "<input type='hidden' name='countryId' value='" . $country->getId() . "'>";
        $form .= "<label for='firstName'>Country Name:</label>";
        $form .= "<input type='text' name='name' value='" . $country->getName() . "'>";
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

        $countryId = intval($_GET['deleteId']);
        $country = $this->countriesRepository->findById($countryId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/CountryPageController.php?CountryList'>";
        $form .= "<input type='hidden' name='countryId' value='" . $country->getId() . "'>";
        $form .= '<p class="text-center">Are you sure to delete this country?</p>';
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

    private function showAllCountries(): string
    {
        $countries = $this->countriesRepository->getAllCountries();
        $result = '';

        $result .= '<div class="container mt-3">';
        $result .= '<table class ="table table-primary table-striped">';
        $result .= '<thead>';
        $result .= "<tr>";
        $result .= "<th>Currency Name</th>";
        $result .= "<th>Options</th>";
        $result .= "</tr>";
        $result .= "</thead>";

        foreach ($countries as $country) {
            $result .= '<tbody>';
            $result .= "<tr>";
            $result .= "<td>" . $country->getName() . "</td>";

            $result .= '<td><div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>';
            $result .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
            $result .= "<li><a class='dropdown-item' href='../Controllers/CountryPageController.php?CountryList&deleteId=" . $country->getId() . "'>Delete</a></li>";
            $result .= "<li><a class='dropdown-item' href='../Controllers/CountryPageController.php?CountryList&editId=" . $country->getId() . "'>Edit</a></li>";
            $result .= '</ul></div></td>';

            $result .= "</tr>";
            $result .= '</tbody>';
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
            header("Location: ../Controllers/CountryPageController.php?CountryList");
            exit();
        }

        $countryId = intval($_POST['countryId']);
        $name = htmlspecialchars($_POST['name']);

        $this->countriesRepository->update($countryId, $name);

        header("Location: ../Controllers/CountryPageController.php?CountryList");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/CountryPageController.php?CountryList");
            exit();
        }

        $countryId = intval($_POST['countryId']);
        $this->countriesRepository->delete($countryId);

        header("Location: ../Controllers/CountryPageController.php?CountryList");
    }
}
