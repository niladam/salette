<?php

declare(strict_types=1);

namespace Salette\Traits\Connector;

use Salette\Contracts\Sender;
use Salette\Senders\GuzzleSender;

trait HasSender
{
    /**
     * Specify the default sender
     *
     * @var class-string<Sender> Fully-qualified class name of a Sender implementation
     */
    protected string $defaultSender = GuzzleSender::class;

    /**
     * The request sender.
     */
    protected ?Sender $sender = null;

    /**
     * Manage the request sender.
     */
    public function sender(): Sender
    {
        return $this->sender ??= $this->defaultSender();
    }

    /**
     * Define the default request sender.
     */
    protected function defaultSender(): Sender
    {
        $class = $this->defaultSender ?: GuzzleSender::class;

        return new $class();
    }
}
