<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

use Platform\Package\Components\Time\DateTime;

class MetricDto
{
    public function __construct(
        public int $requestedFromClient = 0,
        public int $acceptedOnServer = 0,
        public int $sentOnMt = 0,
        public int $answeredFromMt = 0,
        public int $processedOnMt = 0,
        public int $acceptedEventOnServer = 0,
        public int $processedOnServer = 0,
        public int $sentOnNt = 0,
        public int $answeredFromNt = 0,
    ) {
    }

    public static function start(int $requestedFromClient) : self
    {
        $metric = new self($requestedFromClient);

        $metric->acceptingOnServer();

        return $metric;
    }

    public function acceptingOnServer() : void
    {
        $this->acceptedOnServer = DateTime::now()->getTimestampMs();
    }

    public function sendingOnMt() : void
    {
        $this->sentOnMt = DateTime::now()->getTimestampMs();
    }

    public function sendingOnNt() : void
    {
        $this->sentOnNt = DateTime::now()->getTimestampMs();
    }

    public function answeringFromMt() : void
    {
        $this->answeredFromMt = DateTime::now()->getTimestampMs();
    }

    public function answeringFromNt() : void
    {
        $this->answeredFromNt = DateTime::now()->getTimestampMs();
    }

    public function processedOnMt(DateTime $time) : void
    {
        $this->processedOnMt = $time->getTimestampMs();
    }

    public function acceptingEvent() : void
    {
        $this->acceptedEventOnServer = DateTime::now()->getTimestampMs();
    }

    public function processedOnServer() : void
    {
        $this->processedOnServer = DateTime::now()->getTimestampMs();
    }
}
