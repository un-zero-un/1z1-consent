<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\WebsiteHitCrudController;
use App\Tests\Controller\AdminLogin;
use App\Tests\Controller\Repositories;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

final class WebsiteHitCrudControllerTest extends AbstractCrudTestCase
{
    use AdminLogin;
    use Repositories;

    public function testIndexIsUnderLogin(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseRedirects();
    }

    public function testIndexIsAccessible(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
        $this->assertIndexPageEntityCount(20);
    }

    protected function getControllerFqcn(): string
    {
        return WebsiteHitCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
