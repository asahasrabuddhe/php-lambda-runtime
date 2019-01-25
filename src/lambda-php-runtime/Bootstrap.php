<?php
/**
 * Created by PhpStorm.
 * User: ajitem
 * Date: 25/1/19
 * Time: 10:36 AM
 */

namespace LambdaPHPRuntime;


class Bootstrap
{
    private $runtime;

    private $handler;

    private $function;

    private $request;

    public function __construct()
    {
        $this->runtime = new PHPRuntime('2018-06-01');
    }

    public function initialize()
    {
        try {
            $this->handler = reset(explode('.', $_ENV['_HANDLER']));
            $this->function = next(explode('.', $_ENV['_HANDLER']));

            require_once $_ENV['LAMBDA_TASK_ROOT'] . '/src/' . $this->handler . '.php';

            $this->request = $this->runtime->nextInvocation();
        } catch (\Exception $e) {
            $error = [
                'errorMessage' => 'Initialization Exception',
                'errorType' => $e->getMessage()
            ];

            $this->runtime->initError($error);
        }
    }

    public function process()
    {
        try {
            $response = ($this->function)($this->request['payload'], new Context($this->request));

            $this->runtime->invocationResponse($this->request['invocationId'], $response);
        } catch (\Exception $e) {
            $error = [
                'errorMessage' => 'Runtime Exception',
                'errorType' => $e->getMessage()
            ];

            $this->runtime->invocationError($this->request['invocationId'], $error);
        }
    }
}