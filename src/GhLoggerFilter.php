<?php

use Pomm\Exception\Exception;
use Pomm\FilterChain\FilterInterface;
use Pomm\FilterChain\QueryFilterChain;
use Pomm\Tools\Logger;

class GhLoggerFilter implements FilterInterface
{
    protected $logger;

    /**
     * __construct
     *
     * @param Pomm\Tools\Logger $logger
     **/
    public function __construct(GhLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @see Pomm\FilterChain\FilterInterface
     **/
    public function execute(QueryFilterChain $query_filter_chain)
    {
        $time_start = microtime(true);
        $stmt = $query_filter_chain->executeNext($query_filter_chain);
        $time_end = microtime(true);

        $this->logger->writeIfEnabled(sprintf("SQL ===\n%s\n===\nparams=(%s)\nduration = %.1f ms\nresults = %d\nmap class='%s'.\n", 
            $query_filter_chain->getSql(),
            join(', ', $query_filter_chain->getValues()),
            1000 * ($time_end - $time_start),
            $stmt->rowCount(),
            get_class($query_filter_chain->getMap())
        ));

        return $stmt;
    }
}
