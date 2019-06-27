<?php


namespace IbonKonesa\Catastro;

use IbonKonesa\Catastro\Models\Dnp;
use IbonKonesa\Catastro\Models\Region;
use IbonKonesa\Catastro\Models\Street;
use IbonKonesa\Catastro\Models\StreetType;
use IbonKonesa\Catastro\Models\Town;

/**
 * Class Client
 * @package IbonKonesa\Catastro
 */
class Client
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client(['base_uri' => 'https://ovc.catastro.meh.es/ovcservweb/OVCSWLocalizacionRC/OVCCallejero.asmx/', 'verify' => false]);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }


    /**
     * @return array
     */
    public function getRegions()
    {

        $client = $this->getClient();
        $data = $client->get('ConsultaProvincia')->getBody();

        $xml = simplexml_load_string($data);

        $regions = [];

        foreach ($xml->provinciero->children() as $region) {
            array_push($regions, new Region($region));
        }

        return $regions;
    }


    /**
     * @param Region $region
     * @param string $query
     * @return array
     */
    public function getTowns(Region $region, $query = '')
    {

        $client = $this->getClient();
        $data = $client->post('ConsultaMunicipio', ['form_params' => ['Provincia' => $region->name, 'Municipio' => $query]])->getBody();

        $xml = simplexml_load_string($data);

        $towns = [];

        if ($xml->municipiero) {
            foreach ($xml->municipiero->children() as $town) {
                array_push($towns, new Town($town));
            }
        }

        return $towns;

    }


    /**
     * @return array
     */
    public function getStreetTypes()
    {

        $data = json_decode(file_get_contents(__DIR__ . '/streetTypes.json'));

        $streetTypes = [];

        foreach ($data as $streetType) {
            array_push($streetTypes, new StreetType($streetType));
        }

        return $streetTypes;

    }


    /**
     * @param Region $region
     * @param Town $town
     * @param StreetType|null $type
     * @param string $query
     * @return array
     */
    public function getStreets(Region $region, Town $town, StreetType $type = null, $query = '')
    {

        $client = $this->getClient();
        $data = $client->post('ConsultaVia',
            ['form_params' =>
                [
                    'Provincia' => $region->name,
                    'Municipio' => $town->name,
                    'TipoVia' => $type ? $type->code : '',
                    'NombreVia' => $query
                ]
            ])->getBody();


        $xml = simplexml_load_string($data);
        $streets = [];

        if ($xml->callejero) {
            foreach ($xml->callejero->children() as $street) {
                array_push($streets, new Street($street));
            }

        }

        return $streets;

    }


    /**
     * @param Region $region
     * @param Town $town
     * @param Street $street
     * @param $number
     * @return \stdClass
     */
    public function checkNumber(Region $region, Town $town, Street $street, $number)
    {

        $client = $this->getClient();

        $data = $client->post('ConsultaNumero',
            ['form_params' =>
                [
                    'Provincia' => $region->name,
                    'Municipio' => $town->name,
                    'TipoVia' => $street->streetType,
                    'NomVia' => $street->name,
                    'Numero' => $number
                ]
            ])->getBody();


        $xml = simplexml_load_string($data);


        $response = new \stdClass();

        $response->numberExists = !$xml->lerr;
        $response->nearNumbers = [];

        if (!$response->numberExists && $xml->numerero) {
            foreach ($xml->numerero->children() as $nump) {
                array_push($response->nearNumbers, (integer)$nump->num->pnp);
            }
        }

        return $response;

    }


    /**
     * @param Region $region
     * @param Town $town
     * @param Street $street
     * @param $number
     * @param string $block
     * @param string $stair
     * @param string $floor
     * @param string $door
     * @return \stdClass
     */
    public function getDataByLocation(Region $region, Town $town, Street $street, $number, $block = '', $stair = '', $floor = '', $door = '')
    {

        $client = $this->getClient();

        $data = $client->post('Consulta_DNPLOC',
            ['form_params' =>
                [
                    'Provincia' => $region->name,
                    'Municipio' => $town->name,
                    'Sigla' => $street->streetType,
                    'Calle' => $street->name,
                    'Numero' => $number,
                    'Bloque' => $block,
                    'Escalera' => $stair,
                    'Planta' => $floor,
                    'Puerta' => $door
                ]
            ])->getBody();


        $xml = simplexml_load_string($data);

        $response = new \stdClass();

        if ($xml->lerr) {
            $response->error = (string)$xml->lerr->err->des;
        }

        if ($xml->bico) {

            $response->concreteResult = true;
            $response->data = new Dnp($xml->bico);

        } else if ($xml->lrcdnp) {

            $response->concreteResult = false;
            $response->data = [];

            foreach ($xml->lrcdnp->children() as $option) {

                $reference = '';
                foreach ($option->rc->children() as $refPart) $reference .= (string)$refPart;
                array_push($response->data, $reference);
            }

        }

        return $response;

    }


    /**
     * @param Region $region
     * @param Town $town
     * @param $reference
     * @return \stdClass
     */
    public function getDataByReference(Region $region, Town $town, $reference)
    {

        $client = $this->getClient();

        $data = $client->post('Consulta_DNPRC',
            ['form_params' =>
                [
                    'Provincia' => $region->name,
                    'Municipio' => $town->name,
                    'rc' => $reference
                ]

            ])->getBody();


        $xml = simplexml_load_string($data);

        $response = new \stdClass();

        if ($xml->lerr) {
            $response->error = (string)$xml->lerr->err->des;
        }

        if ($xml->bico) {

            $response->concreteResult = true;
            $response->data = new Dnp($xml->bico);

        } else if ($xml->lrcdnp) {

            $response->concreteResult = false;
            $response->data = [];

            foreach ($xml->lrcdnp->children() as $option) {

                $reference = '';
                foreach ($option->rc->children() as $refPart) $reference .= (string)$refPart;
                array_push($response->data, $reference);
            }

        }

        return $response;

    }


}
