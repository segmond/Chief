<?php

namespace Chief;

trait CacheableCommandDefaults
{
    /**
     * @return int
     *  In how many seconds from now should this cache item expire.
     */
    public function getCacheExpiry()
    {
        return 60*60*24;
    }

    /**
     * @return string
     *  The cache key used when caching this object.
     */
    public function getCacheKey()
    {
        $properties = get_object_vars($this);
        return md5(static::class . serialize($properties));
    }
}
