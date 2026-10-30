<?php

namespace App\Tests;

use App\Entity\Location;
use App\Entity\Measurement;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CrudTest extends WebTestCase
{
    public function testCrudAndBackendValidation(): void
    {
        // Keep the real development database untouched.
        $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = 'sqlite:///:memory:';
        $client = static::createClient();
        $client->disableReboot();
        $manager = static::getContainer()->get('doctrine')->getManager();
        (new SchemaTool($manager))->createSchema($manager->getMetadataFactory()->getAllMetadata());

        $client->request('GET', '/location/new');
        $client->submitForm('Zapisz', ['location[city]' => '', 'location[country]' => 'PL', 'location[latitude]' => '91', 'location[longitude]' => '14.5']);
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('body', 'Podaj miasto nowej lokalizacji.');
        self::assertSelectorTextContains('body', 'Szerokość musi być między -90 a 90.');
        $client->submitForm('Zapisz', ['location[city]' => 'Testowo', 'location[latitude]' => '53.4']);
        self::assertResponseRedirects('/location', 303);
        $location = $manager->getRepository(Location::class)->findOneBy(['city' => 'Testowo']);
        self::assertNotNull($location);
        $locationId = $location->getId();

        $client->request('GET', '/location/'.$locationId.'/edit');
        $client->submitForm('Zapisz zmiany', ['location[city]' => '']);
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('body', 'Miasto edytowanej lokalizacji nie może być puste.');
        $client->submitForm('Zapisz zmiany', ['location[city]' => 'Testowo po edycji']);
        self::assertResponseRedirects('/location', 303);

        $client->request('GET', '/measurement/new');
        $client->submitForm('Zapisz', ['measurement[date]' => '2026-10-30', 'measurement[celsius]' => '12.5', 'measurement[humidity]' => '101']);
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('body', 'Wybierz lokalizację nowego pomiaru.');
        self::assertSelectorTextContains('body', 'Wilgotność musi być między 0 a 100%.');
        $client->submitForm('Zapisz', ['measurement[location]' => (string) $locationId, 'measurement[humidity]' => '75']);
        self::assertResponseRedirects('/measurement', 303);
        $measurement = $manager->getRepository(Measurement::class)->findOneBy([]);
        self::assertNotNull($measurement);
        $measurementId = $measurement->getId();

        $client->request('GET', '/measurement/'.$measurementId.'/edit');
        $client->submitForm('Zapisz zmiany', ['measurement[location]' => '', 'measurement[celsius]' => '-300']);
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('body', 'Edytowany pomiar musi mieć lokalizację.');
        self::assertSelectorTextContains('body', 'Temperatura nie może być niższa niż -273,15°C.');
        $client->submitForm('Zapisz zmiany', ['measurement[location]' => (string) $locationId, 'measurement[celsius]' => '10']);
        self::assertResponseRedirects('/measurement', 303);

        foreach (['/location', '/measurement'] as $path) {
            $client->request('GET', $path);
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('nav a[href="/location"]');
            self::assertSelectorExists('nav a[href="/measurement"]');
            self::assertSelectorExists('.table-responsive table');
            self::assertSelectorTextContains('table', 'Testowo po edycji');
        }

        $client->request('GET', '/location/'.$locationId);
        $client->submitForm('Usuń');
        self::assertResponseRedirects('/location/'.$locationId);
        $client->followRedirect();
        self::assertSelectorTextContains('.alert', 'Najpierw usuń pomiary');

        $client->request('GET', '/measurement/'.$measurementId);
        $client->submitForm('Usuń');
        self::assertResponseRedirects('/measurement', 303);
        self::assertNull($manager->find(Measurement::class, $measurementId));
        $client->request('GET', '/location/'.$locationId);
        $client->submitForm('Usuń');
        self::assertResponseRedirects('/location', 303);
        self::assertNull($manager->find(Location::class, $locationId));
    }
}
