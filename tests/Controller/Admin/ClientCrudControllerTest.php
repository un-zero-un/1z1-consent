<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\ClientCrudController;
use App\Controller\Admin\DashboardController;
use App\Entity\Client;
use App\Tests\Controller\AdminLogin;
use App\Tests\Controller\Repositories;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

final class ClientCrudControllerTest extends AbstractCrudTestCase
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
        $this->assertGlobalActionDisplays('new', 'Créer Client');
    }

    public function testNewValidationErrors(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->clickOnIndexGlobalAction('new');

        $this->client->submitForm('Créer', [
            'Client[name]' => '',
        ]);

        $this->assertSelectorCount(1, '.invalid-feedback');
        $this->assertSelectorTextContains('.invalid-feedback', 'Cette valeur ne doit pas être vide.');
    }

    public function testNewAndCreate(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);

        $this->assertSelectorTextNotContains('.datagrid', 'Le Client du Test');
        $this->clickOnIndexGlobalAction('new');

        $crawler = $this->client->getCrawler();
        $this->client->request(
            'POST',
            $this->generateNewFormUrl(),
            [
                'Client' => [
                    '_token' => $crawler->filter('#Client__token')->attr('value'),
                    'name' => 'Le Client du Test',
                    'persons' => [
                        [
                            'firstName' => 'Test',
                            'lastName' => 'User',
                            'country' => 'FR',
                            'email' => 'test@example.com',
                            'phoneNumber' => '0601020304',
                        ],
                    ],
                ],
                'ea' => [
                    'newForm' => [
                        'btn' => 'saveAndReturn',
                    ],
                ],
            ],
        );

        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(2);
        $this->assertSelectorTextContains('.datagrid', 'Le Client du Test');
    }

    public function testEdit(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);
        $this->assertSelectorTextContains('.datagrid', 'Client de test');
        $this->assertSelectorTextNotContains('.datagrid', 'Client de test - Modifié');

        $client = $this->getRepository(Client::class)->findOneBy(['name' => 'Client de test']);
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('edit', $client->id))->link(),
        );

        $this->client->submitForm('Sauvegarder les modifications', [
            'Client' => [
                'name' => 'Client test - Modifié',
                'persons' => [
                    [
                        'firstName' => 'Person 1',
                        'lastName' => 'Person 1',
                    ],
                    [
                        'firstName' => 'Person 2',
                        'lastName' => 'Person 2',
                    ],
                ],
            ],
        ]);

        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(1);
        $this->assertSelectorTextNotContains('.datagrid', 'Client de test');
        $this->assertSelectorTextContains('.datagrid', 'Client test - Modifié');
        $this->assertSelectorTextContains('.datagrid', 'Person 1');
        $this->assertSelectorTextContains('.datagrid', 'Person 2');
    }

    public function testDelete(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);

        $client = $this->getRepository(Client::class)->findOneBy(['name' => 'Client de test']);
        $this->client->request(
            'POST',
            $crawler->filter($this->getIndexEntityActionSelector('delete', $client->id))->attr('href'),
            [
                'token' => $crawler->filter('#delete-form [name="token"]')->attr('value'),
            ]
        );

        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexPageEntityCount(0);
    }

    public function testDetail(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $client = $this->getRepository(Client::class)->findOneBy(['name' => 'Client de test']);
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('detail', $client->id))->link(),
        );

        $this->assertResponseIsSuccessful();
    }

    public function testViewRegister(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $client = $this->getRepository(Client::class)->findOneBy(['name' => 'Client de test']);
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('viewRegister', $client->id))->link(),
        );

        $this->assertResponseIsSuccessful();
    }

    public function testViewPDFRegister(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $client = $this->getRepository(Client::class)->findOneBy(['name' => 'Client de test']);
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('viewPDFRegister', $client->id))->link(),
        );

        $this->assertResponseIsSuccessful();
    }

    protected function getControllerFqcn(): string
    {
        return ClientCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
