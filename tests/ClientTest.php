<?php

namespace IbonKonesa\Catastro;

use IbonKonesa\Catastro\Models\Dnp;
use IbonKonesa\Catastro\Models\Region;
use IbonKonesa\Catastro\Models\Street;
use IbonKonesa\Catastro\Models\StreetType;
use IbonKonesa\Catastro\Models\Town;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{


    public function testGetClient()
    {

        $client = new Client();
        $this->assertTrue($client->getClient() instanceof \GuzzleHttp\Client);

    }


    public function testGetStreetTypes()
    {

        $client = new Client();
        $streetTypes = $client->getStreetTypes();

        $this->assertTrue(is_array($streetTypes));
        $this->assertTrue($streetTypes[0] instanceof StreetType);

    }


    public function testGetRegions()
    {
        $client = new Client();
        $regions = $client->getRegions();

        $this->assertTrue(is_array($regions));
        $this->assertTrue($regions[0] instanceof Region);


        //PASS REGION TO NEXT TESTS

        $data = ['region' => $regions[rand(0, count($regions) - 1)]];

        return $data;

    }


    /**
     * @depends testGetRegions
     */

    public function testGetAllTowns($data)
    {
        $client = new Client();
        $towns = $client->getTowns($data['region']);

        $this->assertTrue(is_array($towns));
        $this->assertTrue($towns[0] instanceof Town);

        //ADD TOWN
        return $data + ['town' => $towns[rand(0, count($towns) - 1)]];

    }


    /**
     * @depends testGetRegions
     */
    public function testGetTownsByQuery($data)
    {

        $client = new Client();
        $towns = $client->getTowns($data['region'], 'zas');

        $this->assertTrue(is_array($towns));

        if (count($towns) > 0) {
            $this->assertTrue($towns[0] instanceof Town);
        }


    }


    /**
     * @depends testGetAllTowns
     */

    public function testGetStreets($data)
    {

        $client = new Client();
        $streets = $client->getStreets($data['region'], $data['town']);

        $this->assertTrue(is_array($streets));
        $this->assertTrue($streets[0] instanceof Street);


        //ADD STREET
        return $data + ['street' => $streets[rand(0, count($streets) - 1)]];
    }


    /**
     * @depends testGetAllTowns
     */

    public function testGetStreetsWithParams($data)
    {

        $client = new Client();

        $streetTypes = $client->getStreetTypes();
        $streetTypeCl = $streetTypes[57];

        $streets = $client->getStreets($data['region'], $data['town'], $streetTypeCl, 'FORASTEROS');

        $this->assertTrue(is_array($streets));

        if (count($streets) > 0) {
            $this->assertTrue($streets[0] instanceof Street);
        }

    }


    /**
     * @depends testGetStreets
     */

    public function testCheckNumber($data)
    {


        $client = new Client();

        $number = rand(1, 5);

        $response = $client->checkNumber($data['region'], $data['town'], $data['street'], $number);

        $this->assertTrue(property_exists($response, 'numberExists'));

        if (!$response->numberExists) {
            $this->assertTrue(is_array($response->nearNumbers));
        }

        //ADD NUMBER
        return $data + ['number' => $number];

    }


    /**
     * @depends testCheckNumber
     */

    public function testGetDataByLocation($data)
    {

        $client = new Client();

        $response = $client->getDataByLocation($data['region'], $data['town'], $data['street'], $data['number']);


        if (!property_exists($response, 'error')) {

            $this->assertTrue(property_exists($response, 'concreteResult'));

            if ($response->concreteResult) {

                $this->assertTrue($response->data instanceof Dnp);
                return $data + ['reference' => $response->data->reference];

            } else {

                $this->assertTrue(is_array($response->data));
                return $data + ['reference' => $response->data[0]];

            }


        } else {

            $this->assertTrue(gettype($response->error) === 'string');
            return null;

        }


    }


    /**
     * @depends testGetDataByLocation
     */

    public function testGetDataByReference($data)
    {

        if ($data) {

            $client = new Client();

            $response = $client->getDataByReference($data['region'], $data['town'], $data['reference']);


            if (!property_exists($response, 'error')) {

                $this->assertTrue(property_exists($response, 'concreteResult'));

                if ($response->concreteResult) {

                    $this->assertTrue($response->data instanceof Dnp);

                } else {

                    $this->assertTrue(is_array($response->data));

                }


            } else {

                $this->assertTrue(gettype($response->error) === 'string');
                return null;

            }


        } else {

            $this->assertTrue(is_null($data));


        }
    }


}
