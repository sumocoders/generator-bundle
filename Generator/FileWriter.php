<?php

namespace SumoCoders\GeneratorBundle\Generator;

use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpClass;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class FileWriter
{
    /**
     * @var DefaultGeneratorStrategy
     */
    protected $strategy;

    public function __construct()
    {
        $this->strategy = new DefaultGeneratorStrategy();
    }

    /**
     * @param BundleInterface $bundle
     * @param PhpClass $class
     * @param string $root
     *
     * @return mixed
     */
    public function saveFileContent(BundleInterface $bundle, PhpClass $class, $root)
    {
        $filename = $this->createFileName($bundle, $class->getShortName(), $root);
        $content = $this->strategy->generate($class);

        if (!is_dir(dirname($filename))) {
            @mkdir(dirname($filename), 0775, true);
        }

        return @file_put_contents($filename, sprintf("<?php\n\n%s", $content));
    }

    /**
     * @param $path
     * @param string $filename
     * @param string $content
     *
     * @return bool|int
     */
    public function saveYamlFileContent($path, $filename, $content)
    {
        $filePath = $path . DIRECTORY_SEPARATOR . $filename;

        if (!is_dir($path)) {
            mkdir(dirname($path), 0775, true);
        }

        return file_put_contents($filePath, $content);
    }

    /**
     * @param BundleInterface $bundle
     * @param string $class
     * @param string $root
     *
     * @return string
     */
    protected function createFileName(BundleInterface $bundle, $class, $root)
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
