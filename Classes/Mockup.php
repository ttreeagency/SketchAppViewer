<?php

namespace Ttree\SketchAppViewer;

use Ttree\SketchAppViewer\Domain\Model\Artboard;
use Ttree\SketchAppViewer\Domain\Model\ArtboardInterface;

class Mockup implements MockupInterface, \Countable, \IteratorAggregate, \JsonSerializable
{
    /**
     * @var ArtboardInterface
     */
    protected $artboard;

    /**
     * @var ImageInterface[]
     */
    protected $images = [];

    public static function fromArtboard(ArtboardInterface $artboard)
    {
        $mockup = new static();
        $mockup->artboard = $artboard;
        return $mockup;
    }

    public static function fromJson(array $json, $directory)
    {
        $mockup = new static();
        if (!empty($json['artboard'])) {
            $mockup->artboard = Artboard::fromJson($json['artboard']);
        }
        if (!empty($json['images'])) {
            foreach ($json['images'] as $image) {
                $mockup->images[] = Image::fromJson($image, $directory);
            }
        }
        return $mockup;
    }

    public function addImage($image)
    {
        $this->images[] = $image;
    }

    public function count()
    {
        return count($this->getImages());
    }

    public function getArtboard()
    {
        return $this->artboard;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getImages());
    }

    public function jsonSerialize()
    {
        return [
            'artboard' => $this->artboard,
            'images' => $this->images,
        ];
    }
}
