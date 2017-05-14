<?php

namespace lajax\translatemanager\translation;

use Yii;
use yii\helpers\Json;

/**
 * Translator using the Google Cloud Translation API.
 *
 * More information on this service: https://cloud.google.com/translate/
 *
 * Required configuration:
 *
 * ```php
 * [
 *     'class' => 'lajax\translatemanager\translation\GoogleTranslator',
 *     'apiKey' => 'YOUR_API_KEY',
 * ]
 * ```
 *
 * @author moltam
 */
class GoogleTranslator extends BaseTranslator
{
    /**
     * @var string The API key for authentication.
     */
    public $apiKey;

    /**
     * @var string
     */
    private static $baseApiUrl = 'https://translation.googleapis.com';

    /**
     * @inheritdoc
     */
    public function translate($text, $target, $source = null, $format = 'html')
    {
        $response = $this->apiClient->send($this->buildApiUrl('/language/translate/v2'), 'POST', [
            'key' => $this->apiKey,
            'q' => $text,
            'target' => substr($target, 0, 2),
            'source' => substr($source, 0, 2),
            'format' => $format,
        ]);

        $decodesResponse = $this->decodeResponse($response);
        $this->checkResponse($decodesResponse);

        return $decodesResponse['data']['translations'][0]['translatedText'];
    }

    /**
     * @inheritdoc
     */
    public function detect($text)
    {
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
    }

    /**
     * @param string $uri
     * @param array $queryParams [optional]
     *
     * @return string
     */
    private function buildApiUrl($uri, array $queryParams = [])
    {
        return self::$baseApiUrl . $uri . '?' . http_build_query($queryParams);
    }

    /**
     * @param string $response
     *
     * @return array
     *
     * @throws Exception
     */
    private function decodeResponse($response)
    {
        $decodesResponse = Json::decode($response);
        if (!$decodesResponse) {
            throw new Exception('Invalid API response: json decode failed!');
        }

        return $decodesResponse;
    }

    /**
     * @param array $response
     *
     * @throws Exception
     */
    private function checkResponse($response)
    {
        if (isset($response['error'])) {
            $error = $response['error'];
            Yii::error('API response: ' . var_export($response, true), 'translatemanager');

            throw new Exception("API error: $error[message]", $error['code']);
        }
    }
}
