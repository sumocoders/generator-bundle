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
     * @param Application $application
     * @param string $modulePath
     */
    public function __construct(Application $application, $modulePath)
    {
        $this->application = $application;
        $this->moduleFQN = $modulePath;
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
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $entityFQN);

        $matches = [];

        if ($application === Application::fromString(Application::FORK)) {
            preg_match('/((?:Backend|Frontend)\/Modules\/\w+)/.*/', $path, $matches);
        } else {
            preg_match("/(\w+Bundle)\/.*/", $path, $matches);
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
        return $this->moduleFQN;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $this->moduleFQN;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->moduleFQN;
    }
}
