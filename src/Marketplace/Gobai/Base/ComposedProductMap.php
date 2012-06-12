<?php

namespace Marketplace\Gobai\Base;

use \Pomm\Object\BaseObjectMap;
use \Pomm\Exception\Exception;

abstract class ComposedProductMap extends BaseObjectMap
{
    public function initialize()
    {

        $this->object_class =  'Marketplace\Gobai\ComposedProduct';
        $this->object_name  =  'gobai.composed_product';

        $this->addField('reference', 'varchar');
        $this->addField('composed_of', 'varchar');

        $this->pk_fields = array('reference', 'composed_of');
    }
}