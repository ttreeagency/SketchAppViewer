<?php

namespace Ttree\SketchAppViewer\Exception;

use Ttree\SketchAppViewer\Domain\Model\ArtboardInterface;
use Ttree\SketchAppViewer\Domain\Model\DocumentInterface;

class ExportException extends \Exception
{
    public function __construct(DocumentInterface $doc, ArtboardInterface $artboard, $path, $format = null, $scale = null)
    {
        parent::__construct("Export failed");
    }
}
