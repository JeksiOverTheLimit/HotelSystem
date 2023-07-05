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

$callController = new ReservationPageController();

class ReservationPageController
{
    private const VIEW_PATH = "../Views/Reservations.html";
    private const VIEW_LIST_PATH = "../Views/ReservationList.html";
    private RoomRepository $roomsRepository;
    private RoomTypeRepository $roomTypesRepository;
    private ReservationRepository $reservationsRepository;
    private ReservationStatusRepository $reservationStatusRepository;
    private ReservationGuestRepository $reservationGuestsRepository;
    private EmployeeRepository $employeesRepository;
    private CityRepository $citiesRepository;
    private CountryRepository $countriesRepository;
    private GuestRepository $guestsRepository;
    private RoomExtraRepository $roomExtrasRepository;
    private RoomExtraMapRepository $roomExtrasMapRepository;
    private SelectMenuHelper $selectMenuHelper;
    private PaymentRepository $paymentsRepository;

    public function __construct()
    {
        $this->roomsRepository = new RoomRepository();
        $this->roomTypesRepository = new RoomTypeRepository();
        $this->reservationsRepository = new ReservationRepository();
        $this->reservationStatusRepository = new ReservationStatusRepository();
        $this->employeesRepository = new EmployeeRepository();
        $this->citiesRepository = new CityRepository();
        $this->countriesRepository = new CountryRepository();
        $this->guestsRepository = new GuestRepository();
        $this->reservationGuestsRepository = new ReservationGuestRepository();
        $this->roomExtrasRepository = new RoomExtraRepository();
        $this->roomExtrasMapRepository = new RoomExtraMapRepository();
        $this->selectMenuHelper = new SelectMenuHelper();
        $this->paymentsRepository = new PaymentRepository();

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

        $findedGuest = $this->guestsRepository->findById($guestId);

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

        require_once "../Views/Reservations.php";
    }

    private function showReservationList(): void
    {
        $allReservations = $this->reservationsRepository->getAllReservations();
        $reservations = [];
        foreach ($allReservations as $reservation) {
            $reservationId = $reservation->getId();
            $reservationStartingDate = $reservation->getStartingDate();
            $reservationFinalDate = $reservation->getFinalDate();

            $employee = $this->employeesRepository->findById($reservation->getEmployeeId());
            $employeeFirstName = $employee->getFirstName();
            $employeeLastName = $employee->getlastName();

            $rooms = $this->roomsRepository->findById($reservation->getRoomId());
            $roomNumber = $rooms->getNumber();

            $status = $this->reservationStatusRepository->findById($reservation->getStatusId());
            $roomStatus = $status->getName();

            $reservationGuest  = $this->reservationGuestsRepository->findByReservationId($reservation->getId());
            $roomGuest = $this->guestsRepository->findById($reservationGuest->getGuestId());
            $guestId = $roomGuest->getId();
            $roomGuestName = $roomGuest->getFirstName() . " " . $roomGuest->getLastName();

            $guestCountry = $this->countriesRepository->findById($roomGuest->getCountryId());
            $guestCountryName = $guestCountry->getName();

            $guestCity = $this->citiesRepository->findById($roomGuest->getCityId());
            $guestCityName =  $guestCity->getName();

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
                'guestCity' => $guestCityName
            ];
        }
        require_once "../Views/ReservationList.php";
    }

    private function showUpdatePage(): void
    {
        $reservationId = $_GET['reservationId'];
        $reservation = $this->reservationsRepository->findById(intval($reservationId));
        $reservationStartingDate = $reservation->getStartingDate();
        $reservationFinalDate = $reservation->getFinalDate();

        $employeeSelectMenu = $this->generateEmployeeSelectMenu($reservation->getEmployeeId());
        $roomSelectMenu = $this->generateRoomSelectMenu($reservation->getRoomId());
        $statusSelectMenu = $this->generateStatusSelectMenu($reservation->getStatusId());

        $reservationGuest  = $this->reservationGuestsRepository->findByReservationId($reservation->getId());
        $roomGuest = $this->guestsRepository->findById($reservationGuest->getGuestId());
        $guestId = $roomGuest->getId();
        $guestFirstName = $roomGuest->getFirstName();
        $guestLastName = $roomGuest->getLastName();
        $guestEGN = $roomGuest->getEgn();
        $guestPhone = $roomGuest->getPhoneNumber();

        $countrySelectMenu = $this->selectMenuHelper->generateCountrySelectMenu($roomGuest->getCountryId());
        $citySelectMenu = $this->selectMenuHelper->generateCitySelectMenu($roomGuest->getCityId(), $roomGuest->getCountryId());

        require_once '../Views/reservation_form.php';
    }

    private function generateEmployeeSelectMenu(int $selectedEmployeeId = null): ?array
    {
        $employees = $this->employeesRepository->getAllEmployees();
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

        $country = $this->countriesRepository->findById($guest->getCountryId());
        $city = $this->citiesRepository->findById($guest->getCityId());

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
        $reservationIds = $this->reservationGuestsRepository->getAllReservationGuestsByGuestId($guest->getId());

        foreach ($reservationIds as $reservationId) {
            $reservation = $this->reservationsRepository->findById($reservationId->getReservationId());
            $room = $this->roomsRepository->findById($reservation->getRoomId());
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
        $rooms = $this->roomsRepository->getAllRooms();
        $selectMenu = [];

        foreach ($rooms as $room) {
            $roomId = $room->getId();
            $roomName = $room->getNumber();
            $roomTypes = $this->roomTypesRepository->findById($room->getTypeId());
            $type = $roomTypes->getName();

            $extra = $this->roomExtrasMapRepository->findByRoomId($room->getId());
            $roomExtra = $this->roomExtrasRepository->findById($extra->getExtraId());
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

        $foundGuest = $this->guestsRepository->findByEGN($guestEgn);

        $reservation = $this->reservationsRepository->create(intval($employeeId), intval($roomId), $startingDate, $finalDate, intval($statusId));

        if ($foundGuest !== null) {
            $this->reservationGuestsRepository->create($reservation->getId(), $foundGuest->getId());
        } else {
            $guest = $this->guestsRepository->create($guestFirstName, $guestLastName, $guestEgn, $guestPhone, intval($guestCountry), intval($guestCity));
            $this->reservationGuestsRepository->create($reservation->getId(), $guest->getId());
        }

        header("Location: ../Controllers/ReservationPageController.php?ReservationLists");
    }

    private function filterHandle(string $reservationStartingDate = '', string $reservationFinalDate = '', int $roomId = null, int $countryId = null): ?array
    {
        if ($reservationStartingDate == '' && $reservationFinalDate == '' && $roomId == null) {
            $reservations = $this->reservationsRepository->getAllReservations();
        } else if ($reservationStartingDate != '' && $reservationFinalDate != '' && $roomId != null) {
            $reservations = $this->reservationsRepository->getAllReservationByRoomAndPeriod($reservationStartingDate, $reservationFinalDate, $roomId);
        } else if ($reservationStartingDate == '' && $reservationFinalDate == '' && $countryId != null) {
            $guests = $this->guestsRepository->findByCountryId($countryId);

            foreach ($guests as $guest) {
                $reservationsByCountry = $this->reservationGuestsRepository->findByGuestId($guest->getId());

                $reservations = $this->reservationsRepository->getAllReservationById($reservationsByCountry->getReservationId());
            }
        } else {
            $reservations = $this->reservationsRepository->getAllReservationByRoom($roomId);
        }

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

        $idForDelete = intval($_GET['deleteId']);
        $reservationById = $this->reservationGuestsRepository->findByReservationId($idForDelete);
        $foundedGuest = $reservationById->getGuestId();
        $reservationMapIds = $this->reservationGuestsRepository->getAllReservationGuestsByGuestId($foundedGuest);

        foreach ($reservationMapIds as $reservationMapId) {
            $guestId = $reservationMapId->getGuestId();
            $reservationId = $reservationMapId->getReservationId();
            $this->reservationGuestsRepository->deleteByGuestId($reservationMapId->getGuestId());
            $this->guestsRepository->delete($guestId);
            $this->paymentsRepository->delete($reservationId);
            $this->reservationsRepository->delete($reservationId);
        }

        header("Location: ../Controllers/ReservationPageController.php?ReservationLists");
    }
}
