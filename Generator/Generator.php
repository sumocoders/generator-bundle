<?php

namespace SumoCoders\GeneratorBundle\Generator;

class Generator
{
    /**
     * @param string $filename
     * @param string $content
     *
     * @return mixed
     */
    public function saveFileContent($filename, $content)
    {
        if (!is_dir(dirname($filename))) {
            @mkdir(dirname($filename), 0775, true);
        }

        return @file_put_contents($filename, sprintf("<?php\n\n%s", $content));
    }
}
