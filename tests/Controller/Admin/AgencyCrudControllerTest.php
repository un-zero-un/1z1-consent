<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\AgencyCrudController;
use App\Controller\Admin\DashboardController;
use App\Entity\Agency;
use App\Tests\Controller\AdminLogin;
use App\Tests\Controller\Repositories;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

final class AgencyCrudControllerTest extends AbstractCrudTestCase
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
        $this->assertIndexFullEntityCount(2);
        $this->assertGlobalActionDisplays('new', 'Créer Agence');
    }

    public function testNewValidationErrors(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->clickOnIndexGlobalAction('new');

        $this->client->submitForm('Créer', [
            'Agency[name]' => '',
        ]);

        $this->assertSelectorCount(2, '.invalid-feedback');
        $this->assertSelectorTextContains('.invalid-feedback', 'Cette valeur ne doit pas être vide.');
    }

    public function testNewAndCreate(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(2);

        $this->assertSelectorTextNotContains('.datagrid', 'Un Zéro Trois');
        $this->clickOnIndexGlobalAction('new');

        $this->client->submitForm('Créer', [
            'Agency' => [
                'name' => 'Un Zéro Trois',
                'host' => 'third.localhost',
            ],
            'ea' => [
                'newForm' => [
                    'btn' => 'saveAndReturn',
                ],
            ],
        ]);

        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(3);
        $this->assertSelectorTextContains('.datagrid', 'Un Zéro Trois');
    }

    public function testEdit(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(2);
        $this->assertSelectorTextContains('.datagrid', 'Un Zéro Un');
        $this->assertSelectorTextNotContains('.datagrid', 'Un Zéro Trois');

        $agency = $this->getRepository(Agency::class)->findOneBy(['name' => 'Un Zéro Un']);
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('edit', $agency->getId()))->link(),
        );

        $this->client->submitForm('Sauvegarder les modifications', [
            'Agency' => [
                'name' => 'Un Zéro Trois',
            ],
        ]);

        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(2);
        $this->assertSelectorTextNotContains('.datagrid', 'Un Zéro Un');
        $this->assertSelectorTextContains('.datagrid', 'Un Zéro Trois');
    }

    public function testDeleteFails(): void
    {
        $platformName = $this->getContainer()->get('doctrine')->getConnection()->getDatabasePlatform()->getName();
        if ('sqlite' === $platformName) {
            $this->markTestSkipped('SQLite in-memory does not support foreign keys.');
        }

        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(2);

        $agency = $this->getRepository(Agency::class)->findOneBy(['name' => 'Un Zéro Un']);
        $this->client->request(
            'POST',
            $crawler->filter($this->getIndexEntityActionSelector('delete', $agency->getId()))->attr('href'),
            [
                'token' => $crawler->filter('#delete-form [name="token"]')->attr('value'),
            ],
        );

        $this->assertResponseStatusCodeSame(409);
    }

    protected function getControllerFqcn(): string
    {
        return AgencyCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
