<?php

declare(strict_types=1);

include_once  "../Models/Cities.php";
include_once  "../Database/Repositories/CitiesRepository.php";
include_once "../Models/Countries.php";
include_once "../Database/Repositories/CountriesRepository.php";
include_once "../Database/database.php";
include_once "../Models/Employees.php";
include_once "../Database/Repositories/EmployeesRepository.php";

$callController = new EployeePageController();

class EployeePageController
{
    private const VIEW_PATH = "../Views/employees.html";
    private const VIEW_LIST_PATH = "../Views/EmployeeList.html";
    private const NAV_PATH = "../Views/Navigations.html";
    private CitiesRepository $citiesRepository;
    private CountriesRepository $countriesRepository;
    private EmployeesRepository $employeesRepository;

    public function __construct()
    {
        $this->citiesRepository = new CitiesRepository();
        $this->countriesRepository = new CountriesRepository();
        $this->employeesRepository = new EmployeesRepository();
        
        $this->create();
        $this->update();
        $this->delete();

        if (isset($_GET['Employees'])) {
            echo $this->showEmployeePage();
        }

        if (isset($_GET['EmployeeLists'])) {
            echo $this->showEmployeeList();
        }
    }

    public function showEmployeePage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $citySelectMenu = $this->generateCitySelectMenu();
        $countrySelectMenu = $this->generateCountrySelectMenu();

        $result = sprintf($file, $countrySelectMenu, $citySelectMenu);

        return $result;
    }

    public function showEmployeeList(): string
    {
        $file = file_get_contents(self::VIEW_LIST_PATH);
        $allEmployes = $this->showAllEmployees();
        $generateEditPopup = $this->generateUpdatePopupForm();
        $generateDeletePopup = $this->generateDeletePopup();

        $result = sprintf($file, $allEmployes, $generateEditPopup, $generateDeletePopup);

        return $result;
    }

    private function generateCitySelectMenu(int $selectedCityId = null, int $selectedCountryId = null)
    {
        $countryId = isset($_GET['countryId']) ? intval($_GET['countryId']) : $selectedCountryId;
        $cities = $this->citiesRepository->getCitiesByCountryId($countryId);
    
        $selectMenus = '';
    
        foreach ($cities as $city) {
            $optionTemplate = "<option value='%s'%s>%s</option>";
            $cityId = $city->getId();
            $cityName = $city->getName();
            $selected = ($selectedCityId !== null && $selectedCityId === $cityId) ? " selected" : "";
            $option = sprintf($optionTemplate, $cityId, $selected, $cityName);
            $selectMenus .= $option;
        }
    
        if (!isset($_GET['countryId'])) {
            return $selectMenus;
        } else {
            echo $selectMenus;
            exit();
        }
    }
    

    private function generateCountrySelectMenu(int $selectedCountryId = null): string
    {
        $countries = $this->countriesRepository->getAllCountries();
        $selectMenu = '';
        
        if ($selectedCountryId === null) {
            $placeholderOption = "<option value='' selected>Изберете държава</option>";
            $selectMenu .= $placeholderOption;
        }
    
        foreach ($countries as $country) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $countryId = $country->getId();
            $countryName = $country->getName();
            $selected = ($selectedCountryId !== null && $selectedCountryId === $countryId) ? "selected" : "";
            $option = sprintf($optionTemplate, $countryId, $selected, $countryName);
            $selectMenu .= $option;
        }
    
        return $selectMenu;
    }
    

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $firstName = htmlspecialchars($_POST['firstName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $egn = htmlspecialchars($_POST['egn']);
        $phoneNumber = htmlspecialchars($_POST['phone']);
        $countryId = intval($_POST['Country']);
        $cityId = intval($_POST['City']);

        $this->employeesRepository->create($firstName, $lastName, $egn, $phoneNumber, $countryId, $cityId);

        header("Location: ../Controllers/EmployeePageController.php?EmployeeLists");
    }

    private function generateUpdatePopupForm(): string
    {
        $isEditRequested = isset($_GET['editId']);

        if (!$isEditRequested) {
            return '';
        }

        $employeeId = intval($_GET['editId']);
        $employee = $this->employeesRepository->findById($employeeId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/EmployeePageController.php?EmployeeLists'>";
        $form .= "<input type='hidden' name='employeeId' value='" . $employee->getId() . "'>";
        $form .= "<label for='firstName'>First Name:</label>";
        $form .= "<input type='text' name='firstName' value='" . $employee->getFirstName() . "'>";
        $form .= "<br>";
        $form .= "<label for='lastName'>Last Name:</label>";
        $form .= "<input type='text' name='lastName' value='" . $employee->getLastName() . "'>";
        $form .= "<br>";
        $form .= "<label for='egn'>EGN:</label>";
        $form .= "<input type='text' name='egn' value='" . $employee->getEgn() . "'>";
        $form .= "<br>";
        $form .= "<label for='phone'>Phone Number:</label>";
        $form .= "<input type='text' name='phone' value='" . $employee->getPhoneNumber() . "'>";
        $form .= "<br>";
        $form .= '<div class="mb-3">';
        $form .= '<label for="countries" class="form-label">Contries</label>';
        $form .= '<select  class = "form-select" id="countries" name="Country" onchange="fetchCitiesByCountry()">';
        $form .= $this->generateCountrySelectMenu($employee->getCountryId());
        $form .= '</select>';
        $form .= '</div>';
        $form .= '<div class="mb-3">';
        $form .= '<label for="cities" class="form-label">Cities</label>';
        $form .= '<select class="form-select" id="cities" name="City">';
        $form .= $this->generateCitySelectMenu($employee->getCityId(), $employee->getCountryId());
        $form .= '</select>';
        $form .= '</div>';
        $form .= "<input type='submit' name='update' value='update'>";
        $form .= "<input type='submit' name='cancel' value='cancel'>";
        $form .= "</form>";
        $form .= "</div>";

        $form .= "<script>";
        $form .= "document.getElementById('overlay').style.display = 'block';";
        $form .= "document.getElementById('form-container').style.display = 'block';";
        $form .=  "document.getElementById('submitBTN').value = 'Edit Phone';";
        $form .=  "document.getElementById('newNumber').style.display = 'block';";

        $form .= ' function fetchCitiesByCountry() { ';
        $form .= 'const countryId = document.getElementById("countries").value; ';
        $form .= ' fetch(`../Controllers/EmployeePageController.php?Employees&countryId=${countryId}`)';
        $form .= ' .then(response => response.text()) ';
        $form .= ' .then(result => { ';
        $form .= 'document.getElementById("cities").innerHTML = result;
                console.log(document.getElementById("cities").innerHTML = result);
            })
            .catch(error => console.error(error)); 
    }';
        $form .= "</script>";

        return $form;
    }

    private function generateDeletePopup(): string
    {
        $isEditRequested = isset($_GET['deleteId']);

        if (!$isEditRequested) {
            return '';
        }

        $employeeId = intval($_GET['deleteId']);
        $employee = $this->employeesRepository->findById($employeeId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/EmployeePageController.php?EmployeeLists'>";
        $form .= "<input type='hidden' name='employeeId' value='" . $employee->getId() . "'>";
        $form .= '<p class="text-center">Are you sure to delete this employee?</p>';
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

    private function showAllEmployees(): string
    {
        $employees = $this->employeesRepository->getAllEmployees();
        $result = '';

        $result .= '<div class="container mt-3">';
        $result .= '<table class ="table table-primary table-striped">';
        $result .= '<thead>';
        $result .= "<tr>";
        $result .= "<th>First Name</th>";
        $result .= "<th>Last Name</th>";
        $result .= "<th>Egn</th>";
        $result .= "<th>Phone Number</th>";
        $result .= "<th>Country</th>";
        $result .= "<th >City</th>";
        $result .= "<th >Actions</th>";
        $result .= "</tr>";
        $result .= '</thead>';

        foreach ($employees as $employee) {
            $result .= '<tbody>';
            $result .= "<tr>";
            $result .= "<td>" . $employee->getFirstName() . "</td>";
            $result .= "<td>" . $employee->getLastName() . "</td>";
            $result .= "<td>" . $employee->getEgn() . "</td>";
            $result .= "<td>" . $employee->getPhoneNumber() . "</td>";

            $country = $this->countriesRepository->findById($employee->getCountryId());
            $result .= "<td>" . $country->getName() . "</td>";

            $city = $this->citiesRepository->findById($employee->getCityId());
            $result .= "<td>" . $city->getName() . "</td>";

            $result .= '<td><div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>';
            $result .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
            $result .= "<li><a class='dropdown-item' href='../Controllers/EmployeePageController.php?EmployeeLists&deleteId=" . $employee->getId() . "'>Delete</a></li>";
            $result .= "<li><a class='dropdown-item' href='../Controllers/EmployeePageController.php?EmployeeLists&editId=" . $employee->getId() . "'>Edit</a></li>";
            $result .= "</ul></td>";

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
            header("Location: ../Controllers/EmployeePageController.php?EmployeeLists");
            exit();
        }

        $employeeId = intval($_POST['employeeId']);
        $firstName = htmlspecialchars($_POST['firstName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $egn = htmlspecialchars($_POST['egn']);
        $phoneNumber = htmlspecialchars($_POST['phone']);
        $countryId = intval($_POST['Country']);
        $cityId = intval($_POST['City']);

        $this->employeesRepository->update($employeeId, $firstName, $lastName, $egn, $phoneNumber, $countryId, $cityId);

        header("Location: ../Controllers/EmployeePageController.php?EmployeeLists");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/EmployeePageController.php?EmployeeLists");
            exit();
        }

        $employeeId = intval($_POST['employeeId']);
        $this->employeesRepository->delete($employeeId);

        header("Location: ../Controllers/EmployeePageController.php?EmployeeLists");
    }
}
