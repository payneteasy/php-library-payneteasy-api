<?php

namespace PaynetEasy\Paynet\Queries;

class       QueryFactory
implements  QueryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQuery($apiQueryName, array $apiQueryConfig)
    {
        $nameChunks = array_map('ucfirst', explode('-', $apiQueryName));
        $queryClass = __NAMESPACE__ . '\\' . implode('', $nameChunks) . 'Query';

        return new $queryClass($apiQueryConfig);
    }
}