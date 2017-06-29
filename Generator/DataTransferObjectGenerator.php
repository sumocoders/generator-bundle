<?php

namespace SumoCoders\GeneratorBundle\Generator;

use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use CG\Generator\PhpProperty;
use CG\Generator\Writer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ReflectionClass;
use ReflectionProperty;
use SumoCoders\GeneratorBundle\Shared\Module;

final class DataTransferObjectGenerator extends Generator
{
    /**
     * @param Module $module
     * @param $entityReflection
     *
     * @return PhpClass
     */
    public function generate(Module $module, $entityReflection)
    {
        $properties = $this->getProperties($entityReflection);

        $class = new PhpClass();
        $class->setName(
            $this->createClassName(
                $module,
                $entityReflection->getShortName() . 'DataTransferObject',
                'DataTransferObject'
            )
        );
        $class->setAbstract(false);

        if ($this->isPropertiesWithCollections($properties)) {
            $class->setMethod($this->generateConstructor($properties));
        }

        foreach ($properties as $name => $type) {
            $property = PhpProperty::create($name);
            $property->setVisibility('public');
            $property->setDocblock(sprintf("/**\n * @var %s \$%s\n */\n", $type, $name));
            $class->setProperty($property);
        }

        $class->addUseStatement(Collection::class);
        $class->addUseStatement(ArrayCollection::class);

        return $class;
    }

    /**
     * @param ReflectionClass $reflect
     *
     * @return array
     */
    private function getProperties(ReflectionClass $reflect)
    {
        $reflectionProperties = $reflect->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);

        $properties = [];

        foreach ($reflectionProperties as $reflectionProperty) {
            $var = [];

            // Ignore @GeneratedValue properties
            if (preg_match('/\@ORM\\\\GeneratedValue/', $reflectionProperty->getDocComment())) {
                continue;
            }
            preg_match('/.*\@var\s(\w+)/', $reflectionProperty->getDocComment(), $var);
            $properties[$reflectionProperty->getName()] = empty($var) ? '' : $var[1];
        }

        return $properties;
    }

    /**
     * @param array $properties
     *
     * @return bool
     */
    private function isPropertiesWithCollections($properties)
    {
        return !empty(
        array_filter(
            $properties,
            function ($element) {
                return $element === 'Collection';
            }
        )
        );
    }

    /**
     * @param $properties array
     *
     * @return PhpMethod
     */
    private function generateConstructor($properties)
    {
        $method = PhpMethod::create('__constructor');
        $writer = new Writer();

        foreach ($properties as $name => $type) {
            if ($type === 'Collection') {
                $writer->writeln('$this->' . $name . ' = new ArrayCollection();');
            }
        }

        $method->setBody($writer->getContent());

        return $method;
    }
}
