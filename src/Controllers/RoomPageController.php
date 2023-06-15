<?php

declare(strict_types=1);

include_once  "../Models/Cities.php";
include_once  "../Database/Repositories/RoomsRepository.php";
include_once "../Database/database.php";
include_once "../Models/Employees.php";
include_once "../Models/RoomTypes.php";
include_once "../Database/Repositories/RoomTypesRepository.php";
include_once "../Models/Rooms.php";
include_once "../Models/RoomExtras.php";
include_once "../Database/Repositories/RoomExtrasRepository.php";
include_once "../Database/Repositories/RoomsExtrasMapRepository.php";
include_once "../Models/RoomExtrasMap.php";


$callController = new RoomPageController();

class RoomPageController
{
    private const VIEW_PATH = "../Views/rooms.html";
    private const VIEW_LIST_PATH = "../Views/RoomList.html";
    private RoomsRepository $roomsRepository;
    private RoomTypesRepository $roomTypesRepository;
    private RoomExtrasRepository $roomExtrasRepository;
    private RoomsExtrasMapRepository $roomExtrasMapRepository;

    public function __construct()
    {
        $this->roomsRepository = new RoomsRepository();
        $this->roomTypesRepository = new RoomTypesRepository();
        $this->roomExtrasRepository = new RoomExtrasRepository();
        $this->roomExtrasMapRepository = new RoomsExtrasMapRepository();

        $this->create();
        $this->update();
        $this->delete();

        if(isset($_GET['Rooms'])){
        echo $this->showRoomPage();
        }

        if(isset($_GET['RoomLists'])){
        echo $this->showRoomList();
        }
    }

    public function showRoomPage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $navigation = $this->generateNavigation();
        $typeSelectMenu = $this->generateTypesSelectMenu();
        $extraSelectMenu = $this->generateExtraCheckboxes();
      
        $result = sprintf($file, $navigation, $typeSelectMenu, $extraSelectMenu);

        return $result;
    }

    public function showRoomList(): string
    {
        $file = file_get_contents(self::VIEW_LIST_PATH);
        $navigation = $this->generateNavigation();
        $allRooms = $this->showAllRooms();
        $generateEditPopup = $this->generateUpdatePopupForm();
        $generateDeletePopup = $this->generateDeletePopup();


        $result = sprintf($file, $navigation, $allRooms, $generateEditPopup,$generateDeletePopup);

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


    private function generateTypesSelectMenu(int $selectedTypesId = null): string
    {
        $types = $this->roomTypesRepository->getAllTypes();

        $selectMenu = '<label for="types" class="form-label">Types</label>';
        $selectMenu .= '<select class="form-select" name="typeId" id="types">';

        foreach ($types as $type) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $typeId = $type->getId();
            $typeName = $type->getName();
            $selected = ($selectedTypesId !== null && $selectedTypesId === $typeId) ? "selected" : "";
            $option = sprintf($optionTemplate, $typeId, $selected, $typeName);
            $selectMenu .= $option;
        }

        $selectMenu .= "</select>";
        
        return $selectMenu;
    }

    private function generateExtraCheckboxes(array $selectedExtraIds = []): string
    {
        $extras = $this->roomExtrasRepository->getAllExtras();
        $checkboxes = '<label class="form-label">Extras</label>';

        foreach ($extras as $extra) {
            $checkboxTemplate = '<div class="form-check">';
            $checkboxTemplate .= '<input class="form-check-input" type="checkbox" name="extraIds[]" value="%s" %s>';
            $checkboxTemplate .= '<label class="form-check-label" for="extra_%s">%s</label>';
            $checkboxTemplate .= '</div>';

            $extraId = $extra->getId();
            $extraName = $extra->getName();
            $checked = in_array($extraId, $selectedExtraIds) ? "checked" : "";
            $checkbox = sprintf($checkboxTemplate, $extraId, $checked, $extraId, $extraName);
            $checkboxes .= $checkbox;
        }

        return $checkboxes;
    }

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

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

    private function generateUpdatePopupForm(): string
    {
        $isEditRequested = isset($_GET['editId']);

        if (!$isEditRequested) {
            return '';
        }

        $roomId = intval($_GET['editId']);
        $room = $this->roomsRepository->findById($roomId);
        $allTypes = $this->roomTypesRepository->getAllTypes();
        $allExtrasForRoom = $this->roomExtrasMapRepository->getAllExtrasForRoom($roomId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id='form-container'>";
        $form .= "<form method='POST' action='../Controllers/RoomPageController.php?RoomLists'>";
        $form .= "<input type='hidden' name='roomId' value='" . $room->getId() . "'>";
        $form .= "<label for='Number'>Number:</label>";
        $form .= "<input type='text' name='number' value='" . $room->getNumber() . "'>";
        $form .= "<label for='types' class='form-label'>Types</label>";
        $form .= "<select class='form-select' name='typeId' id='types'>";

        foreach ($allTypes as $type) {
            $optionTemplate = "<option value='%s' %s>%s</option>";
            $typeId = $type->getId();
            $typeName = $type->getName();
            $selected = ($room->getTypeId() === $typeId) ? "selected" : "";
            $option = sprintf($optionTemplate, $typeId, $selected, $typeName);
            $form .= $option;
        }

        $form .= "</select>";
        $form .= "<br>";
        $form .= "<label for='price'>Price:</label>";
        $form .= "<input type='text' name='price' value='" . $room->getPrice() . "'>";
        $form .= "<br>";

        $form .= "<br>";
        $extraIds = [];
        foreach ($allExtrasForRoom as $extra) {
            $extraIds[] = $extra['extraId'];
        }

        $checkboxes = $this->generateExtraCheckboxes($extraIds);
        $form .= $checkboxes;

        $form .= "<br>";
        $form .= "<input type='submit' name='update' value='Update'>";
        $form .= "<input type='submit' name='cancel' value='Cancel'>";
        $form .= "</form>";
        $form .= "</div>";
        $form .= "<script>";
        $form .= "document.getElementById('overlay').style.display = 'block';";
        $form .= "document.getElementById('form-container').style.display = 'block';";
        $form .= "</script>";

        return $form;
    }

    private function generateDeletePopup() : string
    {
        $isEditRequested = isset($_GET['deleteId']);

        if (!$isEditRequested) {
            return '';
        }

        $roomId = intval($_GET['deleteId']);
        $room = $this->roomsRepository->findById($roomId);

        $form = "<div id='overlay'></div>";
        $form .= "<div id = 'form-container'>";
        $form .= "<form method='POST' action='../Controllers/RoomPageController.php?RoomLists'>";
        $form .= "<input type='hidden' name='roomId' value='" . $room->getId() . "'>";
        $form .= '<p class="text-center">Are you sure to delete this room?</p>';
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
    private function showAllRooms(): string
    {
        $rooms = $this->roomsRepository->getAllRooms();
        $result = '';


        $result .= '<div class="container mt-3">';
        $result .= '<table class ="table table-primary table-striped">';
        $result .= '<thead>';
        $result .= '<tr>';
        $result .= '<th>Number</th>';
        $result .= '<th>Type</th>';
        $result .= '<th>Price</th>';
        $result .= '<th>Extries</th>';
        $result .= '<th>Options</th>';
        $result .= '</tr></thead>';

        foreach ($rooms as $room) {
            $result .= '<tbody>';
            $result .= '<tr>';
            $result .= '<td>' . $room->getNumber() . '</td>';
            $type = $this->roomTypesRepository->findById($room->getTypeId());
            $result .= '<td>' . $type->getName() . '</td>';
            $result .= '<td>' . $room->getPrice() . '</td>';

            $extraIdFromMapRepository = $this->roomExtrasMapRepository->getAllExtrasForRoom($room->getId());
            $extras = [];

            foreach ($extraIdFromMapRepository as $extraIds) {
                foreach ($extraIds as $extraId) {
                    $extras[] = $this->roomExtrasRepository->findById($extraId)->getName();
                }
            }
            
            if (empty($extras)) {
                $result .= '<td>No Extras</td>';
            } else {
                $result .= '<td>' . implode(', ', $extras) . '</td>';
            }

            $result .= '<td><div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>';
            $result .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';

            $result .= "<li><a class='dropdown-item' href='../Controllers/RoomPageController.php?RoomLists&deleteId=" . $room->getId() . "'>Delete</a></li>";
            $result .= "<li><a class='dropdown-item' href='../Controllers/RoomPageController.php?RoomLists&editId=" . $room->getId() . "'>Edit</a></li>";
            $result .= '</ul></div></td>';

            $result .= '</tr></tbody>';
        }

        $result .= '</table>';
        $result .= '</div>';

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

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: ../Controllers/RoomPageController.php?RoomLists");
            exit();
        }

        $roomId = intval($_POST['roomId']);
        $roomMapId = $this->roomExtrasMapRepository->findByRoomId($roomId);

        $this->roomExtrasMapRepository->delete($roomMapId->getId());
        $this->roomsRepository->delete($roomId);

        header("Location: ../Controllers/RoomPageController.php?RoomLists");
    }
}
