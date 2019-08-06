<?php

declare(strict_types=1);

namespace Rescue\Container\Exception;

use Exception;

class ContainerNotFoundException extends Exception implements NotFoundExceptionInterface
{

}
