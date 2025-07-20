<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Debuggers;

use Salette\Debugging\DebugData;
use Salette\Debugging\Drivers\DebuggingDriver;

class ArrayDebugger extends DebuggingDriver
{
    
    protected array $requests = [];

    
    protected array $responses = [];

    
    public function name()
    {
        return 'array';
    }

    
    public function send(DebugData $data)
    {
        if ($data->wasNotSent()) {
            $this->requests[] = $this->formatData($data);
        }

        if ($data->wasSent()) {
            $this->responses[] = $this->formatData($data);
        }
    }

    /**
     * Get request
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Get response
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Determines if the debugging driver can be used
     *
     * E.g if it has the correct dependencies
     */
    public function hasDependencies()
    {
        return true;
    }
}
