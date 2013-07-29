<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\String;
use RuntimeException;

class       QueryFactory
implements  QueryFactoryInterface
{
    /**
     * Interface, that query class must implement
     *
     * @var     string
     */
    static protected $queryInterface = 'PaynetEasy\PaynetEasyApi\Query\QueryInterface';

    /**
     * {@inheritdoc}
     */
    public function getQuery($apiQueryName)
    {
        $queryClass = __NAMESPACE__ . '\\' . String::camelize($apiQueryName) . 'Query';

        if (class_exists($queryClass, true))
        {
            return $this->instantiateQuery($queryClass, $apiQueryName);
        }

        throw new RuntimeException("Unknown query class '{$queryClass}' for query with name '{$apiQueryName}'");
    }

    /**
     * Method check query class and return new query object
     *
     * @param       string      $queryClass         Query class
     * @param       string      $apiQueryName       Query api method name
     *
     * @return      QueryInterface                  New query object
     *
     * @throws      RuntimeException                Query does not implements QueryInterface
     */
    protected function instantiateQuery($queryClass, $apiQueryName)
    {
        if (!is_a($queryClass, static::$queryInterface, true))
        {
            throw new RuntimeException("Query class '{$queryClass}' does not implements '" .
                                       static::$queryInterface . "' interface.");
        }

        return new $queryClass($apiQueryName);
    }
}