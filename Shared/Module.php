<?php

namespace SumoCoders\GeneratorBundle\Shared;

use Exception;
use SumoCoders\GeneratorBundle\ValueObject\Application;

final class Module
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $moduleFQN;

    /**
     * @var string
     */
    private $name;

    /**
     * @param Application $application
     * @param string $moduleFQN
     */
    private function __construct(Application $application, $moduleFQN)
    {
        $this->application = $application;
        $this->moduleFQN = $moduleFQN;

        $matches = [];
        if ($this->application->equals(Application::fromString(Application::FORK))) {
            preg_match("/(?:Backend|Frontend)\\\\Modules\\\\(\w+)/", $this->moduleFQN, $matches);
        } else {
            preg_match("/\w+Bundle\\\\(\w+)\\.*/", $this->moduleFQN, $matches);
        }
        $this->name = $matches[1];
    }

    /**
     * @param Application $application
     * @param string $entityFQN
     *
     * @throws Exception
     *
     * @return Module
     */
    public static function fromApplicationAndEntity(Application $application, $entityFQN)
    {
        $matches = [];

        if ($application->equals(Application::fromString(Application::FORK))) {
            preg_match("/((?:Backend|Frontend)\\\\Modules\\\\\w+)\\\\.*/", $entityFQN, $matches);
        } else {
            preg_match("/(\w+Bundle)\\.*/", $entityFQN, $matches);
        }

        if (count($matches) !== 2) {
            throw new Exception('There is something wrong with your entity\'s FQN.');
        }

        return new self($application, $matches[1]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR .
            str_replace(
                '\\',
                DIRECTORY_SEPARATOR,
                $this->moduleFQN
            );
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->moduleFQN;
    }
}
