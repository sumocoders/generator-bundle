<?php

namespace SumoCoders\GeneratorBundle\Generator;

use CG\Generator\PhpClass;
use CG\Generator\PhpProperty;
use ReflectionClass;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class CommandGenerator extends Generator
{
    const CREATE_COMMAND = 'Create';
    const UPDATE_COMMAND = 'Update';
    const DELETE_COMMAND = 'Delete';

    /**
     * @param string $type
     * @param BundleInterface $bundle
     * @param ReflectionClass $entityReflection
     * @param PhpClass $dataTransferObject
     *
     * @return PhpClass
     */
    public function generate(
        $type,
        BundleInterface $bundle,
        ReflectionClass $entityReflection,
        PhpClass $dataTransferObject
    ) {
        $commandName = $type . $entityReflection->getShortName();

        $class = new PhpClass();
        $class->setName(
            $this->createClassName(
                $bundle,
                $commandName,
                'Command'
            )
        );

        if (self::UPDATE_COMMAND === $type || self::DELETE_COMMAND === $type) {
            $propertyName = lcfirst($entityReflection->getShortName());
            $property = PhpProperty::create($propertyName);
            $property->setVisibility('public');
            $property->setDocblock(
                sprintf("/**\n * @var %s \$%s\n */\n", $entityReflection->getShortName(), $propertyName)
            );

            $class->setProperty($property);
            $class->addUseStatement($entityReflection->getName());
        }

        $class->setFinal(true);
        $class->setParentClassName($dataTransferObject->getShortName());
        $class->addUseStatement($dataTransferObject->getName());

        return $class;
    }
}
