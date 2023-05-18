<?php

namespace App\DTO;
class AmadeusFlightOfferData
{
    public string $url;
    public array $data;

    public function __construct(string $url, array $data)
    {
        $this->url = $url;
        $this->data = $data;
    }
}