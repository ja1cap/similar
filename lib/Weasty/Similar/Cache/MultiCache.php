<?php
namespace Weasty\Similar\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Class MultiCache
 * @package Weasty\Similar\Cache
 */
class MultiCache extends CacheProvider {

    /**
     * @var \Doctrine\Common\Cache\Cache[]
     */
    private $cacheProviders = [];

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return string|boolean The cached data or FALSE, if no cache entry exists for the given id.
     */
    protected function doFetch($id)
    {
        return $this->multiCall('fetch', [$id]);
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    protected function doContains($id)
    {
        return $this->multiCall('contains', [$id]);
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param string $data The cache entry/data.
     * @param int $lifeTime The lifetime. If != 0, sets a specific lifetime for this
     *                           cache entry (0 => infinite lifeTime).
     *
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->multiCall('save', [$id, $data, $lifeTime]);
        return true;
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    protected function doDelete($id)
    {
        $this->multiCall('delete', [$id]);
        return true;
    }

    /**
     * Flushes all cache entries.
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    protected function doFlush()
    {
        return true;
    }

    /**
     * Retrieves cached information from the data store.
     *
     * @since 2.2
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    protected function doGetStats()
    {
        return null;
    }

    /**
     * @param $method
     * @param array $args
     * @return $this
     */
    private function multiCall($method, $args = [])
    {
        switch($method){
            case 'contains':

                foreach($this->cacheProviders as $cacheProvider){

                    $contains = $cacheProvider->contains(current($args));
                    if($contains){
                        return $contains;
                    }

                }

                return false;

            case 'fetch':

                foreach($this->cacheProviders as $cacheProvider){

                    $data = $cacheProvider->fetch(current($args));
                    if($data){
                        return $data;
                    }

                }

                return null;

            default:

                foreach($this->cacheProviders as $cacheProvider){
                    call_user_func_array([$cacheProvider, $method], $args);
                }

        }
        return $this;
    }

    /**
     * @param Cache $cache
     * @return $this
     */
    public function addCacheProvider(Cache $cache)
    {
        $this->cacheProviders[] = $cache;
        return $this;
    }

}