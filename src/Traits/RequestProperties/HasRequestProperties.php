<?php

declare(strict_types=1);

namespace Salette\Traits\RequestProperties;

trait HasRequestProperties
{
    use HasConfig;
    use HasDelay;
    use HasHeaders;
    use HasMiddleware;
    use HasQuery;
}
