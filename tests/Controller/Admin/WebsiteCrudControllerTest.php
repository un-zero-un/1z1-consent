<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\WebsiteCrudController;
use App\Entity\Client;
use App\Entity\Server;
use App\Entity\Website;
use App\Tests\Controller\AdminLogin;
use App\Tests\Controller\Repositories;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

final class WebsiteCrudControllerTest extends AbstractCrudTestCase
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
        $this->assertGlobalActionDisplays('new', 'Créer Site');
    }

    public function testNewValidationErrors(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->clickOnIndexGlobalAction('new');

        $crawler = $this->client->getCrawler();
        $this->client->request(
            'POST',
            $this->generateNewFormUrl(),
            [
                'Website' => [
                    '_token' => $crawler->filter('#Website__token')->attr('value'),
                    'client' => '',
                ],
                'ea' => [
                    'newForm' => [
                        'btn' => 'saveAndReturn',
                    ],
                ],
            ],
        );

        $this->assertSelectorCount(1, '.invalid-feedback');
        $this->assertSelectorTextContains('.invalid-feedback', 'Cette valeur ne doit pas être vide.');
    }

    public function testNewAndCreate(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);

        $this->assertSelectorTextNotContains('.datagrid', 'www.example.com');
        $this->clickOnIndexGlobalAction('new');

        $crawler = $this->client->getCrawler();
        $this->client->request(
            'POST',
            $this->generateNewFormUrl(),
            [
                'Website' => [
                    '_token' => $crawler->filter('#Website__token')->attr('value'),
                    'client' => $this->getRepository(Client::class)->findOneBy(['name' => 'Client de test'])->id->toRfc4122(),
                    'server' => $this->getRepository(Server::class)->findOneBy(['name' => 'Serveur 1'])->id->toRfc4122(),
                    'domains' => [
                        [
                            'domain' => 'www.example.com',
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
        $this->assertSelectorTextContains('.datagrid', 'www.example.com');
    }

    public function testEdit(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);
        $this->assertSelectorTextContains('.datagrid', 'www.localhost');
        $this->assertSelectorTextNotContains('.datagrid', 'www.example.com');

        $website = $this->getRepository(Website::class)->findOneByHostname('www.localhost');
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('edit', $website->id))->link(),
        );

        $this->client->submitForm('Sauvegarder les modifications', [
            'Website' => [
                'domains' => [
                    [
                        'domain' => 'www.example.com',
                    ]
                ],
            ],
        ]);

        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(1);
        $this->assertSelectorTextNotContains('.datagrid', 'www.localhost');
        $this->assertSelectorTextContains('.datagrid', 'www.example.com');
    }

    public function testDelete(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(1);

        $website = $this->getRepository(Website::class)->findOneByHostname('www.localhost');
        $this->client->request(
            'POST',
            $crawler->filter($this->getIndexEntityActionSelector('delete', $website->id))->attr('href'),
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

        $website = $this->getRepository(Website::class)->findOneByHostname('www.localhost');
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('detail', $website->id))->link(),
        );

        $this->assertResponseIsSuccessful();
    }


    public function testShowConsent(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $website = $this->getRepository(Website::class)->findOneByHostname('www.localhost');
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('showConsents', $website->id))->link(),
        );

        $this->assertResponseIsSuccessful();
    }

    protected function getControllerFqcn(): string
    {
        return WebsiteCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
