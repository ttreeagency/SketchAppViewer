<?php

namespace Ttree\SketchAppViewer\Command;

use Neos\Flow\Cli\CommandController;
use Ttree\SketchAppViewer\Domain\Model\Artboard;
use Ttree\SketchAppViewer\Domain\Model\Document;

class SketchAppCommandController extends CommandController
{
    public function listCommand(string $file)
    {
        $this->outputLine();
        $this->outputLine('Process "<b>%s</b>" ...', [$file]);
        $this->outputLine();

        $document = new Document($file);

        $artboards = $document->getArtboards(false);

        \usort($artboards, function (Artboard $a, Artboard $b) {
            return \strnatcmp($a->getName(), $b->getName());
        });

        \array_map(function (Artboard $artboard) {
            $this->outputLine();
            $this->outputLine('   Page: <comment>%s</comment> Artboard: <info>%s</info> (%s)', [$artboard->getPage(), $artboard->getName(), $artboard->getId()]);
        }, $artboards);

        $this->outputLine();
        $this->outputLine('Number of artboards: %d', [count($artboards)]);
        $this->outputLine();
    }
}

