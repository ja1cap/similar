<?php
namespace Weasty\Similar\Index\Sphinx;

use Foolz\SphinxQL\Connection;
use Foolz\SphinxQL\SphinxQL;
use Weasty\Similar\Index\AbstractSimilarIndex;

/**
 * Class SimilarSphinxIndex
 * @package Weasty\Similar\Index\Sphinx
 */
class SimilarSphinxIndex extends AbstractSimilarIndex {

    /**
     * @var \Foolz\SphinxQL\Connection
     */
    private $connection;

    /**
     * @return Connection
     */
    private function getConnection(){
        if(!$this->connection){
            $this->connection = new Connection();
            $this->connection->setParams(array('host' => 'localhost', 'port' => 19306));
        }
        return $this->connection;
    }

    /**
     * @param $query
     * @return array
     */
    public function search($query)
    {

        $query = $this->prepareValue($query);
        if(!$query){
            return [];
        }

        $spinxQuery = SphinxQL::create($this->getConnection())
            ->select()
            ->from('similarities')
            ->match('keyword', $query)
        ;

        $results = array_map(function($result){ return $result['keyword']; }, $spinxQuery->execute());
        return $results;

    }

}