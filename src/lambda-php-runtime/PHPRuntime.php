<?php

/**
 * Created by PhpStorm.
 * User: ajitem
 * Date: 24/1/19
 * Time: 6:03 PM
 */

namespace LambdaPHPRuntime;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class PHPRuntime implements Runtime
{
    /** @var string */
    private $baseURL;

    /** @var string */
    private $apiVersion;

    /** @var Client */
    private $client;

    /** @var array */
    private $endpoints = [
        'nextInvocation' => '/runtime/invocation/next',
        'invocationResponse' => '/runtime/invocation/%s/response',
        'invocationError' => '/runtime/invocation/%s/error',
        'initError' => '/runtime/init/error'
    ];

    public function __construct(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;
        $this->baseURL = trim(sprintf('http://%s/%s', $_ENV['AWS_LAMBDA_RUNTIME_API'], $this->apiVersion));

        $this->client = new Client();
    }


    public function nextInvocation(): array
    {
        $response = $this->client->get(sprintf('%s%s', $this->baseURL, $this->endpoints['nextInvocation']));

        return [
            'invocationId' => reset($response->getHeader('Lambda-Runtime-Aws-Request-Id')),
            'invokedFunctionArn' => reset($response->getHeader('Lambda-Runtime-Invoked-Function-Arn')),
            'deadlineInMs' => reset($response->getHeader('Lambda-Runtime-Deadline-Ms')),
            'clientContext' => reset($response->getHeader('Lambda-Runtime-Client-Context')),
            'identity' => reset($response->getHeader('Lambda-Runtime-Cognito-Identity')),
            'traceId' => reset($response->getHeader('Lambda-Runtime-Trace-Id')),
            'payload' => json_decode((string)$response->getBody(), true)
        ];
    }

    public function invocationResponse(string $awsRequestId, $response): void
    {
        $this->client->post(
            sprintf('%s%s', $this->baseURL, sprintf($this->endpoints['invocationResponse'], $awsRequestId)),
            [
                RequestOptions::JSON => $response
            ]
        );
    }

    public function invocationError(string $awsRequestId, $error): void
    {
        $this->client->post(
            sprintf('%s%s', $this->baseURL, sprintf($this->endpoints['invocationError'], $awsRequestId)),
            [
                RequestOptions::JSON => $error
            ]
        );
    }

    public function initError($error): void
    {
        $this->client->post(
            sprintf('%s%s', $this->baseURL, $this->endpoints['initError']),
            [
                RequestOptions::JSON => $error
            ]
        );
    }
}