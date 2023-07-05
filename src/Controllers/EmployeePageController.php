<?php

declare(strict_types=1);

include_once  "../Models/City.php";
include_once  "../Database/Repositories/CityRepository.php";
include_once "../Models/Country.php";
include_once "../Database/Repositories/CountryRepository.php";
include_once "../Database/database.php";
include_once "../Models/Employee.php";
include_once "../Database/Repositories/EmployeeRepository.php";
include_once "../Models/RoomExtraMap.php";
include_once "../Controllers/SelectMenuHelper.php";
include_once "../Database/Repositories/ReservationRepository.php";
include_once "../Models/Reservation.php";
include_once "../Database/Repositories/ReservationGuestRepository.php";
include_once "../Models/ReservationGuest.php";

$callController = new EployeePageController();

class EployeePageController
{
    private const VIEW_PATH = "../Views/employees.html";
    private const VIEW_LIST_PATH = "../Views/EmployeeList.html";
    private const NAV_PATH = "../Views/Navigations.html";
    private CityRepository $citiesRepository;
    private CountryRepository $countriesRepository;
    private EmployeeRepository $employeesRepository;
    private SelectMenuHelper $selectMenuHelper;
    private ReservationRepository $reservationRepository;
    private ReservationGuestRepository  $reservationGuestsRepository;

    public function __construct()
    {
        $this->citiesRepository = new CityRepository();
        $this->countriesRepository = new CountryRepository();
        $this->employeesRepository = new EmployeeRepository();
        $this->selectMenuHelper = new SelectMenuHelper(); 
        $this->reservationRepository = new ReservationRepository();
        $this->reservationGuestsRepository = new ReservationGuestRepository();
        
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
            case isset($_GET['Employees']):
                echo $this->showEmployeeCreatePage();
                break;
            case isset($_GET['EmployeeLists']):
                echo $this->showEmployeeListPage();
                break;
                case isset($_GET['Edit']):
                    echo $this->showUpdatePage();
                    break;
        }        
    }

    private function showEmployeeCreatePage(){
        $selectedCountryId = null;
        $countryOptions = $this->selectMenuHelper->generateCountrySelectMenu($selectedCountryId);
        $cityOptions = $this->selectMenuHelper->generateCitySelectMenu();
        require_once '../Views/employees.php';
    }

    private function showUpdatePage()
    {
        $employeeId = $_GET['editId'];
        $employee = $this->employeesRepository->findById(intval($employeeId));
        $selectedCountryId = $employee->getCountryId();
        $countryOptions = $this->selectMenuHelper->generateCountrySelectMenu($selectedCountryId);
        $selectedCityId = $employee->getCityId();
        $cityOptions = $this->selectMenuHelper->generateCitySelectMenu($selectedCityId, $selectedCountryId);
        require_once '../Views/employee_form.php';
    }
    

    private function showEmployeeListPage()
{
    $employees = $this->employeesRepository->getAllEmployees();
    $employeess = [];
    foreach ($employees as $employee) {
        $employeeId = $employee->getId();
        $employeeFirstName = $employee->getFirstName();
        $employeeLastName = $employee->getLastName();
        $employeeEGN = $employee->getEgn();
        $employeePhoneNumber = $employee->getPhoneNumber();
        $countryId = $this->countriesRepository->findById($employee->getCountryId());
        $country = $countryId->getName();
        $cityId = $this->citiesRepository->findById($employee->getCityId());
        $city = $cityId->getName();

        $employeess[] = [
            'id' => $employeeId,
            'firstName' => $employeeFirstName,
            'lastName' => $employeeLastName,
            'egn' => $employeeEGN,
            'phoneNumber' => $employeePhoneNumber,
            'country' => $country,
            'city' => $city
        ];
    }

    require_once '../Views/EmployeeList.php';
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

    private function update()
    {
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
    
        $employeeId = intval($_GET['deleteId']);
        $reservations = $this->reservationRepository->findByEmployeeId($employeeId);
        foreach ($reservations as $reservation) {
            $reservationId = $reservation->getId();
            $this->reservationGuestsRepository->deleteByReservationId($reservationId);
        }
    
        
        $this->reservationRepository->deleteByEmployeeId($employeeId);
    
        $this->employeesRepository->delete($employeeId);
    
        header("Location: ../Controllers/EmployeePageController.php?EmployeeLists");
    }
    

}
