<?php

namespace Chief\Cache;

use Chief\Command;

interface CacheableCommand extends Command
{
    /**
     * @return int|null
     *  In how many seconds from now should this cache item expire. Return null to use the default value specified
     *  in the CachingDecorator.
     */
    public function getCacheExpiry();

    /**
     * @return string|null
     *  The cache key used when caching this object. Return null to automatically generate a cache key.
     */
    public function getCacheKey();
}
