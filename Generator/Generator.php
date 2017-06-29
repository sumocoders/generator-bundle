<?php

namespace SumoCoders\GeneratorBundle\Generator;

use SumoCoders\GeneratorBundle\Shared\Module;

class Generator
{
    /**
     * @param Module $module
     * @param $className
     * @param $root
     *
     * @return string
     */
    protected function createClassName(Module $module, $className, $root)
    {
        return $module->getNamespace() . '\\' . $root . '\\' . $className;
    }
}
