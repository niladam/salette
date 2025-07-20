<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Salette\Tests\Fixtures\Connectors\TestConnector;

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function apiUrl()
{
    return 'https://tests.saloon.dev/api';
}

function connector()
{
    return new TestConnector;
}

/**
 * @param resource $output
 */
function getCustomVarDump($output)
{
    return function ($var, $label = null) use ($output) {
        $dumper = new CliDumper;
        $cloner = new VarCloner;

        $var = $cloner->cloneVar($var);

        // Check if this is a debug call by looking at the backtrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $isDebugCall = false;
        $debugType = null;

        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && $trace['class'] === 'Salette\Helpers\Debugger') {
                $isDebugCall = true;
                if (strpos($trace['function'], 'Request') !== false) {
                    $debugType = 'Request';
                } elseif (strpos($trace['function'], 'Response') !== false) {
                    $debugType = 'Response';
                }
                break;
            }
        }

        if ($isDebugCall && $debugType) {
            fwrite($output, "Saloon {$debugType} (UserRequest) -> ");
        }

        $dumper->dump($var, $output);
    };
}
