<?php

namespace App\Cache;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @psalm-suppress PropertyNotSetInConstructor Error raised for non-initialized property $callbackWrapper. False positive.
 */
final class AutoCacheAdapter extends AbstractAdapter
{
    private readonly AbstractAdapter $adapter;

    /**
     * @psalm-suppress UndefinedClass RedisAdapter uses some Redis adapter class that is not always available
     */
    public function __construct(
        string $namespace = '',
        int $defaultLifetime = 0,
        ?MarshallerInterface $marshaller = null,
        #[Autowire('%env(CACHE_DSN)%')] #[\SensitiveParameter] string $cacheDsn = 'apcu://default',
    ) {
        switch (true) {
            case str_starts_with($cacheDsn, 'redis://'):
            case str_starts_with($cacheDsn, 'rediss://'):
            case str_starts_with($cacheDsn, 'valkey://'):
            case str_starts_with($cacheDsn, 'valkeys://'):
                if (!preg_match('#/(\d+)?$#', $cacheDsn)) {
                    $cacheDsn .= '/0'; // Default to database 0 if not specified
                }

                $this->adapter = new RedisAdapter(
                    RedisAdapter::createConnection($cacheDsn),
                    $namespace,
                    $defaultLifetime,
                    $marshaller,
                );
                break;
            case str_starts_with($cacheDsn, 'memcached://'):
                $this->adapter = new MemcachedAdapter(
                    MemcachedAdapter::createConnection($cacheDsn),
                    $namespace,
                    $defaultLifetime,
                    $marshaller,
                );
                break;
            case str_starts_with($cacheDsn, 'apcu://'):
                $this->adapter = new ApcuAdapter($namespace, $defaultLifetime, null, $marshaller);
                break;
            default:
                throw new \InvalidArgumentException("Unsupported cache DSN: $cacheDsn");
        }

        parent::__construct($namespace, $defaultLifetime);
    }

    #[\Override]
    protected function doFetch(array $ids): iterable
    {
        return $this->adapter->doFetch($ids);
    }

    #[\Override]
    protected function doHave(string $id): bool
    {
        return $this->adapter->doHave($id);
    }

    #[\Override]
    protected function doClear(string $namespace): bool
    {
        return $this->adapter->doClear($namespace);
    }

    #[\Override]
    protected function doDelete(array $ids): bool
    {
        return $this->adapter->doDelete($ids);
    }

    #[\Override]
    protected function doSave(array $values, int $lifetime): array|bool
    {
        return $this->adapter->doSave($values, $lifetime);
    }
}
