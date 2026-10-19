<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class WelcomeTest extends KernelTestCase
{
    public function testDevelopmentWelcomePage(): void
    {
        $kernel = self::bootKernel(['environment' => 'dev']);
        $request = Request::create('/');
        $response = $kernel->handle($request);

        // Symfony serves its development welcome page with 404 until a route is added.
        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('<title>Welcome to Symfony!</title>', $response->getContent());
        self::assertStringContainsString(dirname(__DIR__), $response->getContent());
        $kernel->terminate($request, $response);
    }
}
