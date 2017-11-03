<?php

namespace Ttree\SketchAppViewer;

interface PatternInterface
{
    public function addMockup(MockupInterface $mockup);

    public function getId();

    public function getMockups();
}
