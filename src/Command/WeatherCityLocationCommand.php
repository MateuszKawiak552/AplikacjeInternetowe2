<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city-location',
    description: 'Displays weather forecast for a location based on country code and city name',
)]
class WeatherCityLocationCommand extends Command
{
    public function __construct(
        private readonly WeatherUtil $weatherUtil
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('countryCode', InputArgument::REQUIRED, 'Country code of the location')
            ->addArgument('city', InputArgument::REQUIRED, 'City name of the location')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = $input->getArgument('countryCode');
        $city = $input->getArgument('city');

        $location = $this->weatherUtil->getLocationRepository()->findOneBy([
            'country' => $countryCode,
            'city' => $city,
        ]);

        if (!$location) {
            $io->error('Location not found.');
            return Command::FAILURE;
        }

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->writeln(sprintf('Location: %s, %s', $location->getCity(), $location->getCountry()));

        foreach ($measurements as $measurement) {
            $io->writeln(sprintf("\t%s: %s°C",
                $measurement->getDate()->format('Y-m-d'),
                $measurement->getCelsius()
            ));
        }

        return Command::SUCCESS;
    }
}
