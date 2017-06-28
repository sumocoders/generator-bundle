<?php

namespace SumoCoders\GeneratorBundle\Tests\Generator;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionProperty;
use SumoCoders\GeneratorBundle\Generator\DataTransferObjectGenerator;
use SumoCoders\GeneratorBundle\SumoCodersGeneratorBundle;

class DataTransferObjectGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testDataTransferObjectGenerator()
    {
        $generator = new DataTransferObjectGenerator();

        $bundle = $this->getMockBuilder(SumoCodersGeneratorBundle::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bundle->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->will($this->returnValue('SumoCoders\GeneratorBundle'));

        $propertyId = $this->getMockBuilder(ReflectionProperty::class)
            ->disableOriginalConstructor()
            ->getMock();

        $propertyId->expects($this->atLeastOnce())
            ->method('getDocComment')
            ->will($this->returnValue("/**\n * @var int\n * @ORM\\GeneratedValue\n **/"));
        $propertyId->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('id'));

        $propertyName = $this->getMockBuilder(ReflectionProperty::class)
            ->disableOriginalConstructor()
            ->getMock();
        $propertyName->expects($this->atLeastOnce())
            ->method('getDocComment')
            ->will($this->returnValue("/**\n * @var string\n **/"));
        $propertyName->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('name'));

        $entityReflection = $this->getMockBuilder(ReflectionClass::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityReflection->expects($this->atLeastOnce())
            ->method('getShortName')
            ->will($this->returnValue('Blog'));
        $entityReflection->expects($this->atLeastOnce())
            ->method('getProperties')
            ->willReturn([
                $propertyId, $propertyName,
            ]);

        $dataTransferObjectClass = $generator->generate($bundle, $entityReflection);

        $this->assertNotNull($dataTransferObjectClass);
        $this->assertCount(1, $dataTransferObjectClass->getProperties());
    }
}
