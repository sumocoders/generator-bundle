<?php

namespace SumoCoders\GeneratorBundle\Tests\Generator;

use CG\Generator\PhpClass;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use SumoCoders\GeneratorBundle\Generator\CommandGenerator;
use SumoCoders\GeneratorBundle\SumoCodersGeneratorBundle;

class CommandGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testCreateCommandGenerator()
    {
        $commandGenerator = new CommandGenerator();

        $bundle = $this->getMockBuilder(SumoCodersGeneratorBundle::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bundle->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->will($this->returnValue('SumoCoders\GeneratorBundle'));

        $entityReflection = $this->getMockBuilder(ReflectionClass::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityReflection->expects($this->atLeastOnce())
            ->method('getShortName')
            ->will($this->returnValue('Blog'));

        $dataTransferObject = $this->getMockBuilder(PhpClass::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dataTransferObject->expects($this->atLeastOnce())
            ->method('getShortName')
            ->will($this->returnValue('BlogDataTransferObject'));
        $dataTransferObject->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('SumoCoders\GeneratorBundle\BlogDataTransferObject');

        $createCommand = $commandGenerator->generate(
            CommandGenerator::CREATE_COMMAND,
            $bundle,
            $entityReflection,
            $dataTransferObject
        );

        $this->assertNotNull($createCommand);
        $this->assertEquals('CreateBlog', $createCommand->getShortName());
    }

    public function testUpdateCommandGenerator()
    {
        $commandGenerator = new CommandGenerator();

        $bundle = $this->getMockBuilder(SumoCodersGeneratorBundle::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bundle->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->will($this->returnValue('SumoCoders\GeneratorBundle'));

        $entityReflection = $this->getMockBuilder(ReflectionClass::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityReflection->expects($this->atLeastOnce())
            ->method('getShortName')
            ->will($this->returnValue('Blog'));

        $dataTransferObject = $this->getMockBuilder(PhpClass::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityReflection->expects($this->atLeastOnce())
            ->method('getShortName')
            ->will($this->returnValue('BlogDataTransferObject'));
        $entityReflection->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('SumoCoders\GeneratorBundle\BlogDataTransferObject');

        $updateCommand = $commandGenerator->generate(
            CommandGenerator::UPDATE_COMMAND,
            $bundle,
            $entityReflection,
            $dataTransferObject
        );

        $this->assertNotNull($updateCommand);
        $this->assertEquals('UpdateBlog', $updateCommand->getShortName());
    }

    public function testDeleteCommandGenerator()
    {
        $commandGenerator = new CommandGenerator();

        $bundle = $this->getMockBuilder(SumoCodersGeneratorBundle::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bundle->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->will($this->returnValue('SumoCoders\GeneratorBundle'));

        $entityReflection = $this->getMockBuilder(ReflectionClass::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityReflection->expects($this->atLeastOnce())
            ->method('getShortName')
            ->will($this->returnValue('Blog'));

        $dataTransferObject = $this->getMockBuilder(PhpClass::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityReflection->expects($this->atLeastOnce())
            ->method('getShortName')
            ->will($this->returnValue('BlogDataTransferObject'));
        $entityReflection->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('SumoCoders\GeneratorBundle\BlogDataTransferObject');

        $deleteCommand = $commandGenerator->generate(
            CommandGenerator::DELETE_COMMAND,
            $bundle,
            $entityReflection,
            $dataTransferObject
        );

        $this->assertNotNull($deleteCommand);
        $this->assertEquals('DeleteBlog', $deleteCommand->getShortName());
    }
}
