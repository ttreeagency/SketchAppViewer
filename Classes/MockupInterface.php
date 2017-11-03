<?php

namespace Ttree\SketchAppViewer;

use Ttree\SketchAppViewer\Domain\Model\ArtboardInterface;

interface MockupInterface
{
    /**
     * @return ArtboardInterface
     */
    public function getArtboard();

    /**
     * @return ImageInterface[]
     */
    public function getImages();
}
