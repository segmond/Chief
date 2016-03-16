<?php

namespace Chief\Decorators;

use Chief\Busses\SynchronousCommandBus;
use Chief\CacheableCommand;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingDecorator implements Decorator
{
    /**
     * @var CommandBus
     */
    private $innerBus;
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * CachingDecorator constructor.
     * @param CacheItemPoolInterface $cache
     * @param CommandBus $innerCommandBus
     */
    public function __construct(CacheItemPoolInterface $cache, CommandBus $innerCommandBus = null)
    {
        $this->cache = $cache;
        $this->setInnerBus($innerCommandBus ?: new SynchronousCommandBus);
    }

    /**
     * @inheritdoc
     */
    public function setInnerBus(CommandBus $bus)
    {
        $this->innerBus = $bus;
    }

    /**
     * @inheritdoc
     */
    public function execute(Command $command)
    {
        if (!$command instanceof CacheableCommand) {
            return $this->innerBus->execute($command);
        }

        $cached = $this->cache->getItem($this->parseCachekey($command));
        if ($cached->isHit()) {
            return $cached->get();
        }

        $value = $this->innerBus->execute($command);

        $this->cache->save($this->createCacheItem($command, $value));

        return $value;
    }

    /**
     * Create a new cache item instance to be persisted.
     *
     * @param CacheableCommand $command
     * @param mixed $value
     * @return CacheItemInterface
     */
    private function createCacheItem(CacheableCommand $command, $value)
    {
        return $this->cache->getItem($this->parseCachekey($command))
            ->expiresAfter($this->parseCacheExpiry($command))
            ->set($value);
    }

    /**
     * @param CacheableCommand $command
     * @throws \InvalidArgumentException
     * @return string
     */
    private function parseCachekey(CacheableCommand $command)
    {
        $key = $command->getCacheKey();

        if (strlen($key) < 1) {
            throw new \InvalidArgumentException('Cache key cannot be null');
        }

        return $key;
    }

    /**
     * @param CacheableCommand $command
     * @throws \InvalidArgumentException
     * @return int
     */
    private function parseCacheExpiry(CacheableCommand $command)
    {
        $seconds = $command->getCacheExpiry();

        if ($seconds < 1) {
            throw new \InvalidArgumentException('Cache expiry must be at least 1 second');
        }

        return $seconds;
    }
}
