<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;

class WeatherUtil
{
    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly MeasurementRepository $measurementRepository,
    ){}

    /**
     * Zwraca repozytorium lokalizacji.
     *
     * @return LocationRepository
     */
    public function getLocationRepository(): LocationRepository
    {
        return $this->locationRepository;
    }

    /**
     * Pobiera dane pogodowe (pomiar) dla konkretnej lokalizacji.
     *
     * @param Location $location
     * @return Measurement[] Lista pomiarów dla podanej lokalizacji.
     */
    public function getWeatherForLocation(Location $location): array
    {
        return $this->measurementRepository->findByLocation($location);
    }

    /**
     * Pobiera dane pogodowe (pomiar) na podstawie kodu kraju i nazwy miasta.
     *
     * @param string $countryCode Kod kraju
     * @param string $city Nazwa miasta
     * @return Measurement[] Lista pomiarów dla danej lokalizacji
     */
    public function getWeatherForCountryAndCity(string $countryCode, string $city): array
    {
        $location = $this->locationRepository->findOneBy([
            'country' => $countryCode,
            'city' => $city,
        ]);

        if (!$location) {
            return [];
        }

        return $this->getWeatherForLocation($location);
    }
}
