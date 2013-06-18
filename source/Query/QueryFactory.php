<?php

namespace PaynetEasy\Paynet\Query;

use RuntimeException;

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

        if (class_exists($queryClass, true))
        {
            return new $queryClass($apiQueryConfig);
        }

        // :NOTICE:         Imenem          18.06.13
        //
        // All "*-form" methods has the same format,
        // therefore they have only one class - FormQuery
        if (end($nameChunks) == 'Form')
        {
            $query = new FormQuery($apiQueryConfig);
            $query->setApiMethod($apiQueryName);

            return $query;
        }

        throw new RuntimeException("Unknown query class {$queryClass} for query with name {$apiQueryName}");
    }
}