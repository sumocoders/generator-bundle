<?php

namespace SumoCoders\GeneratorBundle\Exception;

use InvalidArgumentException;
use SumoCoders\GeneratorBundle\ValueObject\Application;

final class InvalidApplicationException extends InvalidArgumentException
{
    public static function withApplication($application)
    {
        return new self('"' . $application . '" is not a valid application, possible options are ' .
            implode(',', Application::getPossibleApplications()));
    }
}
