<?php

namespace Ttree\SketchAppViewer\Domain\Model;

use Symfony\Component\Process\Process;
use Ttree\SketchAppViewer\Exception\ExportException;
use Ttree\SketchAppViewer\Exception\InvalidDocumentPathException;
use Ttree\SketchAppViewer\Image;
use Ttree\SketchAppViewer\ImageInterface;

class Document implements DocumentInterface
{
    protected $path;

    public function __construct($path)
    {
        if (!is_file($path)) {
            throw new InvalidDocumentPathException($path);
        }
        $this->path = realpath($path);
    }

    /**
     * @param \Ttree\SketchAppViewer\Domain\Model\ArtboardInterface $artboard
     * @param string $path
     * @param string|null $format
     * @param string|null $scale
     * @return ImageInterface[]
     * @throws \Exception
     */
    public function export(ArtboardInterface $artboard, $path, $format = null, $scale = null)
    {
        $cmd = sprintf('sketchtool export artboards %s', escapeshellarg($this->path));
        $cmd .= sprintf(' --item=%s', escapeshellarg($artboard->getId()));
        $cmd .= sprintf(' --output=%s', escapeshellarg($path));
        $cmd .= ' --use-id-for-name=YES';
        $cmd .= ' --save-for-web=YES';
        if (isset($format)) {
            $cmd .= sprintf(' --formats=%s', escapeshellarg($format));
        }
        if (isset($scale)) {
            $cmd .= sprintf(' --scales=%s', escapeshellarg($scale));
        }
        $proc = new Process($cmd);
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new ExportException($this, $artboard, $path, $format, $scale);
        }
        $images = [];
        $lines = explode("\n", $proc->getOutput());
        foreach ($lines as $line) {
            if (preg_match('/^Exported (.+)$/', $line, $matches)) {
                $images[] = Image::fromPath(basename($matches[1]), $path);
            }
        }
        return $images;
    }

    /**
     * @param bool $includeSymbols
     * @return ArtboardInterface[]
     * @throws \Exception
     */
    public function getArtboards($includeSymbols = true)
    {
        $artboards = [];
        $cmd = sprintf("sketchtool list artboards %s", escapeshellarg($this->path));
        if ($includeSymbols) {
            $cmd .= " --include-symbols=YES";
        }
        $proc = new Process($cmd);
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \Exception("Could not retrieve artboards");
        }
        $data = json_decode($proc->getOutput(), true);
        foreach ($data['pages'] as $page) {
            foreach ($page['artboards'] as $artboard) {
                $artboards[$artboard['id']] = Artboard::fromDocumentJson($artboard, $page);
            }
        }
        return $artboards;
    }

    public function getMetadata()
    {
        $cmd = sprintf("sketchtool metadata %s", escapeshellarg($this->path));
        $proc = new Process($cmd);
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \Exception("Could not retrieve metadata");
        }
        return json_decode($proc->getOutput(), true);
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        $time = new \DateTime();
        $time->setTimestamp(filemtime($this->getPath()));
        return $time;
    }
}
