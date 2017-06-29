<?php

namespace SumoCoders\GeneratorBundle\Exception;

use Exception;

final class FileAlreadyExists extends Exception
{
    public function __construct()
    {
        parent::__construct('File already exists');
    }
}
