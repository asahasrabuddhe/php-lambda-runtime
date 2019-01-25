<?php

namespace LambdaPHPRuntime;

interface Runtime {
    public function nextInvocation(): array;

    public function invocationResponse(string $awsRequestId, $response): void;

    public function invocationError(string $awsRequestId, $response): void;

    public function initError($error): void;
}