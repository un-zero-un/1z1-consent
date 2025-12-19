<?php

namespace App\Tests\Provider;

use App\Entity\Website;
use App\Provider\PrivacyContextProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

final class PrivacyContextProviderTest extends TestCase
{
    #[DataProvider('providerTestContextIsValid')]
    public function testContextIsValid(
        bool $expectedDoNotTrack,
        bool $expectedGlobalPrivacyControl,
        bool $dntEnabled,
        bool $respectDoNotTrack,
        bool $respectGlobalPrivacyControl,
        string $dntHeader,
        string $secGpcHeader,
    ): void {
        $provider = new PrivacyContextProvider($dntEnabled);

        $request = $this->createStub(Request::class);
        $request->headers = new HeaderBag([
            'DNT' => $dntHeader,
            'Sec-GPC' => $secGpcHeader,
        ]);

        $website = $this->createStub(Website::class);
        $website->respectDoNotTrack = $respectDoNotTrack;
        $website->respectGlobalPrivacyControl = $respectGlobalPrivacyControl;

        $privacyContext = $provider->getContext($website, $request);
        $this->assertSame($expectedDoNotTrack, $privacyContext->doNotTrack);
        $this->assertSame($expectedGlobalPrivacyControl, $privacyContext->globalPrivacyControl);
    }

    public static function providerTestContextIsValid(): iterable
    {
        yield 'All disabled' => [
            'expectedDoNotTrack' => false,
            'expectedGlobalPrivacyControl' => false,
            'dntEnabled' => false,
            'respectDoNotTrack' => false,
            'respectGlobalPrivacyControl' => false,
            'dntHeader' => '0',
            'secGpcHeader' => '0',
        ];

        yield 'All enabled' => [
            'expectedDoNotTrack' => true,
            'expectedGlobalPrivacyControl' => true,
            'dntEnabled' => true,
            'respectDoNotTrack' => true,
            'respectGlobalPrivacyControl' => true,
            'dntHeader' => '1',
            'secGpcHeader' => '1',
        ];

        yield 'DNT Globally disabled' => [
            'expectedDoNotTrack' => false,
            'expectedGlobalPrivacyControl' => true,
            'dntEnabled' => false,
            'respectDoNotTrack' => true,
            'respectGlobalPrivacyControl' => true,
            'dntHeader' => '1',
            'secGpcHeader' => '1',
        ];

        yield 'DNT Website disabled' => [
            'expectedDoNotTrack' => false,
            'expectedGlobalPrivacyControl' => true,
            'dntEnabled' => true,
            'respectDoNotTrack' => false,
            'respectGlobalPrivacyControl' => true,
            'dntHeader' => '1',
            'secGpcHeader' => '1',
        ];

        yield 'GPC Website disabled' => [
            'expectedDoNotTrack' => true,
            'expectedGlobalPrivacyControl' => false,
            'dntEnabled' => true,
            'respectDoNotTrack' => true,
            'respectGlobalPrivacyControl' => false,
            'dntHeader' => '1',
            'secGpcHeader' => '1',
        ];
    }
}
