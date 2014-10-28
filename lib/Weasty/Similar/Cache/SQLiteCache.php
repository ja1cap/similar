<?php

namespace Weasty\Similar\Cache;

use Doctrine\Common\Cache\CacheProvider;

/**
 * Class SQLiteCache
 * @package Weasty\Similar\Cache
 */
class SQLiteCache extends CacheProvider
{
    /**
     * @var \SQLite3
     */
    private $db;

    /**
     * Constructor
     *
     * @param string $dsn
     */
    public function __construct($dsn)
    {
        if (':memory:' === $dsn) {
            $this->db = new \SQLite3($dsn);
        } else {
            $this->db = new \SQLite3($dsn, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        }

        $this->initDb();
    }

    /**
     * {@inheritDoc}
     */
    protected function doContains($id)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM doctrine_cache WHERE cache_key = :id AND (cache_expired_at = 0 OR cache_expired_at >= :date)');
        $stmt->bindParam('id', $id);
        $stmt->bindValue('date', time());

        $data = $stmt->execute()->fetchArray(SQLITE3_NUM);
        return $data[0] > 0;
    }

    /**
     * {@inheritDoc}.
     */
    protected function doDelete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM doctrine_cache WHERE cache_key = :id');
        $stmt->bindParam('id', $id);
        $stmt->execute();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function doFetch($id)
    {
        $stmt = $this->db->prepare('SELECT cache_data FROM doctrine_cache WHERE cache_key = :id AND (cache_expired_at = 0 OR cache_expired_at >= :date)');
        $stmt->bindParam('id', $id);
        $stmt->bindValue('date', time());

        $data = $stmt->execute()->fetchArray(SQLITE3_NUM);

        return $data[0];
    }

    /**
     * {@inheritDoc}
     */
    protected function doFlush()
    {
        return $this->db->exec('DELETE FROM doctrine_cache');
    }

    /**
     * {@inheritDoc}
     */
    protected function doSave($id, $data, $lifeTime = false)
    {

        $stmt = $this->db->prepare('UPDATE doctrine_cache SET cache_data = :data, cache_expired_at = :lifetime WHERE cache_key = :id');
        $stmt->bindParam('data', $data);
        $stmt->bindParam('lifetime', $lifeTime);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        if (!$this->db->changes()) {
            return $this->createCacheEntry($id, $data, $lifeTime);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function doGetStats()
    {
        return null;
    }

    /**
     * Create a cache entry
     *
     * @param string  $id       The cache key
     * @param string  $data     The serialized data
     * @param integer $lifeTime The lifetime
     *
     * @return boolean
     */
    private function createCacheEntry($id, $data, $lifeTime)
    {
        $stmt = $this->db->prepare('INSERT INTO doctrine_cache (cache_key, cache_data, cache_expired_at) VALUES (:id, :data, :lifetime)');
        $stmt->bindParam('id', $id);
        $stmt->bindParam('data', $data);
        $stmt->bindParam('lifetime', $lifeTime);

        return false !== $stmt->execute();
    }

    /**
     * Initialize the sqlite table
     */
    private function initDb()
    {
        $this->db->exec(<<<SQL
CREATE TABLE IF NOT EXISTS doctrine_cache (
  id INTEGER NOT NULL,
  cache_key TEXT NOT NULL,
  cache_data BLOB NOT NULL,
  cache_expired_at INTEGER NOT NULL,
  PRIMARY KEY(id)
)
SQL
        );
    }

}