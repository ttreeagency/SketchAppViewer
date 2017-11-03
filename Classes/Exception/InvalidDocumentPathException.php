<?php

namespace Ttree\SketchAppViewer\Exception;

class InvalidDocumentPathException extends \InvalidArgumentException
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
        parent::__construct("Not a file: $path");
    }
}
