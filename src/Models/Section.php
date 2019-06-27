<?php

namespace IbonKonesa\Catastro\Models;


/**
 * Class Section
 * @package IbonKonesa\Catastro\Models
 */
class Section
{

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
    public $block;
    /**
     * @var string
     */
    public $stair;
    /**
     * @var string
     */
    public $floor;
    /**
     * @var string
     */
    public $door;


    /**
     * Section constructor.
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->usageType = (string)$xml->lcd;
        $this->area = (string)$xml->dfcons->stl;

        if ($xml->dt) {

            $loint = $xml->dt->lourb->loint;

            if ($loint->bq) $this->block = (string)$loint->bq;
            if ($loint->es) $this->stair = (string)$loint->es;
            if ($loint->pt) $this->floor = (string)$loint->pt;
            if ($loint->pu) $this->door = (string)$loint->pu;

        }

    }

}
