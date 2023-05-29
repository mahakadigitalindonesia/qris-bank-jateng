<?php

namespace Mdigi\QrisBankJateng\Services;

use Illuminate\Support\Facades\Crypt;
use Mdigi\QrisBankJateng\Dtos\QrisLink;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QrisServiceImpl implements QrisService
{
    private $baseURL;
    private $username;
    private $password;
    private $apiKey;
    const URL_GET_TOKEN = '/getToken';
    const URL_GET_LINK = '/getLink';

    public function __construct($baseURL, $username, $password, $apiKey)
    {
        $this->baseURL = $baseURL;
        $this->username = $username;
        $this->password = $password;
        $this->apiKey = $apiKey;
    }

    public function getLink($idBilling) : QrisLink
    {
        try {
            if (!Cache::has('qris_token')) {
                $token = $this->http()->post(self::URL_GET_TOKEN, [
                    'key' => $this->apiKey,
                    'idBilling' => $idBilling
                ]);

                Log::info('QRIS API: Get Token', [
                    'idbilling' => $idBilling,
                    'response' => $token->body()
                ]);

                if (!$token->ok()) {
                    return QrisLink::create($token->status());
                }

                if ($token->json('errCode') !== '00') {
                    return QrisLink::create($token->json('errCode'));
                }

                if (is_null($token->json('token') || empty($token->json('token')))) {
                    return QrisLink::create('token tidak valid');
                }
                Cache::put('qris_token_' . $idBilling, $token->json('token'), now()->addMinutes(4));
            }

            $link = $this->http()->post(self::URL_GET_LINK, [
                'token' => Cache::get('qris_token_' . $idBilling)
            ]);

            Log::info('QRIS API: Get Link', [
                'idbilling' => $idBilling,
                'response' => $link->body()
            ]);
            if (!$link->ok()) {
                return QrisLink::create($link->status());
            }

            if ($link->json('errCode') !== '00') {
                return QrisLink::create($link->json('errCode'));
            }

            if (is_null($link->json('data')) || empty($link->json('data'))) {
                return QrisLink::create('link qris tidak valid.');
            }

            if ($this->isJson((string)$link->json('data'))) {
                return QrisLink::create('88');
            }

            return QrisLink::create('00', (string)$link->json('data'));
        } catch (\Exception $e) {
            return QrisLink::create('99');
        }
    }

    public function verifyExternalApiKey(string $apiKey): bool
    {
        return Crypt::decrypt($apiKey) === self::getExternalApiKey();
    }

    public function makeExternalApiKey(): string
    {
        return Crypt::encrypt(self::getExternalApiKey());
    }

    private static function getExternalApiKey(): string
    {
        return bin2hex(config('qris.base_url') . '|' . config('qris.username') . '|' . config('qris.api_key'));
    }

    private function isJson($resoponse)
    {
        try {
            $json = Http::get($resoponse);
            return !is_null($json->json('errCode'));
        } catch (\Exception $e) {
            return false;
        }
    }

    private function http()
    {
        return Http::baseUrl($this->baseURL)
            ->withBasicAuth($this->username, $this->password);
    }


}
