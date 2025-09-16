<?php

namespace Ocpi\Modules\Credentials\Object;

readonly class PartyCode
{
    /**
     * @param string $code
     * @param string $countryCode
     */
    public function __construct(private string $code, private string $countryCode)
    {
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCodeFormatted(): string
    {
        return  $this->countryCode. '*' . $this->code;
    }
}