<?php

namespace SumoCoders\GeneratorBundle\Generator;

use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use CG\Generator\PhpProperty;
use CG\Generator\Writer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class DataTransferObjectGenerator extends Generator
{
    /**
     * @var DefaultGeneratorStrategy
     */
    private $strategy;

    public function __construct()
    {
        $this->strategy = new DefaultGeneratorStrategy();
    }

    /**
     * @param BundleInterface $bundle
     * @param string $entity
     */
    public function generate($bundle, $entity)
    {
        $reflect = new ReflectionClass($bundle->getNamespace() . '\\' . $entity);

        $properties = $this->getProperties($reflect);

        $class = new PhpClass();
        $class->setName(
            $this->createClassName(
                $bundle,
                $reflect->getShortName() . 'DataTransferObject',
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

        $this->saveFileContent(
            $this->createFileName($bundle, $reflect->getShortName() . 'DataTransferObject', 'DataTransferObject'),
            $this->strategy->generate($class)
        );
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
     * @param BundleInterface $bundle
     * @param string $className
     * @param string $root
     *
     * @return string
     */
    private function createClassName(BundleInterface $bundle, $className, $root)
    {
        return $bundle->getNamespace() . '\\' . $root . '\\' . $className;
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

    /**
     * @param BundleInterface $bundle
     * @param string $class
     * @param string $root
     *
     * @return string
     */
    private function createFileName(BundleInterface $bundle, $class, $root)
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                $bundle->getPath(),
                $root,
                str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php',
            ]
        );
    }
}
