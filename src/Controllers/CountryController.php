<?php

declare(strict_types=1);

include_once "../Models/Country.php";
include_once "../Database/database.php";
include_once "../Database/Repositories/CountryRepository.php";
include_once "../Database/Repositories/CityRepository.php";
include_once "../Models/City.php";
include_once "../Database/Repositories/GuestRepository.php";
include_once "../Models/Guest.php";
include_once "../Database/Repositories/ReservationRepository.php";
include_once "../Models/Reservation.php";
include_once "../Database/Repositories/ReservationGuestRepository.php";
include_once "../Models/ReservationGuest.php";
include_once "../Database/Repositories/EmployeeRepository.php";
include_once "../Models/Employee.php";
include_once "../Database/Repositories/PaymentRepository.php";
include_once "../Models/Payment.php";

$callController = new CountryController();

class CountryController
{
    private CountryRepository $countryRepository;
    private CityRepository $cityRepository;
    private GuestRepository $guestRepository;
    private ReservationRepository $reservationRepository;
    private ReservationGuestRepository $reservationGuestRepository;
    private EmployeeRepository $employeeRepository;
    private PaymentRepository $paymentRepository;

    public function __construct()
    {
        $this->countryRepository = new CountryRepository();
        $this->cityRepository = new CityRepository();
        $this->guestRepository = new GuestRepository();
        $this->reservationRepository = new ReservationRepository();
        $this->reservationGuestRepository = new ReservationGuestRepository();
        $this->employeeRepository = new EmployeeRepository();
        $this->paymentRepository = new PaymentRepository();

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
            case isset($_GET['Create']):
                $this->showCreateCountryPage();
                break;
            case isset($_GET['CountryList']):
                $this->showAllCountries();
                break;
                case isset($_GET['Edit']):
                    $this->showUpdatePage();
                    break;
        }
    }
     
    private function showCreateCountryPage(){
        require_once '../Views/Country.php';
    }

    private function showUpdatePage(){
        $countryId = $_GET['editId'];
        $country = $this->countryRepository->findById(intval($countryId));
        require_once '../Views/country_form.php';
    }

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $name = htmlspecialchars($_POST['name']);

        $this->countryRepository->create($name);

        header("Location: CountryController.php?CountryList");
    }

    private function showAllCountries(): void
    {
        $countries = $this->countryRepository->getAllCountries();
        require_once '../Views/CountryList.php';
                       
    }

    private function update()
    {
        $isPostIncome = isset($_POST['update']);


        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: CountryController.php?CountryList");
            exit();
        }

        $countryId = intval($_POST['countryId']);
        $name = htmlspecialchars($_POST['name']);

        $this->countryRepository->update($countryId, $name);

        header("Location: CountryController.php?CountryList");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: CountryController.php?CountryList");
            exit();
        }

        $countryId = intval($_GET['deleteId']);
       
        $guests = $this->guestRepository->findByCountryId($countryId);
        $employees = $this->employeeRepository->findByCountryId($countryId);
        $cities = $this->cityRepository->findByCountryId($countryId);

        foreach ($guests as $guest) {
            $guestId = $guest->getId();
            $reservationsGuests = $this->reservationGuestRepository->findByGuestId($guestId);
            $this->reservationGuestRepository->deleteByGuestId($guestId);
            $this->guestRepository->delete($guestId);
            $this->paymentRepository->delete($reservationsGuests->getReservationId());
            $this->reservationRepository->delete($reservationsGuests->getReservationId());

            foreach ($employees as $employee) {
                $this->employeeRepository->delete($employee->getId());
            }

            foreach ($cities as $city) {
                $this->cityRepository->delete($city->getId());
            }
        }
        $this->countryRepository->delete($countryId);

        header("Location: CountryController.php?CountryList");
    }
}
