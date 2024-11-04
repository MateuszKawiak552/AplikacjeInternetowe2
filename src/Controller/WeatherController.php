<?php

namespace App\Controller;

use App\Entity\Location;
use App\Service\WeatherUtil;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country}', name: 'app_weather',)]
    public function city(Location $location,WeatherUtil $util): Response
    {
        $measurements = $util->getWeatherForCountryAndCity($location->getCountry(), $location->getCity());
        
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
    
}

