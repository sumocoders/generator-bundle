<?php

namespace SumoCoders\GeneratorBundle\Tests\Generator;

use PHPUnit_Framework_TestCase;
use SumoCoders\GeneratorBundle\Generator\DataTransferObjectGenerator;
use SumoCoders\GeneratorBundle\SumoCodersGeneratorBundle;

class DataTransferObjectGeneratorTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'EntityClass.php';

    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $filePath;

    protected function setUp()
    {
        $this->directory = getcwd() . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR;

        @mkdir($this->directory);

        $this->filePath = $this->directory . self::CLASS_NAME;

        file_put_contents($this->filePath, $this->getClassContent());
    }

    protected function tearDown()
    {
        unlink($this->filePath);
        rmdir($this->directory);
    }

    public function testDataTransferObjectGenerator()
    {
        $generator = new DataTransferObjectGenerator();

        $bundle = $this->getMockBuilder(SumoCodersGeneratorBundle::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bundle->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->will($this->returnValue('SumoCoders\GeneratorBundle'));

        $generator->generate($bundle, 'Entity\EntityClass');
    }

    /**
     * @return string
     */
    private function getClassContent()
    {
        return <<<EOF
<?php

namespace SumoCoders\GeneratorBundle\Entity;

use Doctrine\Common\Collections\Collection;

class EntityClass {
    /**
     * @var int
     */
    private \$id;
    
    /**
     * @var string
     */
    private \$name;
    
    /**
     * @var Collection
     */
    private \$collection;
}
EOF;
    }
}
