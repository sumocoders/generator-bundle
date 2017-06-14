<?php

namespace SumoCoders\GeneratorBundle\Command;

use InvalidArgumentException;
use ReflectionClass;
use SumoCoders\GeneratorBundle\Generator\CommandGenerator;
use SumoCoders\GeneratorBundle\Generator\DataTransferObjectGenerator;
use SumoCoders\GeneratorBundle\Generator\FileWriter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratorCommand extends ContainerAwareCommand
{
    /**
     * @var FileWriter
     */
    private $fileWriter;

    /**
     * @param FileWriter $writer
     */
    public function __construct(FileWriter $writer)
    {
        parent::__construct();
        $this->fileWriter = $writer;
    }

    protected function configure()
    {
        $this->setName('sumocoders:generate');
        $this->addOption(
            'entity',
            '-en',
            InputOption::VALUE_REQUIRED,
            'The entity (shortcut notation) we\'ll using to generate DTOs, commands and command handlers'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundleName, $entity) = $this->parseShortcutNotation($input->getOption('entity'));

        $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);

        $entityReflection = new ReflectionClass($bundle->getNamespace() . '\\' . $entity);

        $dataTransferObjectGenerator = new DataTransferObjectGenerator();
        $dataTransferObject = $dataTransferObjectGenerator->generate($bundle, $entityReflection);

        $this->fileWriter->saveFileContent($bundle, $dataTransferObject, 'DataTransferObject');

        $commandGenerator = new CommandGenerator();

        $createCommand = $commandGenerator->generate(
            CommandGenerator::CREATE_COMMAND, $bundle, $entityReflection, $dataTransferObject
        );

        $this->fileWriter->saveFileContent($bundle, $createCommand, 'Command');

        $updateCommand = $commandGenerator->generate(
            CommandGenerator::UPDATE_COMMAND, $bundle, $entityReflection, $dataTransferObject
        );

        $this->fileWriter->saveFileContent($bundle, $updateCommand, 'Command');

        $deleteCommand = $commandGenerator->generate(
            CommandGenerator::DELETE_COMMAND, $bundle, $entityReflection, $dataTransferObject
        );

        $this->fileWriter->saveFileContent($bundle, $deleteCommand, 'Command');
    }

    /**
     * @param string $shortcut
     *
     * @return array
     */
    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);
        if (false === $pos = strpos($entity, ':')) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Entity name must contain a : ' .
                    '("%s" given, expecting something like AcmeBlogBundle:Blog/Post)',
                    $entity
                )
            );
        }

        return [substr($entity, 0, $pos), substr($entity, $pos + 1)];
    }
}
