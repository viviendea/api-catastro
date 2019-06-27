<?php

namespace IbonKonesa\Catastro\Models;


/**
 * Class Street
 * @package IbonKonesa\Catastro\Models
 */
class Street
{

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $streetType;
    /**
     * @var string
     */
    public $code;


    /**
     * Street constructor.
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->name = (string)$xml->dir->nv;
        $this->streetType = (string )$xml->dir->tv;
        $this->code = (string)$xml->dir->cv;
    }

}
