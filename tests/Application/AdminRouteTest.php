<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AdminRouteTest extends WebTestCase
{
    public function test_admin_is_accessible_on_main_domain(): void
    {
        $client = static::createClient(server: ['HTTP_HOST' => getenv('MAIN_DOMAIN')]);
        $client->request('GET', '/admin/login');

        $this->assertResponseStatusCodeSame(200);
    }

    public function test_admin_is_inaccessible_on_random_domain(): void
    {
        $client = static::createClient(server: ['HTTP_HOST' => 'example.com']);
        $client->request('GET', '/admin/login');

        $this->assertResponseStatusCodeSame(404);
    }

    public function test_admin_is_inaccessible_on_main_domain_subdomain(): void
    {
        $client = static::createClient(server: ['HTTP_HOST' => 'www.'.getenv('MAIN_DOMAIN')]);
        $client->request('GET', '/admin/login');

        $this->assertResponseStatusCodeSame(404);
    }
}
