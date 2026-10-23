<?php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Entity\Location;
use App\Entity\Measurement;

$location = new Location();
$measurement = (new Measurement())
    ->setDate(new DateTime('2026-10-23'))
    ->setCelsius(12.5)
    ->setHumidity(75);

assert($measurement->getDate()->format('Y-m-d') === '2026-10-23');
assert($measurement->getCelsius() === 12.5);
assert($measurement->getHumidity() === 75);
assert($location->getMeasurements()->isEmpty());
$location->addMeasurement($measurement);
$location->addMeasurement($measurement);
assert($location->getMeasurements()->count() === 1);
assert($measurement->getLocation() === $location);
$location->removeMeasurement($measurement);
assert($location->getMeasurements()->isEmpty());
assert($measurement->getLocation() === null);

echo "OK\n";
