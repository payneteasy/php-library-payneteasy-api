<?php

namespace PaynetEasy\Paynet\Query;

interface QueryFactoryInterface
{
    /**
     * Create API query object by API query method
     *
     * @param       string              $apiQueryName       API query method
     * @param       array               $apiQueryConfig     API query config
     *
     * @return      QueryInterface                          API query object
     */
    public function getQuery($apiQueryName, array $apiQueryConfig);
}