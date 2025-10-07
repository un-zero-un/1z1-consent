<?php

namespace App\Tests\Controller;

use Symfony\Component\BrowserKit\AbstractBrowser;

trait AdminLogin
{
    public function login(string $username, string $password): AbstractBrowser
    {
        $client = static::getClient();
        $client->request('GET', '/admin/login');

        $client->submitForm('Connectez-vous', ['_username' => $username, '_password' => $password]);

        self::assertResponseRedirects('/admin');

        return $client;
    }

    public function logout(): AbstractBrowser
    {
        $client = static::getClient();
        $client->request('GET', '/admin/logout');

        self::assertResponseRedirects('/admin/login');

        return $client;
    }

    abstract public static function assertResponseRedirects(?string $expectedLocation = null, ?int $expectedCode = null, string $message = '', bool $verbose = true): void;

    abstract protected static function getClient(?AbstractBrowser $newClient = null): ?AbstractBrowser;
}
