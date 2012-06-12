<?php

namespace Marketplace\Gobai\Base;

use \Pomm\Object\BaseObjectMap;
use \Pomm\Exception\Exception;

abstract class MandatoryTagMap extends BaseObjectMap
{
    public function initialize()
    {

        $this->object_class =  'Marketplace\Gobai\MandatoryTag';
        $this->object_name  =  'gobai.mandatory_tag';

        $this->addField('name', 'public.ltree');
        $this->addField('is_mandatory', 'bool');
        $this->addField('priority', 'int2');

        $this->pk_fields = array('name');
    }
}