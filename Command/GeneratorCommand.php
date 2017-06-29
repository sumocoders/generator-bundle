<?php

namespace SumoCoders\GeneratorBundle\Command;

use Exception;
use InvalidArgumentException;
use ReflectionClass;
use SumoCoders\GeneratorBundle\Generator\CommandGenerator;
use SumoCoders\GeneratorBundle\Generator\ConfigGenerator;
use SumoCoders\GeneratorBundle\Generator\DataTransferObjectGenerator;
use SumoCoders\GeneratorBundle\Generator\FileWriter;
use SumoCoders\GeneratorBundle\Generator\HandlerGenerator;
use SumoCoders\GeneratorBundle\Shared\Module;
use SumoCoders\GeneratorBundle\ValueObject\Application;
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
            'entityFQN',
            '-fqn',
            InputOption::VALUE_REQUIRED,
            'The entity\'s FQN we\'ll be using to generate DTOs, commands and command handlers'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        static::validateExecutionPath();

        $entityFQN = $input->getOption('entityFQN');

        $application = static::inferWhichApplicationIsRunnig($entityFQN);

        $module = Module::fromApplicationAndEntity($application, $entityFQN);

        $entityReflection = new ReflectionClass($entityFQN);

        $dataTransferObjectGenerator = new DataTransferObjectGenerator();
        $dataTransferObject = $dataTransferObjectGenerator->generate($module, $entityReflection);

        $this->fileWriter->savePhpFileContent($module, $dataTransferObject, 'DataTransferObject');

        $commandGenerator = new CommandGenerator();

        $createCommand = $commandGenerator->generate(
            CommandGenerator::CREATE_COMMAND,
            $module,
            $entityReflection,
            $dataTransferObject
        );

        $this->fileWriter->savePhpFileContent($module, $createCommand, 'Command');

        $updateCommand = $commandGenerator->generate(
            CommandGenerator::UPDATE_COMMAND,
            $module,
            $entityReflection,
            $dataTransferObject
        );

        $this->fileWriter->savePhpFileContent($module, $updateCommand, 'Command');

        $deleteCommand = $commandGenerator->generate(
            CommandGenerator::DELETE_COMMAND,
            $module,
            $entityReflection,
            $dataTransferObject
        );

        $this->fileWriter->savePhpFileContent($module, $deleteCommand, 'Command');

        $handlerGenerator = new HandlerGenerator();

        $createCommandHandler = $handlerGenerator->generate(
            HandlerGenerator::CREATE_COMMAND,
            $module,
            $entityReflection,
            $createCommand
        );

        $this->fileWriter->savePhpFileContent($module, $createCommandHandler, 'Command');

        $updateCommandHandler = $handlerGenerator->generate(
            HandlerGenerator::UPDATE_COMMAND,
            $module,
            $entityReflection,
            $updateCommand
        );

        $this->fileWriter->savePhpFileContent($module, $updateCommandHandler, 'Command');

        $deleteCommandHandler = $handlerGenerator->generate(
            HandlerGenerator::DELETE_COMMAND,
            $module,
            $entityReflection,
            $deleteCommand
        );

        $this->fileWriter->savePhpFileContent($module, $deleteCommandHandler, 'Command');

        $configGenerator = new ConfigGenerator();

        $yaml = $configGenerator->generate(
            $module->getName(),
            $module->getPath(),
            [$createCommandHandler, $updateCommandHandler, $deleteCommandHandler]
        );

        $this->fileWriter->saveYamlFileContent(
            $module->getPath() . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config',
            'command.yml',
            $yaml
        );

        $output->writeln(
            '<info>A new file services file was generated (' . $module->getName() .
            "/Resources/config/command.yml).\n This file should be imported in the bundle's services.yml file.</info>"
        );
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

    /**
     * @throws Exception
     */
    private static function validateExecutionPath()
    {
        $cwd = getcwd();
        if (!is_dir($cwd . DIRECTORY_SEPARATOR . 'app') && !is_dir($cwd . DIRECTORY_SEPARATOR . 'src')) {
            throw new Exception(
                <<<EOF
I cannot recognize this application or your are not running this command from the root of your application.
EOF
            );
        }
    }

    /**
     * @param string $fqn
     *
     * @return Application
     */
    private static function inferWhichApplicationIsRunnig($fqn)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $fqn);

        if (preg_match('/(Frontend)|(Backend)\/Modules/', $path)) {
            return Application::fromString(Application::FORK);
        }

        return Application::fromString(Application::FRAMEWORK);
    }
}
