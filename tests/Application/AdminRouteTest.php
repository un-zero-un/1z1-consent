<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AdminRouteTest extends WebTestCase
{
    public function testAdminIsAccessibleOnMainDomain(): void
    {
        $client = static::createClient(server: ['HTTP_HOST' => getenv('MAIN_DOMAIN')]);
        $client->request('GET', '/admin/login');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testAdminIsInaccessibleOnRandomDomain(): void
    {
        $client = static::createClient(server: ['HTTP_HOST' => 'example.com']);
        $client->request('GET', '/admin/login');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testAdminIsInaccessibleOnMainDomainSubdomain(): void
    {
        $client = static::createClient(server: ['HTTP_HOST' => 'www.'.getenv('MAIN_DOMAIN')]);
        $client->request('GET', '/admin/login');

        $this->assertResponseStatusCodeSame(404);
    }
}
