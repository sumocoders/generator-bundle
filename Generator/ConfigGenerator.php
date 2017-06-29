<?php

namespace SumoCoders\GeneratorBundle\Generator;

use CG\Generator\PhpClass;
use Exception;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class ConfigGenerator
{
    /**
     * @param $moduleName
     * @param string $path
     * @param array $commands
     *
     * @throws Exception
     *
     * @return string
     */
    public function generate($moduleName, $path, array $commands)
    {
        // Check for existing command.yml file
        $filename = $path . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'command.yml';
        $content = [];
        if (file_exists($filename)) {
            try {
                $content = Yaml::parse(file_get_contents($filename));
            } catch (ParseException $e) {
                throw new Exception($e->getMessage());
            }
        }

        if (!isset($content['services'])) {
            $content['services'] = [];
        }

        /** @var PhpClass $command */
        foreach ($commands as $command) {
            $serviceName = $this->toSnakeCase(str_replace('Bundle', '', $moduleName))
                . '.command.' . $this->toSnakeCase($command->getShortName());

            $content['services'][$serviceName] = [
                'class' => $command->getName(),
                'arguments' => [
                    '@doctrine.orm.default_entity_manager',
                ],
                'tags' => [
                    [
                        'name' => 'command_handler',
                        'handles' => str_replace('Handler', '', $command->getName()),
                    ],
                ],
            ];
        }

        $yml = Yaml::dump($content, 4, 2);

        return $yml;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function toSnakeCase($value)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }
}
