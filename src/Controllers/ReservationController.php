<?php

declare(strict_types=1);

include_once  "../Database/Repositories/RoomRepository.php";
include_once "../Database/database.php";
include_once "../Models/RoomType.php";
include_once "../Database/Repositories/RoomTypeRepository.php";
include_once "../Models/Room.php";
include_once "../Database/Repositories/ReservationRepository.php";
include_once "../Models/Reservation.php";
include_once "../Models/ReservationStatus.php";
include_once "../Database/Repositories/ReservationStatusRepository.php";
include_once "../Models/Employee.php";
include_once "../Database/Repositories/EmployeeRepository.php";
include_once "../Models/City.php";
include_once "../Database/Repositories/CityRepository.php";
include_once "../Models/Country.php";
include_once "../Database/Repositories/CountryRepository.php";
include_once "../Models/Guest.php";
include_once "../Database/Repositories/GuestRepository.php";
include_once "../Models/ReservationGuest.php";
include_once "../Database/Repositories/ReservationGuestRepository.php";
include_once "../Models/RoomExtra.php";
include_once "../Database/Repositories/RoomExtraRepository.php";
include_once "../Database/Repositories/RoomExtraMapRepository.php";
include_once "../Models/RoomExtraMap.php";
include_once "../Controllers/SelectMenuHelper.php";
include_once "../Database/Repositories/PaymentRepository.php";
include_once "../Models/Payment.php";
include_once "../Services/ReservationValidationService.php";
include_once "../Services/UserValidationService.php";

$callController = new ReservationController();

class ReservationController
{
    private RoomRepository $roomRepository;
    private RoomTypeRepository $roomTypeRepository;
    private ReservationRepository $reservationRepository;
    private ReservationStatusRepository $reservationStatusRepository;
    private ReservationGuestRepository $reservationGuestRepository;
    private EmployeeRepository $employeeRepository;
    private CityRepository $cityRepository;
    private CountryRepository $countryRepository;
    private GuestRepository $guestRepository;
    private RoomExtraRepository $roomExtraRepository;
    private RoomExtraMapRepository $roomExtraMapRepository;
    private SelectMenuHelper $selectMenuHelper;
    private PaymentRepository $paymentRepository;
    private ReservationValidationService $reservationValidationService;
    private UserValidationService $userValidationService;

    public function __construct()
    {
        $this->roomRepository = new RoomRepository();
        $this->roomTypeRepository = new RoomTypeRepository();
        $this->reservationRepository = new ReservationRepository();
        $this->reservationStatusRepository = new ReservationStatusRepository();
        $this->employeeRepository = new EmployeeRepository();
        $this->cityRepository = new CityRepository();
        $this->countryRepository = new CountryRepository();
        $this->guestRepository = new GuestRepository();
        $this->reservationGuestRepository = new ReservationGuestRepository();
        $this->roomExtraRepository = new RoomExtraRepository();
        $this->roomExtraMapRepository = new RoomExtraMapRepository();
        $this->selectMenuHelper = new SelectMenuHelper();
        $this->paymentRepository = new PaymentRepository();
        $this->reservationValidationService = new ReservationValidationService();
        $this->userValidationService = new UserValidationService();

        switch (true) {
            case isset($_POST['submit']):
                try {
                    $this->create();
                } catch (Exception $e) {
                    echo "Грешка: " . $e->getMessage();
                }
                break;
            case isset($_POST['update']):
                try {
                    $this->update();
                } catch (Exception $e) {
                    echo "Грешка: " . $e->getMessage();
                }
                break;
            case isset($_POST['delete']):
                try {
                    $this->delete();
                } catch (Exception $e) {
                    echo "Грешка: " . $e->getMessage();
                }
                break;
            case isset($_GET['Reservation']):
                echo $this->showReservationPage();
                break;
            case isset($_GET['ReservationLists']):
                echo $this->showReservationList();
                break;
            case isset($_GET['Edit']):
                echo $this->showUpdatePage();
                break;
            case isset($_GET['guestPrivatePageId']):
                try {
                    echo $this->showPrivatePage();
                } catch (Exception $e) {
                    echo "Грешка: " . $e->getMessage();
                }
                break;
        }
    }

    private function showPrivatePage()
    {
        $guestId = intval($_GET['guestPrivatePageId']);

        $guestReservationsDate = [];
        $guestInformations = [];

        $findedGuest = $this->guestRepository->findById($guestId);

        $guestData = $this->generateGuestDate($findedGuest);

        foreach ($guestData as $guest) {
            $guestInformations[] = [
                'firstName' => $guest['firstName'],
                'lastName' => $guest['lastName'],
                'egn' => $guest['egn'],
                'phone' => $guest['phone'],
                'country' => $guest['country'],
                'city' => $guest['city']
            ];
        }

        $reservations = $this->generateReservationForGuest($findedGuest);

        foreach ($reservations as $reservation) {
            $guestReservationsDate[] = [
                'startingDate' => $reservation['startingDate'],
                'finalDate' => $reservation['finalDate'],
                'number' => $reservation['number']
            ];
        }
        require_once '../Views/guest_privatepage.php';
    }

    private function showReservationPage(): void
    {
        $employeeSelectMenu = $this->generateEmployeeSelectMenu();
        $roomSelectMenu = $this->generateRoomSelectMenu();
        $statusSelectMenu = $this->generateStatusSelectMenu();
        $countrySelectMenu = $this->selectMenuHelper->generateCountrySelectMenu();
        $citySelectMenu = $this->selectMenuHelper->generateCitySelectMenu();
        

        require_once "../Views/reservation.php";
    }

    private function showReservationList(): void
    {
        $allReservations = $this->filterHandle();
        $countrySelectMenu = $this->selectMenuHelper->generateCountrySelectMenu();
        $roomSelectMenu = $this->generateRoomSelectMenu();

        $reservations = [];
        foreach ($allReservations as $reservation) {
            $reservationId = $reservation->getId();
            $reservationStartingDate = $reservation->getStartingDate();
            $reservationFinalDate = $reservation->getFinalDate();

            $employee = $this->employeeRepository->findById($reservation->getEmployeeId());
            $employeeFirstName = $employee->getFirstName();
            $employeeLastName = $employee->getlastName();

            $rooms = $this->roomRepository->findById($reservation->getRoomId());
            $roomNumber = $rooms->getNumber();

            $status = $this->reservationStatusRepository->findById($reservation->getStatusId());
            $roomStatus = $status->getName();

            $reservationGuest  = $this->reservationGuestRepository->findByReservationId($reservation->getId());
            $roomGuest = $this->guestRepository->findById($reservationGuest->getGuestId());
            $guestId = $roomGuest->getId();
            $roomGuestName = $roomGuest->getFirstName() . " " . $roomGuest->getLastName();

            $guestCountry = $this->countryRepository->findById($roomGuest->getCountryId());
            $guestCountryName = $guestCountry->getName();

            $guestCity = $this->cityRepository->findById($roomGuest->getCityId());
            $guestCityName =  $guestCity->getName();

            $startDate = new DateTime($reservationStartingDate);
            $endDate = new DateTime($reservationFinalDate);
            $interval = $startDate->diff($endDate);
            $overnightStays = $interval->days;

            $price = $reservation->getPrice();

            $reservations[] = [
                'id' => $reservationId,
                'startingDate' => $reservationStartingDate,
                'finalDate' => $reservationFinalDate,
                'employeeFirstName' => $employeeFirstName,
                'employeeLastName' => $employeeLastName,
                'roomNumber' => $roomNumber,
                'roomStatus' => $roomStatus,
                'guestId' => $guestId,
                'guestName' => $roomGuestName,
                'guestCountry' => $guestCountryName,
                'guestCity' => $guestCityName,
                'overnightStays' => $overnightStays,
                'price' => $price
            ];
        }
        require_once "../Views/reservation_list.php";
    }

    private function showUpdatePage(): void
    {
        $reservationId = $_GET['reservationId'];
        $reservation = $this->reservationRepository->findById(intval($reservationId));
        $reservationStartingDate = $reservation->getStartingDate();
        $reservationFinalDate = $reservation->getFinalDate();

        $employeeSelectMenu = $this->generateEmployeeSelectMenu($reservation->getEmployeeId());
        $roomSelectMenu = $this->generateRoomSelectMenu($reservation->getRoomId());
        $statusSelectMenu = $this->generateStatusSelectMenu($reservation->getStatusId());

        $reservationGuest  = $this->reservationGuestRepository->findByReservationId($reservation->getId());
        $roomGuest = $this->guestRepository->findById($reservationGuest->getGuestId());
        $guestId = $roomGuest->getId();
        $guestFirstName = $roomGuest->getFirstName();
        $guestLastName = $roomGuest->getLastName();
        $guestEGN = $roomGuest->getEgn();
        $guestPhone = $roomGuest->getPhoneNumber();

        $countrySelectMenu = $this->selectMenuHelper->generateCountrySelectMenu($roomGuest->getCountryId());
        $citySelectMenu = $this->selectMenuHelper->generateCitySelectMenu($roomGuest->getCityId(), $roomGuest->getCountryId());

        $price = $reservation->getPrice();
        require_once '../Views/reservation_form.php';
    }

    private function generateEmployeeSelectMenu(int $selectedEmployeeId = null): ?array
    {
        $employees = $this->employeeRepository->getAllEmployees();
        $selectMenu = [];

        foreach ($employees as $employee) {
            $employeeId = $employee->getId();
            $employeeName = $employee->getFirstName();
            $selected = ($selectedEmployeeId !== null && $selectedEmployeeId === $employeeId) ? "selected" : "";

            $selectMenu[] = [
                'id' => $employeeId,
                'name' => $employeeName,
                'selected' => $selected
            ];
        }

        return $selectMenu;
    }

    private function generateGuestDate(Guest $guest): ?array
    {
        $guestInformation = [];

        $country = $this->countryRepository->findById($guest->getCountryId());
        $city = $this->cityRepository->findById($guest->getCityId());

        if ($guest === null) {
            throw new Exception('Nqmash mqsto tuk');
        }

        $guestInformation[] = [
            'id' => $guest->getId(),
            'firstName' => $guest->getFirstName(),
            'lastName' => $guest->getLastName(),
            'phone' => $guest->getPhoneNumber(),
            'egn' => $guest->getEgn(),
            'country' => $country->getName(),
            'city' => $city->getName()
        ];

        return  $guestInformation;
    }

    private function generateReservationForGuest(Guest $guest): ?array
    {
        $reservationDate = [];
        $reservationIds = $this->reservationGuestRepository->getAllReservationGuestsByGuestId($guest->getId());

        foreach ($reservationIds as $reservationId) {
            $reservation = $this->reservationRepository->findById($reservationId->getReservationId());
            $room = $this->roomRepository->findById($reservation->getRoomId());
            $reservationDate[] = [
                'startingDate' => $reservation->getStartingDate(),
                'finalDate' => $reservation->getFinalDate(),
                'number' => $room->getNumber(),
            ];
        }

        return $reservationDate;
    }

    private function generateRoomSelectMenu(int $selectedRoomId = null): ?array
    {
        $rooms = $this->roomRepository->getAllRooms();
        $selectMenu = [];

        foreach ($rooms as $room) {
            $roomId = $room->getId();
            $roomName = $room->getNumber();
            $roomTypes = $this->roomTypeRepository->findById($room->getTypeId());
            $type = $roomTypes->getName();

            $extra = $this->roomExtraMapRepository->findByRoomId($room->getId());
            $roomExtra = $this->roomExtraRepository->findById($extra->getExtraId());
            $extraName = $roomExtra->getName();

            $selected = ($selectedRoomId !== null && $selectedRoomId === $roomId) ? "selected" : "";

            $selectMenu[] = [
                'id' => $roomId,
                'name' => $roomName,
                'types' => $type,
                'extra' => $extraName,
                'selected' => $selected
            ];
        }
        return $selectMenu;
    }

    private function generateStatusSelectMenu(int $selectedStatusId = null): ?array
    {
        $reservationStatus = $this->reservationStatusRepository->getAllStatus();
        $selectMenu = [];

        foreach ($reservationStatus as $status) {
            $statusId = $status->getId();
            $statusName = $status->getName();
            $selected = ($selectedStatusId !== null && $selectedStatusId === $statusId) ? "selected" : "";


            $selectMenu[] = [
                'id' => $statusId,
                'name' => $statusName,
                'selected' => $selected
            ];
        }

        return $selectMenu;
        
    }

    private function create(): ?string
    {

        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $employeeId = intval(htmlspecialchars($_POST['employeeId']));
        $roomId = intval(htmlspecialchars($_POST['roomId']));
        $startingDate = htmlspecialchars($_POST['startingDate']);
        $finalDate = htmlspecialchars($_POST['finalDate']);
        $statusId = intval(htmlspecialchars($_POST['statusId']));
        $price = intval(htmlspecialchars($_POST['price']));
        $guestFirstName = htmlspecialchars($_POST['firstName']);
        $guestLastName = htmlspecialchars($_POST['lastName']);
        $guestEgn = htmlspecialchars($_POST['egn']);
        $guestPhone = htmlspecialchars($_POST['phoneNumber']);
        $guestCity = isset($_POST['City']) ? intval(htmlspecialchars($_POST['City'])) : null;
        $guestCountry = intval(htmlspecialchars($_POST['Country']));
        
        $this->validateReservationInputFields($employeeId, $roomId, $startingDate, $finalDate, $statusId, $price);
        $this->validateGuestInputFields($guestFirstName, $guestLastName, $guestEgn, $guestPhone, $guestCountry, $guestCity);

        $foundGuest = $this->guestRepository->findByEGN($guestEgn);

        $reservation = $this->reservationRepository->create($employeeId, $roomId, $startingDate, $finalDate, $statusId, $price);

        if ($foundGuest !== null) {
            $this->reservationGuestRepository->create($reservation->getId(), $foundGuest->getId());
        } else {
            $guest = $this->guestRepository->create($guestFirstName, $guestLastName, $guestEgn, $guestPhone, $guestCountry, $guestCity);
            $this->reservationGuestRepository->create($reservation->getId(), $guest->getId());
        }

        header("Location: ../Controllers/ReservationController.php?ReservationLists");
    }

    private function validateReservationInputFields($employeeId, $roomId, $startingDate, $finalDate, $statusId, $price): void {
        $this->reservationValidationService->validateEmployee($employeeId);
        $this->reservationValidationService->validateRoom($roomId);
        $this->reservationValidationService->validateDate($startingDate, $finalDate);
        $this->reservationValidationService->validateStatus($statusId);
        $this->reservationValidationService->validatePrice($price);
    }

    private function validateGuestInputFields($guestFirstName, $guestLastName, $guestEgn, $guestPhone, $guestCountry, $guestCity): void
    {
      $this->userValidationService->validateName($guestFirstName);
      $this->userValidationService->validateName($guestLastName);
      $this->userValidationService->validateEgn($guestEgn);
      $this->userValidationService->validatePhone($guestPhone);
      $this->userValidationService->validateCountry($guestCountry);
    }

    private function filterHandle(): ?array
    {
        $reservationStartingDate = isset($_POST['startingDate']) ? $_POST['startingDate'] : '';
        $reservationFinalDate = isset($_POST['finalDate']) ? $_POST['finalDate'] : '';
        $roomId = isset($_POST['roomId']) ? intval($_POST['roomId']) : null;
        $countryId = isset($_POST['Country']) ? intval($_POST['Country']) : null;

       $reservations = $this->reservationRepository->filterReservations($reservationStartingDate,  $reservationFinalDate,  $roomId,  $countryId);
       return $reservations;
    }

    private function update()
    {
        $isPostIncome = isset($_POST['update']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/ReservationController.php?ReservationLists");
            exit();
        }
        $reservationId = $_POST['reservationId'];
        $employeeId = intval(htmlspecialchars($_POST['employeeId']));
        $roomId = intval(htmlspecialchars($_POST['roomId']));
        $startingDate = htmlspecialchars($_POST['startingDate']);
        $finalDate = htmlspecialchars($_POST['finalDate']);
        $statusId = intval(htmlspecialchars($_POST['statusId']));
        $price = intval(htmlspecialchars($_POST['price']));

        $guestId = $_POST['guestId'];
        $guestFirstName = htmlspecialchars($_POST['firstName']);
        $guestLastName = htmlspecialchars($_POST['lastName']);
        $guestEgn = htmlspecialchars($_POST['egn']);
        $guestPhone = htmlspecialchars($_POST['phoneNumber']);
        $guestCountry = intval(htmlspecialchars($_POST['Country']));
        $guestCity = isset($_POST['City']) ? intval(htmlspecialchars($_POST['City'])) : null;
        
        $this->validateReservationInputFields($employeeId, $roomId, $startingDate, $finalDate, $statusId, $price);
        $this->validateGuestInputFields($guestFirstName, $guestLastName, $guestEgn, $guestPhone, $guestCountry, $guestCity);
        
        $this->reservationRepository->update(intval($reservationId), intval($employeeId), intval($roomId), $startingDate, $finalDate, intval($statusId), $price);
        $this->guestRepository->update(intval($guestId), $guestFirstName, $guestLastName, $guestEgn, $guestPhone, intval($guestCountry), intval($guestCity));

        header("Location: ../Controllers/ReservationController.php?ReservationLists");
    }

    private function delete()
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/ReservationController.php?ReservationLists");
            exit();
        }

        $idForDelete = intval($_GET['deleteId']);
        $reservationById = $this->reservationGuestRepository->findByReservationId($idForDelete);
        $foundedGuest = $reservationById->getGuestId();
        $reservationMapIds = $this->reservationGuestRepository->getAllReservationGuestsByGuestId($foundedGuest);

        foreach ($reservationMapIds as $reservationMapId) {
            $guestId = $reservationMapId->getGuestId();
            $reservationId = $reservationMapId->getReservationId();
            $this->reservationGuestRepository->deleteByGuestId($reservationMapId->getGuestId());
            $this->guestRepository->delete($guestId);
            $this->paymentRepository->delete($reservationId);
            $this->reservationRepository->delete($reservationId);
        }

        header("Location: ../Controllers/ReservationController.php?ReservationLists");
    }
}
