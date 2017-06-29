<?php

namespace SumoCoders\GeneratorBundle\ValueObject;

use SumoCoders\GeneratorBundle\Exception\InvalidApplicationException;

final class Application
{
    const FORK = 'Fork';
    const FRAMEWORK = 'Framework';

    /**
     * @var string
     */
    private $application;

    /**
     * @param string $application
     */
    private function __construct($application)
    {
        if (!in_array($application, self::getPossibleApplications())) {
            throw InvalidApplicationException::withApplication($application);
        }
        $this->application = $application;
    }

    /**
     * @return array
     */
    public static function getPossibleApplications()
    {
        $possibleApplications = [
            self::FORK,
            self::FRAMEWORK,
        ];

        return array_combine($possibleApplications, $possibleApplications);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->application;
    }

    /**
     * @param string $application
     *
     * @return self
     */
    public static function fromString($application)
    {
        return new self($application);
    }
}
