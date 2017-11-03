<?php

namespace Ttree\SketchAppViewer\Domain\Model;

interface DocumentInterface
{
    public function getArtboards($includeSymbols = true);

    public function getMetadata();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return \DateTime
     */
    public function getTime();
}
