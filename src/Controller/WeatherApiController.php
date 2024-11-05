<?php

namespace App\Controller;



use App\Entity\MeasurementEntry;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;



class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(
        #[MapQueryParameter(filter: \FILTER_VALIDATE_REGEXP, options: ['regexp' => '/^[a-zA-Z-]+$/'])] string $city,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_REGEXP, options: ['regexp' => '/^[a-zA-Z]{2}$/'])] string $country,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_REGEXP, options: ['regexp' => '/^(csv|json)$/'])] string $format,
        #[MapQueryParameter('twig')] bool $twig = false,
        WeatherUtil $weatherUtil,
    ): Response {
        $measurementEntries = $weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($format === 'json')
         {
            if ($twig) {
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurementEntries,
                ]);
            }
#json 
            return $this->json([
                'city' => $city,
                'country' => $country,
                'measurements' => array_map(
                    fn(MeasurementEntry $m) => [
                        'temperature_celsius' => $m->getTemperatureCelsius(),
                        'feels_like' => $m->getFeelsLike(),
                        'humidity' => $m->getHumidity(),
                        'pressure' => $m->getPressure(),
                        'timestamp' => $m->getDateTime()->format('Y-m-d H:i:s'),
                        'wind_speed' => $m->getWindSpeed(),
                        'wind_direction' => $m->getWindDirection(),
                        'temperature_fahrenheit' => $m->getFahrenheit(),
                    ],
                    $measurementEntries
                ),
            ]);
        } elseif ($format === 'csv') 
        {
            if ($twig) 
            {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurementEntries,
                ], new Response('', 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="weather.csv"',
                ]));

            }
#csv 

            $csv = "city,country,temperature_celsius,feels_like,humidity,pressure,timestamp,wind_speed,wind_direction,temperature_fahrenheit\n";
            foreach ($measurementEntries as $m) {
                $csv .= sprintf(
                    "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                    $city,
                    $country,
                    $m->getTemperatureCelsius(),
                    $m->getFeelsLike(),
                    $m->getHumidity(),
                    $m->getPressure(),
                    $m->getDateTime()->format('Y-m-d H:i:s'),
                    $m->getWindSpeed(),
                    $m->getWindDirection(),
                    $m->getFahrenheit()
                );
            }

            return new Response($csv, 200, ['Content-Type' => 'text/csv']);
        }

        return $this->json(['error' => 'Unsupported format'], 400);
    }
}
