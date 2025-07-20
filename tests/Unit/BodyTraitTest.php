<?php

declare(strict_types=1);

use Salette\Enums\Method;
use Salette\Exceptions\BodyException;
use Salette\Http\Connector;
use Salette\Requests\Request;
use Salette\Support\Helpers;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\UserRequest;
use Salette\Traits\Body\ChecksForHasBody;
use Salette\Traits\Body\HasFormBody;
use Salette\Traits\Body\HasJsonBody;
use Salette\Traits\Body\HasMultipartBody;
use Salette\Traits\Body\HasStringBody;
use Salette\Traits\Body\HasXmlBody;

test('each of the body traits has the ChecksForWithBody trait added', function (string $trait) {
    $uses = Helpers::classUsesRecursive($trait);

    expect($uses)->toHaveKey(ChecksForHasBody::class, ChecksForHasBody::class);
})->with([
    HasStringBody::class,
    HasFormBody::class,
    HasJsonBody::class,
    HasMultipartBody::class,
    HasXmlBody::class,
]);

test('when a body trait is added to a request without WithBody it will throw an exception', function () {
    $request = new class extends Request
    {
        use HasJsonBody;

        public const METHOD = Method::GET;

        public function resolveEndpoint(): string
        {
            return '';
        }
    };

    $this->expectException(BodyException::class);
    $this->expectExceptionMessage('You have added a body trait without implementing `Salette\Contracts\Body\HasBody` on your request or connector.');

    TestConnector::make()->send($request);
});

test('when a body trait is added to a connector without WithBody it will throw an exception', function () {
    $connector = new class extends Connector
    {
        use HasJsonBody;

        public function resolveBaseUrl(): string
        {
            return '';
        }
    };

    $this->expectException(BodyException::class);
    $this->expectExceptionMessage('You have added a body trait without implementing `Salette\Contracts\Body\HasBody` on your request or connector.');

    $connector->send(new UserRequest());
});
