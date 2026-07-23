<?php
namespace app\service;

class GeoPublishException extends \RuntimeException
{
    private $errorCode;
    private $httpStatus;

    public function __construct(string $errorCode, string $message, int $httpStatus)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->httpStatus = $httpStatus;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }
}
