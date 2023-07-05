<?php

declare(strict_types=1);

include_once '../Database/Repositories/CityRepository.php';
include_once '../Models/City.php';
include_once '../Database/Repositories/CountryRepository.php';
include_once '../Models/Country.php';

class SelectMenuHelper
{
    private CityRepository $citiesRepository;
    private CountryRepository $countriesRepository;

    public function __construct()
    {
        $this->citiesRepository = new CityRepository();
        $this->countriesRepository = new CountryRepository();
    }

    public function generateCitySelectMenu(int $selectedCityId = null, int $selectedCountryId = null)
{
    $countryId = isset($_GET['countryId']) ? intval($_GET['countryId']) : $selectedCountryId;
    $cities = $this->citiesRepository->getCitiesByCountryId($countryId);

    $selectMenus = [];

    foreach ($cities as $city) {
        $cityId = $city->getId();
        $cityName = $city->getName();
        $selected = ($selectedCityId !== null && $selectedCityId === $cityId);

        $selectMenus[] = [
            'id' => $cityId,
            'name' => $cityName,
            'selected' => $selected
        ];
    }

    if (!isset($_GET['countryId'])) {
        return $selectMenus;
    } else {
        echo json_encode($selectMenus);
        exit();
    }
}


    public function generateCountrySelectMenu(int $selectedCountryId = null): array
    {
        $countries = $this->countriesRepository->getAllCountries();
        $selectMenu = [];

        foreach ($countries as $country) {
            $countryId = $country->getId();
            $countryName = $country->getName();
            $selected = ($selectedCountryId !== null && $selectedCountryId === $countryId);

            $selectMenu[] = [
                'id' => $countryId,
                'name' => $countryName,
                'selected' => $selected
            ];
        }

        return $selectMenu;
    }
}
