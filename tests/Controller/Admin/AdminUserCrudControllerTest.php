<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\AdminUserCrudController;
use App\Controller\Admin\DashboardController;
use App\Entity\AdminUser;
use App\Entity\Agency;
use App\Tests\Controller\AdminLogin;
use App\Tests\Controller\Repositories;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

final class AdminUserCrudControllerTest extends AbstractCrudTestCase
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
        $this->assertIndexFullEntityCount(3);
        $this->assertGlobalActionDisplays('new', 'Créer Utilisateur');
    }

    public function testNewValidationErrors(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());
        $this->clickOnIndexGlobalAction('new');

        $this->client->submitForm('Créer', [
            'AdminUser[email]' => '',
        ]);

        $this->assertSelectorCount(1, '.invalid-feedback');
        $this->assertSelectorTextContains('.invalid-feedback', 'Cette valeur ne doit pas être vide.');
    }

    public function testNewAndCreate(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertSelectorTextNotContains('.datagrid', 'test@example.com');
        $this->clickOnIndexGlobalAction('new');

        $this->client->submitForm('Créer', [
            'AdminUser[email]' => 'test@example.com',
            'AdminUser[agency]' => $this->getRepository(Agency::class)->findOneByName('Un Zéro Un')->id,
            'ea[newForm][btn]' => 'saveAndReturn',
        ]);

        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(4);
        $this->assertSelectorTextContains('.datagrid', 'test@example.com');
    }

    public function testEdit(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(3);
        $this->assertSame(1, $crawler->filterXPath('//table//tr[contains(normalize-space(),"Un Zéro Deux")]')->count());

        $createdUser = $this->getRepository(AdminUser::class)->findOneByEmail('agency@example.com');

        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('edit', $createdUser->id))->link(),
        );

        $this->client->submitForm('Sauvegarder les modifications', [
            'AdminUser[email]' => 'agency@example.com',
            'AdminUser[agency]' => $this->getRepository(Agency::class)->findOneByName('Un Zéro Deux')->id,
        ]);

        $this->assertResponseRedirects($this->generateIndexUrl());
        $crawler = $this->client->followRedirect();

        $this->assertIndexFullEntityCount(3);
        $this->assertSame(2, $crawler->filterXPath('//table//tr[contains(normalize-space(),"Un Zéro Deux")]')->count());
    }

    public function testDelete(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertIndexFullEntityCount(3);

        $agencyUser = $this->getRepository(AdminUser::class)->findOneByEmail('agency@example.com');

        $this->client->request(
            'POST',
            $crawler->filter($this->getIndexEntityActionSelector('delete', $agencyUser->id))->attr('href'),
            [
                'token' => $crawler->filter('#delete-form [name="token"]')->attr('value'),
            ]
        );
        $this->assertResponseRedirects($this->generateIndexUrl());
        $this->client->followRedirect();

        $this->assertIndexFullEntityCount(2);
    }

    public function testImpersonate(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $this->assertSelectorTextContains('.main-content .navbar-custom-menu .user-name', 'admin@example.com');
        $this->assertSelectorTextNotContains('.main-content .navbar-custom-menu .user-name', 'other@example.com');

        $otherUser = $this->getRepository(AdminUser::class)->findOneByEmail('other@example.com');
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('impersonate', $otherUser->id))->link(),
        );
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->client->followRedirect();

        $this->assertSelectorTextContains('.main-content .navbar-custom-menu .user-name', 'other@example.com');
        $this->assertSelectorTextNotContains('.main-content .navbar-custom-menu .user-name', 'admin@example.com');
    }

    public function testSendResetPasswordLink(): void
    {
        $this->login('admin@example.com', '!ChangeMe!');
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $otherUser = $this->getRepository(AdminUser::class)->findOneByEmail('other@example.com');
        $this->client->click(
            $crawler->filter($this->getIndexEntityActionSelector('sendResetPasswordLink', $otherUser->id))->link(),
        );

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailSubjectContains($email, 'Réinitialisation de votre mot de passe');

        $this->assertResponseRedirects();
        $this->client->followRedirect();

        $this->assertSelectorTextContains('.alert-success', 'Un email de réinitialisation de mot de passe a été envoyé à l\'utilisateur.');
    }

    protected function getControllerFqcn(): string
    {
        return AdminUserCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
