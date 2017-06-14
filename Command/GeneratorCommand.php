<?php

namespace SumoCoders\GeneratorBundle\Command;

use InvalidArgumentException;
use SumoCoders\GeneratorBundle\Generator\DataTransferObjectGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sumo:generate');
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

        $generator = new DataTransferObjectGenerator();
        $generator->generate($bundle, $entity);
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
