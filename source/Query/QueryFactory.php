<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\String;
use RuntimeException;

class       QueryFactory
implements  QueryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQuery($apiQueryName, array $apiQueryConfig)
    {
        $queryClass = __NAMESPACE__ . '\\' . String::camelize($apiQueryName) . 'Query';

        if (class_exists($queryClass, true))
        {
            return new $queryClass($apiQueryConfig);
        }

        // :NOTICE:         Imenem          18.06.13
        //
        // All "*-form" methods has the same format,
        // therefore they have only one class - FormQuery
        if (preg_match('#.*-form$#i', $apiQueryName))
        {
            $query = new FormQuery($apiQueryConfig);
            $query->setApiMethod($apiQueryName);

            return $query;
        }

        throw new RuntimeException("Unknown query class '{$queryClass}' for query with name '{$apiQueryName}'");
    }
}