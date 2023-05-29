<?php

namespace Mdigi\QrisBankJateng\Services;

use Mdigi\QrisBankJateng\Dtos\QrisLink;

interface QrisService
{
    public function getLink($idBilling): QrisLink;

    public function verifyExternalApiKey(string $apiKey): bool;

    public function makeExternalApiKey(): string;

}
