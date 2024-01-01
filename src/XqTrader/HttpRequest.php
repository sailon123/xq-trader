<?php

namespace XqTrader;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait HttpRequest
{

    /**
     * @var int
     */
    protected $maxRetries = 5;

    protected array $headers = [];

    /**
     * @return \Closure
     */
    protected function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $requestException = null
        ) {
            if ($retries >= $this->maxRetries) {
                return false;
            }

            if ($requestException instanceof ConnectException) {
                return true;
            }

            if ($response && $response->getStatusCode() >= 500) {
                return true;
            }
            return false;
        };
    }

    /**
     * @return \Closure
     */
    protected function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }

    /**
     * @param array $options
     * @return Client
     */
    protected function getHttpClient(array $options = [])
    {
        return new Client($options);
    }

    /**
     * @return array
     */
    public function getBaseOptions()
    {
        $options = [
            'timeout' => method_exists($this, 'getTimeout') ? $this->getTimeout() : 30.0,
            'handler' => $this->getHandler(),
            'headers' => $this->getHeader(),
        ];
        return $options;
    }

    /**
     * @return HandlerStack
     */
    protected function getHandler()
    {
        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        return $handlerStack;
    }

    /**
     * @param $method
     * @param $uri
     * @param array $options
     * @return mixed|string
     */
    public function request($method, $uri, $options = [])
    {
        $response = $this->unwrapResponse($this->getHttpClient($this->getBaseOptions())->{$method}($uri, $options));
        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return mixed|string
     */
    protected function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();
        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        }
        return $contents;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @param $header
     * @return $this
     */
    public function setHeader($header)
    {
        $this->headers = $header;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->headers;
    }

    /**
     * @param $uri
     * @param array $query
     * @param array $headers
     * @return mixed|string
     */
    public function get($uri, $query = [], $headers = [])
    {
        return $this->request('get', $uri, [
            'headers' => $headers,
            'query' => $query
        ]);
    }

    /**
     * @param $uri
     * @param array $query
     * @param array $headers
     * @return mixed|string
     */
    public function getJson($uri, $query = [], $headers = [])
    {
        return $this->request('get', $uri, [
            'headers' => $headers,
            'json' => $query
        ]);
    }

    /**
     * @param $uri
     * @param array $params
     * @param array $headers
     * @return mixed|string
     */
    public function post($uri, $params = [], $headers = [])
    {
        return $this->request('post', $uri, [
            'headers' => $headers,
            'form_params' => $params,
        ]);
    }

    /**
     * @param $uri
     * @param array $params
     * @param array $headers
     * @return mixed|string
     */
    public function postJson($uri, $params = [], $headers = [])
    {
        return $this->request('post', $uri, [
            'headers' => $headers,
            'json' => $params,
        ]);
    }

    public function postFile($uri, $params = [], $headers = [], $timeout = 120)
    {
        return $this->request('post', $uri, [
            'headers' => $headers,
            'multipart' => $params,
            'timeout' => $timeout,
        ]);
    }
}
