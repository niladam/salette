<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Debuggers;

use Salette\Debugging\DebugData;
use Salette\Debugging\Drivers\DebuggingDriver;

class MissingDependencyDebugger extends DebuggingDriver
{
    
    public function name()
    {
        return 'missingDependency';
    }

    /**
     * Determines if the debugging driver can be used
     *
     * E.g if it has the correct dependencies
     */
    public function hasDependencies()
    {
        return false;
    }

    
    public function send(DebugData $data)
    {
        //
    }
}
