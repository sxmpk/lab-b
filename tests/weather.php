<?php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Entity\Location;
use App\Entity\Measurement;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$kernel = new Kernel('dev', true);
$kernel->boot();
$manager = $kernel->getContainer()->get('doctrine')->getManager();
$connection = $manager->getConnection();
$connection->beginTransaction();

try {
    $location = (new Location())->setCity('Test City')->setCountry('PL')
        ->setLatitude('53.4289')->setLongitude('14.553');
    $other = (new Location())->setCity('Test City')->setCountry('DE')
        ->setLatitude('53.5521')->setLongitude('14.5718');
    $manager->persist($location);
    $manager->persist($other);
    foreach ([[$location, 'yesterday'], [$location, 'today'], [$location, 'tomorrow'], [$other, 'tomorrow']] as [$city, $date]) {
        $manager->persist((new Measurement())->setLocation($city)
            ->setDate(new DateTime($date))->setCelsius(12.5)->setHumidity(75));
    }
    $manager->flush();

    $measurements = $manager->getRepository(Measurement::class)->findByLocation($location);
    assert(count($measurements) === 1);
    assert($measurements[0]->getDate()->format('Y-m-d') === (new DateTime('tomorrow'))->format('Y-m-d'));
    assert($measurements[0]->getLocation() === $location);

    $response = $kernel->handle(Request::create('/weather/Test%20City/PL'));
    assert($response->getStatusCode() === 200);
    assert(str_contains($response->getContent(), 'Weather in Test City, PL'));
    assert(str_contains($response->getContent(), '12.5&deg;C'));
    assert(!str_contains($response->getContent(), 'Weather in Test City, DE'));
    $missing = $kernel->handle(Request::create('/weather/Test%20City/XX'));
    assert($missing->getStatusCode() === 404);
    echo "OK\n";
} finally {
    $connection->rollBack();
    $kernel->shutdown();
}
