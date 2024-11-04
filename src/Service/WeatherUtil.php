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
        // Używa repozytorium MeasurementRepository do znalezienia pomiarów dla lokalizacji
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
        // Pobiera lokalizację na podstawie kodu kraju i nazwy miasta
        $location = $this->locationRepository->findOneBy([
            'country' => $countryCode,
            'city' => $city,
        ]);

        // Jeśli lokalizacja nie została znaleziona, zwraca pustą tablicę
        if (!$location) {
            return [];
        }

        // Wywołuje metodę getWeatherForLocation, aby pobrać dane dla znalezionej lokalizacji
        return $this->getWeatherForLocation($location);
    }
}
