<?php

namespace App\Tests\Cache;

use App\Cache\ApiBypasser;
use App\Cache\Fingerprint;
use App\Repository\WebsiteHitRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class ApiBypasserTest extends TestCase
{
    private CacheInterface&MockObject $cache;
    private WebsiteHitRepository&MockObject $repository;
    private ApiBypasser $object;

    public function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->repository = $this->createMock(WebsiteHitRepository::class);
        $this->object = new ApiBypasser($this->cache, $this->repository);
    }

    public function testItCantBypass(): void
    {
        $fingerprint = $this->createMock(Fingerprint::class);
        $fingerprint->expects($this->once())->method('getHash')->willReturn('hash');

        $this->repository->expects($this->never())->method($this->anything());
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('hash', $this->isInstanceOf(\Closure::class))
            ->willReturn(null);

        $this->assertFalse($this->object->canBypass($fingerprint));
    }

    public function testItCanBypass(): void
    {
        $fingerprint = $this->createMock(Fingerprint::class);
        $fingerprint->expects($this->once())->method('getHash')->willReturn('hash');

        $response = $this->createMock(Response::class);

        $this->repository->expects($this->never())->method($this->anything());
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('hash', $this->isInstanceOf(\Closure::class))
            ->willReturn($response);

        $this->assertTrue($this->object->canBypass($fingerprint));
    }

    public function testItBypass(): void
    {
        $fingerprint = $this->createMock(Fingerprint::class);
        $fingerprint->expects($this->once())->method('getHash')->willReturn('hash');
        $fingerprint->expects($this->once())->method('getWebsiteId')->willReturn('00000000-0000-0000-0000-000000000042');

        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getClientIp')->willReturn('0.42.42.1');
        $request->headers = new HeaderBag();
        $request->headers->set('referer', 'https://example.com');

        $response = $this->createMock(Response::class);

        $this->repository
            ->expects($this->once())
            ->method('saveFromRawData')
            ->with('00000000-0000-0000-0000-000000000042', '0.42.42.1', 'https://example.com');

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('hash', $this->isInstanceOf(\Closure::class))
            ->willReturn($response);

        $this->assertSame($response, $this->object->bypass($fingerprint, $request));
    }

    public function testBypassThrowsIfMisused(): void
    {
        $this->expectException(\RuntimeException::class);

        $fingerprint = $this->createMock(Fingerprint::class);
        $fingerprint->expects($this->once())->method('getHash')->willReturn('hash');
        $fingerprint->expects($this->once())->method('getWebsiteId')->willReturn('00000000-0000-0000-0000-000000000042');

        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getClientIp')->willReturn('0.42.42.1');
        $request->headers = new HeaderBag();
        $request->headers->set('referer', 'https://example.com');

        $this->repository
            ->expects($this->once())
            ->method('saveFromRawData')
            ->with('00000000-0000-0000-0000-000000000042', '0.42.42.1', 'https://example.com');

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('hash', $this->isInstanceOf(\Closure::class))
            ->willReturnCallback(fn (string $hash, \Closure $callback) => $callback());

        $this->object->bypass($fingerprint, $request);
    }

    public function testItSaves(): void
    {
        $fingerprint = $this->createMock(Fingerprint::class);
        $fingerprint->expects($this->any())->method('getHash')->willReturn('hash');

        $response = $this->createMock(Response::class);

        $this->cache->expects($this->once())->method('delete')->with('hash');

        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem->expects($this->once())->method('expiresAfter')->with(3600 * 24);
        $cacheItem->expects($this->once())->method('set')->with($response);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('hash', $this->isInstanceOf(\Closure::class))
            ->willReturnCallback(fn (string $key, \Closure $callback) => $callback($cacheItem));

        $this->object->save($fingerprint, $response);
    }

    public function testItRemoves(): void
    {
        $fingerprint = $this->createMock(Fingerprint::class);
        $fingerprint->expects($this->once())->method('getHash')->willReturn('hash');

        $this->cache->expects($this->once())->method('delete')->with('hash');

        $this->object->remove($fingerprint);
    }
}
