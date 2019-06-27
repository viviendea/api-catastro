<?php

namespace IbonKonesa\Catastro\Models;


/**
 * Class Dnp
 * @package IbonKonesa\Catastro\Models
 */
class Dnp
{

    /**
     * @var string
     */
    public $reference;
    /**
     * @var string
     */
    public $usageType;
    /**
     * @var string
     */
    public $area;
    /**
     * @var string
     */
    public $buildingYear;
    /**
     * @var array
     */
    public $sections = [];


    /**
     * Dnp constructor.
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {

        foreach ($xml->bi->idbi->rc->children() as $refPart) $this->reference .= (string)$refPart;
        if ($xml->bi->debi->luso) $this->usageType = (string)$xml->bi->debi->luso;
        if ($xml->bi->debi->sfc) $this->area = (string)$xml->bi->debi->sfc;
        if ($xml->bi->debi->ant) $this->buildingYear = (string)$xml->bi->debi->ant;

        if ($xml->lcons) {
            foreach ($xml->lcons->children() as $cons) {
                array_push($this->sections, new Section($cons));
            }
        }

    }

}
