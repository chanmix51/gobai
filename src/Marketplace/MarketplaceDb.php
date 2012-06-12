<?php

namespace Marketplace;

use Pomm\Connection\Database;
use Pomm\Converter;

class MarketplaceDb extends Database
{
    protected function initialize()
    {
        parent::initialize();
        $this->registerConverter('LTree', new Converter\PgLTree(), array('public.ltree'));
    }
}
