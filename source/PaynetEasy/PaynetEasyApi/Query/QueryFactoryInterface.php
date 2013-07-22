<?php

namespace PaynetEasy\PaynetEasyApi\Query;

interface QueryFactoryInterface
{
    /**
     * Create API query object by API query method
     *
     * @param       string              $apiQueryName       API query method
     *
     * @return      QueryInterface                          API query object
     */
    public function getQuery($apiQueryName);
}