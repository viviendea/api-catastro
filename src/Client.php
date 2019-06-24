<?php


namespace IbonKonesa\Catastro;


class Client
{

    private $client;

    public function __construct()
    {
        $this->client = "TEST";
    }


    public function getClient()
    {


        error_log($this->client);

    }


}
