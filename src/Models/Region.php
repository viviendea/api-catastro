<?php

namespace IbonKonesa\Catastro\Models;


/**
 * Class Region
 * @package IbonKonesa\Catastro\Models
 */
class Region
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
     * Region constructor.
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->name = (string)$xml->np;
        $this->code = (string)$xml->cpine;

    }

}
