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

$callController = new CountryPageController();

class CountryPageController
{
    private CountryRepository $countriesRepository;
    private CityRepository $citiesRepository;
    private GuestRepository $guestsRepository;
    private ReservationRepository $reservationRepository;
    private ReservationGuestRepository $reservationGuestsRepository;
    private EmployeeRepository $employeesRepository;
    private PaymentRepository $paymentsRepository;

    public function __construct()
    {
        $this->countriesRepository = new CountryRepository();
        $this->citiesRepository = new CityRepository();
        $this->guestsRepository = new GuestRepository();
        $this->reservationRepository = new ReservationRepository();
        $this->reservationGuestsRepository = new ReservationGuestRepository();
        $this->employeesRepository = new EmployeeRepository();
        $this->paymentsRepository = new PaymentRepository();

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
        $country = $this->countriesRepository->findById(intval($countryId));
        require_once '../Views/country_form.php';
    }

    private function create(): string
    {
        $isPostIncome = isset($_POST['submit']);

        if (!$isPostIncome) {
            return '';
        }

        $name = htmlspecialchars($_POST['name']);

        $this->countriesRepository->create($name);

        header("Location: CountryPageController.php?CountryList");
    }

    private function showAllCountries(): void
    {
        $countries = $this->countriesRepository->getAllCountries();
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
            header("Location: CountryPageController.php?CountryList");
            exit();
        }

        $countryId = intval($_POST['countryId']);
        $name = htmlspecialchars($_POST['name']);

        $this->countriesRepository->update($countryId, $name);

        header("Location: CountryPageController.php?CountryList");
    }

    private function delete(): string
    {
        $isPostIncome = isset($_POST['delete']);

        if (!$isPostIncome) {
            return '';
        }

        $isCancelEditIncome = isset($_POST['cancel']);

        if ($isCancelEditIncome) {
            header("Location: CountryPageController.php?CountryList");
            exit();
        }

        $countryId = intval($_GET['deleteId']);
       
        $guests = $this->guestsRepository->findByCountryId($countryId);
        $employees = $this->employeesRepository->findByCountryId($countryId);
        $cities = $this->citiesRepository->findByCountryId($countryId);

        foreach ($guests as $guest) {
            $guestId = $guest->getId();
            $reservationsGuests = $this->reservationGuestsRepository->findByGuestId($guestId);
            $this->reservationGuestsRepository->deleteByGuestId($guestId);
            $this->guestsRepository->delete($guestId);
            $this->paymentsRepository->delete($reservationsGuests->getReservationId());
            $this->reservationRepository->delete($reservationsGuests->getReservationId());

            foreach ($employees as $employee) {
                $this->employeesRepository->delete($employee->getId());
            }

            foreach ($cities as $city) {
                $this->citiesRepository->delete($city->getId());
            }
        }
        $this->countriesRepository->delete($countryId);

        header("Location: CountryPageController.php?CountryList");
    }
}
