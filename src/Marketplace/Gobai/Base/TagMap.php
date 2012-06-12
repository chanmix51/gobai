<?php

namespace Marketplace\Gobai\Base;

use \Pomm\Object\BaseObjectMap;
use \Pomm\Exception\Exception;

abstract class TagMap extends BaseObjectMap
{
    public function initialize()
    {

        $this->object_class =  'Marketplace\Gobai\Tag';
        $this->object_name  =  'gobai.tag';

        $this->addField('reference', 'bpchar');
        $this->addField('name', 'public.ltree');

        $this->pk_fields = array('reference');
    }
}