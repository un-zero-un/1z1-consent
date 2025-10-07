<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\ServerCrudController;
use App\Entity\Server;
use App\Tests\Controller\AdminLogin;
use App\Tests\Controller\Repositories;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

final class ServerCrudControllerTest extends AbstractCrudTestCase
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
        $this->assertIndexFullEntityCount(1);
        $this->assertGlobalActionDisplays('new', 'Créer Serveur');
    }

    public function testNewValidationErrors(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->clickOnIndexGlobalAction('new');

        $this->client->submitForm('Créer', [
            'Server[name]' => '',
        ]);

        $this->assertSelectorCount(1, '.invalid-feedback');
        $this->assertSelectorTextContains('.invalid-feedback', 'Cette valeur ne doit pas être vide.');
    }

    public function testNewAndCreate(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);

        $this->assertSelectorTextNotContains('.datagrid', 'Serveur 101');
        $this->clickOnIndexGlobalAction('new');

        $this->client->submitForm('Créer', [
            'Server' => [
                'name' => 'Serveur 101',
            ],
            'ea' => [
                'newForm' => [
                    'btn' => 'saveAndReturn',
                ],
            ],
        ]);

        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(2);
        $this->assertSelectorTextContains('.datagrid', 'Serveur 101');
    }

    public function testEdit(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);
        $this->assertSelectorTextContains('.datagrid', 'Serveur 1');
        $this->assertSelectorTextNotContains('.datagrid', 'Livreur 101');

        $server = $this->getRepository(Server::class)->findOneBy(['name' => 'Serveur 1']);
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('edit', $server->getId()))->link(),
        );

        $this->client->submitForm('Sauvegarder les modifications', [
            'Server' => [
                'name' => 'Livreur 101',
            ],
        ]);

        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(1);
        $this->assertSelectorTextNotContains('.datagrid', 'Serveur 1');
        $this->assertSelectorTextContains('.datagrid', 'Livreur 101');
    }

    public function testDelete(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);

        $server = $this->getRepository(Server::class)->findOneBy(['name' => 'Serveur 1']);
        $this->client->request(
            'POST',
            $crawler->filter($this->getIndexEntityActionSelector('delete', $server->getId()))->attr('href'),
            [
                'token' => $crawler->filter('#delete-form [name="token"]')->attr('value'),
            ]
        );

        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexPageEntityCount(0);
    }

    protected function getControllerFqcn(): string
    {
        return ServerCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
