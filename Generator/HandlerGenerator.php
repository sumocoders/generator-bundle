<?php

namespace SumoCoders\GeneratorBundle\Generator;

use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use CG\Generator\PhpParameter;
use CG\Generator\PhpProperty;
use CG\Generator\Writer;
use Doctrine\ORM\EntityManager;
use ReflectionClass;
use SumoCoders\GeneratorBundle\Shared\Module;

final class HandlerGenerator extends Generator
{
    const CREATE_COMMAND = 'Create';
    const UPDATE_COMMAND = 'Update';
    const DELETE_COMMAND = 'Delete';

    /**
     * @param string $handlerType
     * @param Module $module
     * @param ReflectionClass $entityReflection
     * @param PhpClass $command
     *
     * @return mixed
     */
    public function generate(
        $handlerType,
        Module $module,
        ReflectionClass $entityReflection,
        PhpClass $command
    ) {
        $handlerName = $handlerType . $entityReflection->getShortName() . 'Handler';

        $handlerClass = new PhpClass();
        $handlerClass->setName(
            $this->createClassName(
                $module,
                $handlerName,
                'Command'
            )
        );
        $handlerClass->setFinal(true);
        $handlerClass->addUseStatement(EntityManager::class);

        $properties = $this->generateProperties();

        $handlerClass->setProperties($properties);

        $method = $this->generateHandleMethod($command);
        $handlerClass->setMethod($method);

        $constructor = $this->generateConstructor();
        $handlerClass->setMethod($constructor);

        return $handlerClass;
    }

    /**
     * @param PhpClass $command
     *
     * @return PhpMethod
     */
    private function generateHandleMethod(PhpClass $command)
    {
        $method = PhpMethod::create('handle');

        $writer = new Writer();
        $writer->writeln('// @TODO: Handle command');
        $method->setBody($writer->getContent());

        $parameter = new PhpParameter();
        $parameter->setName(lcfirst($command->getShortName()));
        $parameter->setType($command->getShortName());

        $method->addParameter($parameter);
        $method->setVisibility('public');

        return $method;
    }

    /**
     * @return PhpMethod
     */
    private function generateConstructor()
    {
        $method = PhpMethod::create('__construct');
        $writer = new Writer();

        $entityManagerParameter = new PhpParameter();
        $entityManagerParameter->setType('EntityManager');
        $entityManagerParameter->setName('entityManager');

        $writer->writeln('$this->entityManager = $entityManager;');

        $method->addParameter($entityManagerParameter);

        $method->setBody($writer->getContent());

        return $method;
    }

    /**
     * @return array
     */
    private function generateProperties()
    {
        $properties = [];

        $property = PhpProperty::create('entityManager');
        $property->setDocblock("/**\n * @var EntityManager\n */\n");

        $properties[] = $property;

        return $properties;
    }
}
