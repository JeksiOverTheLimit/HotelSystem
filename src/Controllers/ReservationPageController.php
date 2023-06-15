<?php

declare(strict_types=1);

include_once  "../Models/Cities.php";
include_once  "../Database/Repositories/RoomsRepository.php";
include_once "../Database/database.php";
include_once "../Models/Employees.php";
include_once "../Models/RoomTypes.php";
include_once "../Database/Repositories/RoomTypesRepository.php";
include_once "../Models/Rooms.php";
include_once "../Database/Repositories/ReservationsRepository.php";
include_once "../Models/Reservations.php";
include_once "../Models/ReservationStatus.php";
include_once "../Database/Repositories/ReservationStatusRepository.php";
include_once "../Models/Employees.php";
include_once "../Database/Repositories/EmployeesRepository.php";
include_once "../Models/Cities.php";
include_once "../Database/Repositories/CitiesRepository.php";
include_once "../Models/Countries.php";
include_once "../Database/Repositories/CountriesRepository.php";
include_once "../Models/Guests.php";
include_once "../Database/Repositories/GuestsRepository.php";
include_once "../Models/ReservationsGuests.php";
include_once "../Database/Repositories/ReservationsGuestsRepository.php";
include_once "../Models/RoomExtras.php";
include_once "../Database/Repositories/RoomExtrasRepository.php";
include_once "../Database/Repositories/RoomsExtrasMapRepository.php";
include_once "../Models/RoomExtrasMap.php";

$callController = new ReservationPageController();

class ReservationPageController
{
    private const VIEW_PATH = "../Views/Reservations.html";
    private const VIEW_LIST_PATH = "../Views/ReservationList.html";
    private RoomsRepository $roomsRepository;
    private RoomTypesRepository $roomTypesRepository;
    private ReservationsRepository $reservationsRepository;
    private ReservationStatusRepository $reservationStatusRepository;
    private ReservationsGuestsRepository $reservationGuestsRepository;
    private EmployeesRepository $employeesRepository;
    private CitiesRepository $citiesRepository;
    private CountriesRepository $countriesRepository;
    private GuestsRepository $guestsRepository;
    private RoomExtrasRepository $roomExtrasRepository;
    private RoomsExtrasMapRepository $roomExtrasMapRepository;

    public function __construct()
    {
        $this->roomsRepository = new RoomsRepository();
        $this->roomTypesRepository = new RoomTypesRepository();
        $this->reservationsRepository = new ReservationsRepository();
        $this->reservationStatusRepository = new ReservationStatusRepository();
        $this->employeesRepository = new EmployeesRepository();
        $this->citiesRepository = new CitiesRepository();
        $this->countriesRepository = new CountriesRepository();
        $this->guestsRepository = new GuestsRepository();
        $this->reservationGuestsRepository = new ReservationsGuestsRepository();
        $this->roomExtrasRepository = new RoomExtrasRepository();
        $this->roomExtrasMapRepository = new RoomsExtrasMapRepository();

        try {
            $this->create();
            $this->update();
            $this->delete();
        } catch (Exception $e) {
            echo "Грешка: " . $e->getMessage();
        }

        if(isset($_GET['Reservation'])){
        echo $this->showReservationPage();
        }

        if(isset($_GET['ReservationLists'])){
          echo $this->showReservationList();
        }
    }

    public function showReservationPage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $navigation = $this->generateNavigation();
        $employeeSelectMenu = $this->generateEmployeeSelectMenu();
        $roomSelectMenu = $this->generateRoomSelectMenu();
        $statusSelectMenu = $this->generateStatusSelectMenu();
        $allCities = $this->generateCitySelectMenu();
        $allCountries = $this->generateCountrySelectMenu();

        $result = sprintf($file, $navigation, $employeeSelectMenu, $roomSelectMenu, $statusSelectMenu, $allCities, $allCountries);

        return $result;
    }

    public function showReservationList(): string
    {
        $file = file_get_contents(self::VIEW_LIST_PATH);
        $navigation = $this->generateNavigation();
        $generateEditPopup = $this->generateUpdatePopupForm();
        $allReservations = $this->showAllReservations();
        $generateDeletePopup = $this->generateDeletePopup();

        $result = sprintf($file, $navigation, $allReservations, $generateEditPopup,$generateDeletePopup);

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

    private function generateEmployeeSelectMenu(int $selectedEmployeeId = null): string
    {
        $employees = $this->employeesRepository->getAllEmployees();

        $selectMenu = '<label for="employees" class="form-label">Employees</label>';
        $selectMenu .= '<select class="form-select" name="employeeId" id="employees">';

        foreach ($employees as $employee) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $employeeId = $employee->getId();
            $employeeName = $employee->getFirstName();
            $selected = ($selectedEmployeeId !== null && $selectedEmployeeId === $employeeId) ? "selected" : "";
            $option = sprintf($optionTemplate, $employeeId, $selected, $employeeName);
            $selectMenu .= $option;
        }

        $selectMenu .= "</select>";

        return $selectMenu;
    }

    private function generateRoomSelectMenu(int $selectedRoomId = null): string
    {
        $rooms = $this->roomsRepository->getAllRooms();

        $selectMenu = '<label for="roooms" class="form-label">Rooms</label>';
        $selectMenu .= '<select class="form-select" name="roomId" id="rooms">';

        foreach ($rooms as $room) {
            $optionTemplate = "<option value='%s' %s>%s-%s : %s</option>";
            $roomId = $room->getId();
            $roomName = $room->getNumber();
            $roomType = $this->roomTypesRepository->findById($room->getTypeId());
            $extra = $this->roomExtrasMapRepository->findByRoomId($room->getId());
            $roomExtra = $this->roomExtrasRepository->findById($extra->getExtraId());
            $selected = ($selectedRoomId !== null && $selectedRoomId === $roomId) ? "selected" : "";
            $option = sprintf($optionTemplate, $roomId, $selected, $roomName, $roomType->getName(), $roomExtra->getName());
            $selectMenu .= $option;
        }

        $selectMenu .= "</select>";

        return $selectMenu;
    }

    private function generateStatusSelectMenu(int $selectedStatusId = null): string
    {
        $reservationStatus = $this->reservationStatusRepository->getAllStatus();

        $selectMenu = '<label for="status" class="form-label">Status</label>';
        $selectMenu .= '<select class="form-select" name="statusId" id="status">';

        foreach ($reservationStatus as $status) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $statusId = $status->getId();
            $statusName = $status->getName();
            $selected = ($selectedStatusId !== null && $selectedStatusId === $statusId) ? "selected" : "";
            $option = sprintf($optionTemplate, $statusId, $selected, $statusName);
            $selectMenu .= $option;
        }

        $selectMenu .= "</select>";

        return $selectMenu;
    }

    private function generateCitySelectMenu(int $selectedCityId = null): string
    {
        $cities = $this->citiesRepository->getAllCities();

        $selectMenus = '<label for="cities" class="form-label">Cities</label>';
        $selectMenus .= '<select class="form-select" name="City" id="cities">';

        foreach ($cities as $city) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $cityId = $city->getId();
            $cityName = $city->getName();
            $selected = ($selectedCityId !== null && $selectedCityId === $cityId) ? "selected" : "";
            $option = sprintf($optionTemplate, $cityId, $selected, $cityName);
            $selectMenus .= $option;
        }

        $selectMenus .= "</select>";

        return $selectMenus;
    }

    private function generateCountrySelectMenu(int $selectedCountryId = null): string
    {
        $countries = $this->countriesRepository->getAllCountries();

        $selectMenu = '<label for="countries" class="form-label">Contries</label>';
        $selectMenu .= '<select class = "form-select" name="Country" id="countries">';

        foreach ($countries as $country) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $countryId = $country->getId();
            $countryName = $country->getName();
            $selected = ($selectedCountryId !== null && $selectedCountryId === $countryId) ? "selected" : "";
            $option = sprintf($optionTemplate, $countryId, $selected, $countryName);
            $selectMenu .= $option;
        }

        $selectMenu .= "</select>";

        return $selectMenu;
    }

    private function create(): ?string
    {

        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $employeeId = htmlspecialchars($_POST['employeeId']);
        $roomId = htmlspecialchars($_POST['roomId']);
        $startingDate = htmlspecialchars($_POST['startingDate']);
        $finalDate = htmlspecialchars($_POST['finalDate']);
        $statusId = htmlspecialchars($_POST['statusId']);
        $guestFirstName = htmlspecialchars($_POST['firstName']);
        $guestLastName = htmlspecialchars($_POST['lastName']);
        $guestEgn = htmlspecialchars($_POST['egn']);
        $guestPhone = htmlspecialchars($_POST['phoneNumber']);
        $guestCity = htmlspecialchars($_POST['City']);
        $guestCountry = htmlspecialchars($_POST['Country']);

        if ($startingDate > $finalDate) {
            throw new Exception("Няма как началната дата да е по голяма от крайната");
        }

        $guest = $this->guestsRepository->create($guestFirstName, $guestLastName, $guestEgn, $guestPhone, intval($guestCountry), intval($guestCity));
        $reservation = $this->reservationsRepository->create(intval($employeeId), intval($roomId), $startingDate, $finalDate, intval($statusId));
        $this->reservationGuestsRepository->create($reservation->getId(), $guest->getId());
        header("Location: ../Controllers/ReservationPageController.php?ReservationLists");
    }

    private function generateUpdatePopupForm(): string
    {
        $isEditRequested = isset($_GET['reservationId']);

        if (!$isEditRequested) {
            return '';
        }

        $reservationId = intval($_GET['reservationId']);
        $reservation = $this->reservationsRepository->findById($reservationId);
        $guestId = intval($_GET['guestId']);
        $guest = $this->guestsRepository->findById(($guestId));

        $form = "<div id='overlay'></div>";
        $form .= "<div id='form-container'>";
        $form .= "<form method='POST' action='../Controllers/ReservationPageController.php?ReservationLists'>";
        $form .= "<input type='hidden' name='reservationId' value='" . $reservationId . "'>";
        $form .= "<input type='hidden' name='guestId' value = '" . $guestId . "'>";
        $form .= $this->generateEmployeeSelectMenu($reservation->getEmployeeId());
        $form .= $this->generateRoomSelectMenu($reservation->getRoomId());
        $form .= $this->generateStatusSelectMenu($reservation->getStatusId());
        $form .= '<label for="startingDateEdit" class="form-label">Starting Date</label>';
        $form .= "<input type='date' class='form-control' name='startingDate' id='startingDateEdit' value=" . $reservation->getStartingDate() . ">";
        $form .= '<label for="finalDateEdit" class="form-label">Final Date</label>';
        $form .= "<input type='date' class='form-control' name='finalDate' id='finalDateEdit' value=" . $reservation->getFinalDate() . ">";
        $form .= '<fieldset id="guestContainer">';
        $form .= '<legend>Danni za Guest</legend>';
        $form .= '<div class="guest-fields mb-3">';
        $form .= '<label for="firstNameEdit" class="form-label ">First Name</label>';
        $form .= '<input type="text" class="form-control" name="firstName" id="firstNameEdit" value=' . $guest->getFirstName() . '>';
        $form .= '</div>';
        $form .= '<div class="guest-fields mb-3">';
        $form .= '<label for="lastNameEdit" class="form-label ">Last Name of Guest</label>';
        $form .= '<input type="text" class="form-control" name="lastName" id="lastNameEdit" value=' . $guest->getLastName() . '>';
        $form .= '</div>';
        $form .= '<div class="guest-fields mb-3">';
        $form .=   '<label for="egnEdit" class="form-label ">Egn of Guest</label>';
        $form .= '<input type="text" class="form-control" name="egn" id="egnEdit" value=' . $guest->getEgn() . '>';
        $form .= '</div>';
        $form .= '<div class="guest-fields mb-3">';
        $form .=  '<label for="phoneNumberEdit" class="form-label ">Phone of Guest</label>';
        $form .= '<input type="text" class="form-control" name="phoneNumber" id="phoneNumberEdit" value=' . $guest->getPhoneNumber() . '>';
        $form .=  '</div>';
        $form .= '<div class="mb-3">';
        $form .=  $this->generateCitySelectMenu($guest->getCityId());
        $form .= '</div>';
        $form .= '<div class="mb-3">';
        $form .= $this->generateCountrySelectMenu($guest->getCountryId());
        $form .= '</div>';
        $form .= '</fieldset>';
        $form .= '<button class="btn btn-primary" type="submit" name="update">Update</button>';
        $form .= '<button class="btn btn-secondary" type="submit" name="cancel">Cancel</button>';
        $form .= "</form>";
        $form .= "</div>";
        $form .= "</div>";

        $form .= "<script>";
        $form .= "document.getElementById('overlay').style.display = 'block';";
        $form .= "document.getElementById('form-container').style.display = 'block';";
        $form .=  "document.getElementById('submitBTN').value = 'Edit Phone';";
        $form .=  "document.getElementById('newNumber').style.display = 'block';";
        $form .= "</script>";

        return $form;
    }

    private function generateDeletePopup() : string
    {
        $isEditRequested = isset($_GET['deleteId']);

        if (!$isEditRequested) {
            return '';
        }

        $reservationId = intval($_GET['deleteId']);
        $reservation = $this->reservationsRepository->findById($reservationId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/ReservationPageController.php?ReservationLists'>";
        $form .= "<input type='hidden' name='reservationId' value='" . $reservation->getId() . "'>";
        $form .= '<p class="text-center">Are you sure to delete this reservation?</p>';
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

    private function showAllReservations(): string
    {
        $reservations = $this->reservationsRepository->getAllReservations();
        $result = '';

        $result .= '<div class="container mt-3">';
        $result .= '<table class ="table table-primary table-striped">';
        $result .= '<thead>';
        $result .= '<tr>';
        $result .= '<th>Employee FirstName</th>';
        $result .= '<th>Employee LastNamer</th>';
        $result .= '<th>Number Of room</th>';
        $result .= '<th>Starting Date</th>';
        $result .= '<th>Final Date</th>';
        $result .= '<th>Reservation status</th>';
        $result .= '<th>Guest Names</th>';
        $result .= '<th>Actions</th>';

        $result .= '</tr></thead>';

        foreach ($reservations as $reservation) {
            $result .= '<tbody>';
            $result .= '<tr>';
            $employee = $this->employeesRepository->findById($reservation->getEmployeeId());

            if ($employee != null) {
                $result .= "<td>" . $employee->getFirstName() . "</td>";
                $result .= "<td>" . $employee->getLastName() . "</td>";
            } else {
                $result .= "<td>Employee not exist</td>";
                $result .= "<td>Employee not exist</td>";
            }

            $rooms = $this->roomsRepository->findById($reservation->getRoomId());

            if($rooms != null){
            $result .= "<td>" . $rooms->getNumber() . "</td>";
            }
            else {
                $result .= "<td>Room not exist</td>";
            }

            $result .= "<td>" . $reservation->getStartingDate() . "</td>";
            $result .= "<td>" . $reservation->getFinalDate() . "</td>";
            $status = $this->reservationStatusRepository->findById($reservation->getStatusId());
            $result .= "<td>" . $status->getName() . "</td>";
            $reservationGuest  = $this->reservationGuestsRepository->findByReservationId($reservation->getId());
            $guest = $this->guestsRepository->findById($reservationGuest->getGuestId());
            $result .= "<td>" . $guest->getFirstName() . " " . $guest->getLastName() . "</td>";
            $result .= '<td><div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>';
            $result .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';

            $editReservation  = $this->reservationsRepository->findById($reservationGuest->getReservationId());
            $editGuest = $this->guestsRepository->findById($reservationGuest->getGuestId());

            $result .= "<li><a class='dropdown-item' href='../Controllers/ReservationPageController.php?ReservationLists&deleteId=" . $reservation->getId() . "'>Delete</a></li>";
            $result .= "<li><a class='dropdown-item' href='../Controllers/ReservationPageController.php?ReservationLists&reservationId=" . $editReservation->getId() . "&guestId=" . $editGuest->getId() . "'>Edit</a></li>";
            $result .= '</ul></div></td>';

            $result .= "</tr>";
            $result .= "</tbody>";
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
            header("Location: ../Controllers/ReservationPageController.php?ReservationLists");
            exit();
        }

        $reservationId = $_POST['reservationId'];
        $employeeId = htmlspecialchars($_POST['employeeId']);
        $roomId = htmlspecialchars($_POST['roomId']);
        $startingDate = htmlspecialchars($_POST['startingDate']);
        $finalDate = htmlspecialchars($_POST['finalDate']);
        $statusId = htmlspecialchars($_POST['statusId']);

        $guestId = $_POST['guestId'];
        $guestFirstName = htmlspecialchars($_POST['firstName']);
        $guestLastName = htmlspecialchars($_POST['lastName']);
        $guestEgn = htmlspecialchars($_POST['egn']);
        $guestPhone = htmlspecialchars($_POST['phoneNumber']);
        $guestCity = htmlspecialchars($_POST['City']);
        $guestCountry = htmlspecialchars($_POST['Country']);


        $this->reservationsRepository->update(intval($reservationId), intval($employeeId), intval($roomId), $startingDate, $finalDate, intval($statusId));
        $this->guestsRepository->update(intval($guestId), $guestFirstName, $guestLastName, $guestEgn, $guestPhone, intval($guestCountry), intval($guestCity));

        header("Location: ../Controllers/ReservationPageController.php?ReservationLists");
    }

    private function delete()
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/ReservationPageController.php?ReservationLists");
            exit();
        }

        $reservationId = intval($_POST['reservationId']);
        $reservationMapId = $this->reservationGuestsRepository->findByReservationId($reservationId);
        $this->reservationGuestsRepository->delete($reservationMapId->getId());
        $this->reservationsRepository->delete($reservationId);

        header("Location: ../Controllers/ReservationPageController.php?ReservationLists");
    }
}
