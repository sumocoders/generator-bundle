<?php

namespace SumoCoders\GeneratorBundle\Generator;

use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpClass;
use SumoCoders\GeneratorBundle\Exception\FileAlreadyExists;
use SumoCoders\GeneratorBundle\Shared\Module;

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
     * @param Module $module
     * @param PhpClass $class
     * @param $root
     *
     * @throws FileAlreadyExists
     *
     * @return bool|int
     */
    public function savePhpFileContent(Module $module, PhpClass $class, $root)
    {
        $filename = $this->createFileName($module, $class->getShortName(), $root);
        $content = $this->strategy->generate($class);

        if (file_exists($filename)) {
            throw new FileAlreadyExists();
        }

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
     * @param Module $module
     * @param $class
     * @param $root
     *
     * @return string
     */
    protected function createFileName(Module $module, $class, $root)
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                $module->getPath(),
                $root,
                str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php',
            ]
        );
    }
}
