<?php

namespace lajax\translatemanager\translation\client;

/**
 * Communicates with a translator API.
 *
 * @author moltam
 */
interface TranslatorApiClient
{
    /**
     * Sends a request to the specified URL.
     *
     * @param string $url The URL of the called API. Query parameters must sent here.
     * @param string $httpMethod [optional]
     * <p>The used HTTP method (GET, POSt, etc).</p>
     * @param array $bodyParams [optional]
     * <p>The parameters sent in the request body (e.g. for an POST request). Query parameters cannot be sent here.</p>
     *
     * @return string The raw response returned from the API.
     *
     * @throws ClientException If an error occurs during the communication (e.g.: HTTP 4xx, 5xx code, etc.).
     */
    public function send($url, $httpMethod = 'GET', array $bodyParams = []);
}
