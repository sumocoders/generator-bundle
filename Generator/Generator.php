<?php

namespace SumoCoders\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class Generator
{
    /**
     * @param BundleInterface $bundle
     * @param string $className
     * @param string $root
     *
     * @return string
     */
    protected function createClassName(BundleInterface $bundle, $className, $root)
    {
        return $bundle->getNamespace() . '\\' . $root . '\\' . $className;
    }
}
