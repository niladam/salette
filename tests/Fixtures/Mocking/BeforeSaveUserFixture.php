<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Mocking;

use Salette\Http\Faking\Fixture;
use Salette\Data\RecordedResponse;

class BeforeSaveUserFixture extends Fixture
{
    /**
     * Define the name of the fixture
     */
    protected function defineName(): string
    {
        return 'user';
    }

    /**
     * Modify the fixture before it is sent
     */
    protected function beforeSave(RecordedResponse $recordedResponse): RecordedResponse
    {
        $recordedResponse->statusCode = 222;

        return $recordedResponse;
    }
}
