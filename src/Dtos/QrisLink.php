<?php

namespace Mdigi\QrisBankJateng\Dtos;

class QrisLink
{
    public const RESPONSE_SUCCESS = '00';
    public $errorCode = self::RESPONSE_SUCCESS;
    public $link;

    public function __construct($errorCode, $link = null)
    {
        $this->errorCode = $errorCode;
        $this->link = $link;
    }

    public static function create($errorCode, $link = null)
    {
        return new self($errorCode, $link);
    }
}
