<?php

declare(strict_types=1);

include_once  "../Models/City.php";
include_once  "../Database/Repositories/RoomRepository.php";
include_once "../Database/database.php";
include_once "../Models/Employee.php";
include_once "../Models/RoomType.php";
include_once "../Database/Repositories/RoomTypeRepository.php";
include_once "../Models/Room.php";
include_once "../Models/RoomExtra.php";
include_once "../Database/Repositories/RoomExtraRepository.php";
include_once "../Database/Repositories/RoomExtraMapRepository.php";
include_once "../Models/RoomExtraMap.php";
include_once "../Database/Repositories/ReservationRepository.php";
include_once "../Models/Reservation.php";
include_once "../Database/Repositories/ReservationGuestRepository.php";
include_once "../Models/ReservationGuest.php";

$callController = new RoomPageController();

class RoomPageController
{
    private const VIEW_PATH = "../Views/rooms.html";
    private const VIEW_LIST_PATH = "../Views/RoomList.html";
    private RoomRepository $roomsRepository;
    private RoomTypeRepository $roomTypesRepository;
    private RoomExtraRepository $roomExtrasRepository;
    private RoomExtraMapRepository $roomExtrasMapRepository;
    private ReservationRepository $reservationRepository;
    private ReservationGuestRepository  $reservationGuestsRepository;

    public function __construct()
    {
        $this->roomsRepository = new RoomRepository();
        $this->roomTypesRepository = new RoomTypeRepository();
        $this->roomExtrasRepository = new RoomExtraRepository();
        $this->roomExtrasMapRepository = new RoomExtraMapRepository();
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
            case isset($_GET['Rooms']):
                echo $this->showRoomPage();
                break;
            case isset($_GET['RoomLists']):
                echo $this->showRoomList();
                break;
            case isset($_GET['Edit']):
                echo $this->showUpdatePage();
                break;
        }
    }

    private function showUpdatePage()
    {
        $roomId = $_GET['editId'];
        $room = $this->roomsRepository->findById(intval($roomId));

        $allTypes = $this->roomTypesRepository->getAllTypes();

        $allExtrasForRoom = $this->roomExtrasMapRepository->getAllExtrasForRoom(intval($roomId));

        $typeOptions = $this->generateTypesSelectMenu($room->getTypeId());

        foreach ($allExtrasForRoom as $extra) {
            $extraIds[] = $extra['extraId'];
        }

        $extraOptions = $this->generateExtraCheckboxes($extraIds);

        require_once "../Views/room_form.php";
    }


    private function showRoomPage()
    {
        $typeOptions =  $this->generateTypesSelectMenu();
        $extraOptions = $this->generateExtraCheckboxes();

        require_once "../Views/rooms.php";
    }

    private function showRoomList()
    {
        $typeId = isset($_POST['typeId']) ? intval($_POST['typeId']) : null;
        $typeOptions = $this->generateTypesSelectMenu();
        if ($typeId == null) {
            $rooms = $this->roomsRepository->getAllRooms();
        } else {
            $rooms = $this->roomsRepository->filterRoomByTypeId($typeId);
        }

        $roomOptions = [];
        foreach ($rooms as $room) {
            $roomId = $room->getId();
            $roomNumber = $room->getNumber();
            $type = $this->roomTypesRepository->findById($room->getTypeId());
            $roomType = $type->getName();
            $roomPrice = $room->getPrice();
            $extraIdFromMapRepository = $this->roomExtrasMapRepository->getAllExtrasForRoom($room->getId());
            $extras = [];

            foreach ($extraIdFromMapRepository as $extraIds) {
                foreach ($extraIds as $extraId) {
                    $extras[] = $this->roomExtrasRepository->findById($extraId)->getName();
                }
            }

            $roomOptions[] = [
                'id' => $roomId,
                'number' => $roomNumber,
                'type' => $roomType,
                'price' => $roomPrice,
                'extras' => $extras
            ];
        }
        require_once '../Views/RoomList.php';
    }

    private function generateTypesSelectMenu(int $selectedTypesId = null): ?array
    {
        $types = $this->roomTypesRepository->getAllTypes();
        $selectMenu = [];
        foreach ($types as $type) {
            $typeId = $type->getId();
            $typeName = $type->getName();
            $selected = ($selectedTypesId !== null && $selectedTypesId === $typeId) ? "selected" : "";

            $selectMenu[] = [
                'id' => $typeId,
                'name' => $typeName,
                'selected' => $selected
            ];
        }

        return $selectMenu;
    }

    private function generateExtraCheckboxes(array $selectedExtraIds = []): ?array
    {
        $extras = $this->roomExtrasRepository->getAllExtras();
        $checkboxes = [];

        foreach ($extras as $extra) {
            $extraId = $extra->getId();
            $extraName = $extra->getName();
            $checked = in_array($extraId, $selectedExtraIds) ? "checked" : "";

            $checkboxes[] = [
                'id' => $extraId,
                'name' => $extraName,
                'checked' => $checked
            ];
        }

        return $checkboxes;
    }

    private function create(): void
    {
        $number = htmlspecialchars($_POST['number']);
        $typeId = htmlspecialchars($_POST['typeId']);
        $price = htmlspecialchars($_POST['price']);
        $extras = isset($_POST['extraIds']) ? $_POST['extraIds'] : [];

        $room = $this->roomsRepository->create(intval($number), intval($typeId), floatval($price));

        foreach ($extras as $extraId) {
            $extra = $this->roomExtrasRepository->findById(intval($extraId));
            $this->roomExtrasMapRepository->create($room->getId(), $extra->getId());
        }

        header("Location: ../Controllers/RoomPageController.php?RoomLists");
    }

    private function update(): void
    {
        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/RoomPageController.php?RoomLists");
            exit();
        }

        $roomId = intval($_POST['roomId']);
        $number = htmlspecialchars($_POST['number']);
        $typeId = htmlspecialchars($_POST['typeId']);
        $price = htmlspecialchars($_POST['price']);
        $extras = isset($_POST['extraIds']) ? $_POST['extraIds'] : [];


        $currentExtras = $this->roomExtrasMapRepository->getAllExtrasForRoom($roomId);

        foreach ($currentExtras as $currentExtra) {
            $found = false;
            foreach ($extras as $extra) {
                if ($extra == $currentExtra['extraId']) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->roomExtrasMapRepository->deleteByRoomId($roomId);
                break;
            }
        }

        foreach ($extras as $extra) {
            $existingExtra = $this->roomExtrasMapRepository->findByRoomId($roomId);
            if ($existingExtra && $existingExtra->getExtraId() == intval($extra)) {
                continue;
            }

            $this->roomExtrasMapRepository->create($roomId, intval($extra));
        }

        $this->roomsRepository->update($roomId, intval($number), intval($typeId), floatval($price));

        header("Location: ../Controllers/RoomPageController.php?RoomLists");
    }

    private function delete(): void
    {
        $isCancelEditIncome = isset($_POST['Cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/RoomPageController.php?RoomLists");
            exit();
        }

        $roomId = intval($_GET['deleteId']);
        $roomMapId = $this->roomExtrasMapRepository->findByRoomId($roomId);

        $reservations = $this->reservationRepository->findByRoomId($roomId);
        foreach ($reservations as $reservation) {
            $reservationId = $reservation->getId();
            $this->reservationGuestsRepository->deleteByReservationId($reservationId);
        }

        $this->reservationRepository->deleteByRoomId($roomId);

        $this->roomExtrasMapRepository->deleteByRoomId($roomId);
        $this->roomsRepository->delete($roomId);

        header("Location: ../Controllers/RoomPageController.php?RoomLists");
    }
}
