<?php

namespace lajax\translatemanager\translation\client;

use Yii;
use yii\base\Object;
use yii\base\NotSupportedException;

/**
 * API client that uses cURL lib for communication.
 *
 * @author moltam
 */
class CurlApiClient extends Object implements TranslatorApiClient
{
    /**
     * @var array The cURL client options.
     */
    public $curlOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
    ];

    /**
     * @inheritdoc
     */
    public function send($url, $httpMethod = 'GET', array $bodyParams = [])
    {
        Yii::trace("Sending $httpMethod request to: $url", 'translatemanager');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt_array($ch, $this->curlOptions);
        curl_setopt_array($ch, $this->getHttpMethodOptions($httpMethod, $bodyParams));

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            throw new ClientException("cURL error occured: $error", $errno);
        }

        return $response;
    }

    /**
     * @param string $httpMethod
     * @param array $bodyParams [optional]
     *
     * @return array
     *
     * @throws NotSupportedException
     */
    protected function getHttpMethodOptions($httpMethod, array $bodyParams = [])
    {
        switch ($httpMethod) {
            case 'GET':
                return []; // GET is default, no options needed.
            case 'POST':
                return [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($bodyParams),
                ];
            default:
                throw new NotSupportedException("Unsupported HTTP method: $httpMethod.");
        }
    }
}
