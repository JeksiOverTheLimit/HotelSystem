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
include_once "../Services/UserValidationService.php";

$callController = new EployeeController();

class EployeeController
{
    private CityRepository $cityRepository;
    private CountryRepository $countryRepository;
    private EmployeeRepository $employeeRepository;
    private SelectMenuHelper $selectMenuHelper;
    private ReservationRepository $reservationRepository;
    private ReservationGuestRepository  $reservationGuestRepository;
    private UserValidationService $userValidationService;

    public function __construct()
    {
        $this->cityRepository = new CityRepository();
        $this->countryRepository = new CountryRepository();
        $this->employeeRepository = new EmployeeRepository();
        $this->selectMenuHelper = new SelectMenuHelper();
        $this->reservationRepository = new ReservationRepository();
        $this->reservationGuestRepository = new ReservationGuestRepository();
        $this->userValidationService = new UserValidationService();

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

    private function showEmployeeCreatePage()
    {
        $selectedCountryId = null;
        $countryOptions = $this->selectMenuHelper->generateCountrySelectMenu($selectedCountryId);
        $cityOptions = $this->selectMenuHelper->generateCitySelectMenu();
        require_once '../Views/employee.php';
    }

    private function showUpdatePage()
    {
        $employeeId = $_GET['editId'];
        $employee = $this->employeeRepository->findById(intval($employeeId));
        $selectedCountryId = $employee->getCountryId();
        $countryOptions = $this->selectMenuHelper->generateCountrySelectMenu($selectedCountryId);
        $selectedCityId = $employee->getCityId();
        $cityOptions = $this->selectMenuHelper->generateCitySelectMenu($selectedCityId, $selectedCountryId);
        require_once '../Views/employee_form.php';
    }

    private function showEmployeeListPage()
    {
        $employees = $this->employeeRepository->getAllEmployees();
        $employeess = [];
        foreach ($employees as $employee) {
            $employeeId = $employee->getId();
            $employeeFirstName = $employee->getFirstName();
            $employeeLastName = $employee->getLastName();
            $employeeEGN = $employee->getEgn();
            $employeePhoneNumber = $employee->getPhoneNumber();
            $countryId = $this->countryRepository->findById($employee->getCountryId());
            $country = $countryId->getName();
            $cityId = $this->cityRepository->findById($employee->getCityId());
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

        require_once '../Views/employee_list.php';
    }


    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $names = htmlspecialchars($_POST['names']);
        $nameParts = explode(' ', $names);
        $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        $egn = htmlspecialchars($_POST['egn']);
        $phoneNumber = htmlspecialchars($_POST['phone']);
        $countryId = intval($_POST['Country']);
        $cityId = isset($_POST['City']) ? intval($_POST['City']) : 0;
        $email = isset($_POST['email']) ? $_POST['email'] : '';

        $this->validateInputField($firstName,$lastName,$egn,$phoneNumber,$countryId, $email);
        $this->employeeRepository->create($firstName, $lastName, $egn, $phoneNumber, $countryId, $cityId, $email);

        header("Location: ../Controllers/EmployeeController.php?EmployeeLists");
    }

    private function validateInputField($firstName, $lastName, $egn, $phoneNumber, $countryId, $email): void
    {
      $this->userValidationService->validateName($firstName);
      $this->userValidationService->validateName($lastName);
      $this->userValidationService->validateEgn($egn);
      $this->userValidationService->validatePhone($phoneNumber);
      $this->userValidationService->validateCountry($countryId);
      $this->userValidationService->validateEmail($email);
    }

    private function update()
    {
        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/EmployeeController.php?EmployeeLists");
            exit();
        }

        $employeeId = intval($_POST['employeeId']);
        $names = htmlspecialchars($_POST['names']);
        $nameParts = explode(' ', $names);
        $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        $egn = htmlspecialchars($_POST['egn']);
        $phoneNumber = htmlspecialchars($_POST['phone']);
        $countryId = intval($_POST['Country']);
        $cityId = isset($_POST['City']) ? intval($_POST['City']) : 0;
        $email = isset($_POST['email']) ? $_POST['email'] : '';

        $this->validateInputField($firstName,$lastName,$egn,$phoneNumber,$countryId, $email);
        $this->employeeRepository->update($employeeId, $firstName, $lastName, $egn, $phoneNumber, $countryId, $cityId, $email);

        header("Location: ../Controllers/EmployeeController.php?EmployeeLists");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/EmployeeController.php?EmployeeLists");
            exit();
        }

        $employeeId = intval($_GET['deleteId']);
        $reservations = $this->reservationRepository->findByEmployeeId($employeeId);
        foreach ($reservations as $reservation) {
            $reservationId = $reservation->getId();
            $this->reservationGuestRepository->deleteByReservationId($reservationId);
        }


        $this->reservationRepository->deleteByEmployeeId($employeeId);

        $this->employeeRepository->delete($employeeId);

        header("Location: ../Controllers/EmployeeController.php?EmployeeLists");
    }
}
