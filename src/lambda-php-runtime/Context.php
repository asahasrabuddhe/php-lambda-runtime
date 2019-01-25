<?php
/**
 * Created by PhpStorm.
 * User: ajitem
 * Date: 25/1/19
 * Time: 10:21 AM
 */

namespace LambdaPHPRuntime;

class Context
{
    /** @var string */
    private $awsRequestId;

    /** @var string */
    private $invokedFunctionArn;

    /** @var string */
    private $logGroupName;

    /** @var string */
    private $logStreamName;

    /** @var string */
    private $functionName;

    /** @var string */
    private $functionVersion;

    /** @var string */
    private $memoryLimitInMB;

    /** @var string */
    private $clientContext;

    /** @var string */
    private $identity;

    private $deadlineMs;

    public function __construct(array $request)
    {
        $this->awsRequestId = $response['invocationId'];
        $this->invokedFunctionArn = $response['invokedFunctionArn'];
        $this->logGroupName = $_ENV['AWS_LAMBDA_LOG_GROUP_NAME'];
        $this->logStreamName = $_ENV['AWS_LAMBDA_LOG_STREAM_NAME'];
        $this->functionName = $_ENV['AWS_LAMBDA_FUNCTION_NAME'];
        $this->functionVersion = $_ENV['AWS_LAMBDA_FUNCTION_VERSION'];
        $this->memoryLimitInMB = $_ENV['AWS_LAMBDA_FUNCTION_MEMORY_SIZE'];

        if (isset($response['clientContext']) && $response['clientContext'] != '') {
            $this->clientContext = json_decode($response['clientContext']);
        }

        if (isset($response['identity']) && $response['identity'] != '') {
            $this->identity = json_decode($response['identity']);
        }

        $this->deadlineMs = floatval($response['deadlineMs']);
    }

    /**
     * @return string
     */
    public function getAwsRequestId(): string
    {
        return $this->awsRequestId;
    }

    /**
     * @return string
     */
    public function getInvokedFunctionArn(): string
    {
        return $this->invokedFunctionArn;
    }

    /**
     * @return string
     */
    public function getLogGroupName(): string
    {
        return $this->logGroupName;
    }

    /**
     * @return string
     */
    public function getLogStreamName(): string
    {
        return $this->logStreamName;
    }

    /**
     * @return string
     */
    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    /**
     * @return string
     */
    public function getFunctionVersion(): string
    {
        return $this->functionVersion;
    }

    /**
     * @return string
     */
    public function getMemoryLimitInMB(): string
    {
        return $this->memoryLimitInMB;
    }

    /**
     * @return string
     */
    public function getClientContext(): string
    {
        return $this->clientContext;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function getRemainingTimeInMillis()
    {
        return $this->deadlineMs - round(microtime(true) * 1000);
    }
}