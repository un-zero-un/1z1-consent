<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\ConsentCrudController;
use App\Controller\Admin\DashboardController;
use App\Tests\Controller\AdminLogin;
use App\Tests\Controller\Repositories;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

final class ConsentCrudControllerTest extends AbstractCrudTestCase
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

    public function testFilters(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl().'?filters[status]=accepted&filters[gpcEnabled]=1');

        $this->assertResponseIsSuccessful();
        $this->assertIndexPageEntityCount(20);
        $this->assertAnySelectorTextContains('[data-label="Consentements"]', '1 / 3');
        $this->assertAnySelectorTextContains('[data-label="Consentements"]', '2 / 3');
        $this->assertAnySelectorTextNotContains('[data-label="Consentements"]', '0 / 3');
        $this->assertAnySelectorTextNotContains('[data-label="Consentements"]', '3 / 3');

        $this->client->request('GET', $this->generateIndexUrl().'?filters[status]=declined');

        $this->assertResponseIsSuccessful();
        $this->assertIndexPageEntityCount(20);
        $this->assertAnySelectorTextContains('[data-label="Consentements"]', '0 / 3');
        $this->assertAnySelectorTextNotContains('[data-label="Consentements"]', '1 / 3');
        $this->assertAnySelectorTextNotContains('[data-label="Consentements"]', '2 / 3');
        $this->assertAnySelectorTextNotContains('[data-label="Consentements"]', '3 / 3');
    }

    protected function getControllerFqcn(): string
    {
        return ConsentCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
