<?php

namespace Marketplace\Gobai\Base;

use \Pomm\Object\BaseObjectMap;
use \Pomm\Exception\Exception;

abstract class ProductMap extends BaseObjectMap
{
    public function initialize()
    {

        $this->object_class =  'Marketplace\Gobai\Product';
        $this->object_name  =  'gobai.product';

        $this->addField('reference', 'varchar');
        $this->addField('name', 'varchar');
        $this->addField('description', 'text');
        $this->addField('tags', 'public.ltree[]');
        $this->addField('price', 'numeric');
        $this->addField('slug', 'varchar');

        $this->pk_fields = array('reference');
    }
}