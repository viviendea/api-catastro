<?php

namespace IbonKonesa\Catastro\Models;


/**
 * Class StreetType
 * @package IbonKonesa\Catastro\Models
 */
class StreetType
{

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $code;


    /**
     * StreetType constructor.
     * @param \stdClass $source
     */
    public function __construct(\stdClass $source)
    {
        $this->name = (string)$source->name;
        $this->code = (string)$source->code;

    }

}
