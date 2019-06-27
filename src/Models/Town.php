<?php

namespace IbonKonesa\Catastro\Models;


/**
 * Class Town
 * @package IbonKonesa\Catastro\Models
 */
class Town
{

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $ineCode;
    /**
     * @var string
     */
    public $mehCode;


    /**
     * Town constructor.
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->name = (string)$xml->nm;
        $this->ineCode = (string )$xml->loine->cm;
        $this->mehCode = (string)$xml->locat->cmc;
    }

}
