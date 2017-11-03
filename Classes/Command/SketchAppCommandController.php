<?php

namespace Ttree\SketchAppViewer\Command;

use Neos\Flow\Cli\CommandController;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\Utility\Now;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\AssetCollectionRepository;
use Neos\Media\Domain\Repository\ImageRepository;
use Neos\Media\Domain\Service\AssetService;
use Neos\Utility\ObjectAccess;
use Ttree\SketchAppViewer\Domain\Model\Artboard;
use Ttree\SketchAppViewer\Domain\Model\Document;
use Neos\Flow\Annotations as Flow;

class SketchAppCommandController extends CommandController
{
    /**
     * @var AssetCollectionRepository
     * @Flow\Inject
     */
    protected $assetCollectionRepository;

    /**
     * @var ResourceManager
     * @Flow\Inject
     */
    protected $resourceManager;

    /**
     * @var ImageRepository
     * @Flow\Inject
     */
    protected $imageRespository;

    /**
     * @var AssetService
     * @Flow\Inject
     */
    protected $assetService;

    public function importArtboardsCommand(string $file, string $name)
    {
        $this->outputLine();
        $this->outputLine('Process "<b>%s</b>" ...', [$file]);
        $this->outputLine();

        $exportDirectory = \FLOW_PATH_DATA . 'Persistent/SketchApp/Exports/' . $name . '/';

        $collection = $this->createAssetCollection($name);

        $document = new Document($file);

        $artboards = $document->getArtboards(false);

        \usort($artboards, function (Artboard $a, Artboard $b) {
            return \strnatcmp($a->getName(), $b->getName());
        });

        \array_map(function (Artboard $artboard) use ($document, $collection, $exportDirectory) {
            $this->outputLine();
            $this->outputLine('   Page: <comment>%s</comment> Artboard: <info>%s</info> (%s)', [$artboard->getPage(), $artboard->getName(), $artboard->getId()]);

            $document->export($artboard, $exportDirectory, 'png', '2x');

            $resource = $this->resourceManager->importResource($exportDirectory . $artboard->getId() . '@2x.png');
            $identifier = \strtolower($artboard->getId());
            /** @var Image $image */
            $image = $this->imageRespository->findByIdentifier($identifier);
            if ($image === null) {
                $image = new Image($resource);
                ObjectAccess::setProperty($image, 'Persistence_Object_Identifier', $identifier, true);
                $this->imageRespository->add($image);
            } else {
                $this->assetService->replaceAssetResource($image, $resource, [
                    'keepOriginalFilename' => true
                ]);
                $this->imageRespository->update($image);
            }
            $image->setTitle($artboard->getName());
            $image->setCaption(\vsprintf('Imported from "%s", last import %s', [basename($document->getPath()), (new Now())->format(\DATE_ATOM)]));

            $collection->addAsset($image);
            $this->assetCollectionRepository->update($collection);
        }, $artboards);

        $this->outputLine();
        $this->outputLine('Number of artboards: %d', [count($artboards)]);
        $this->outputLine();
    }

    protected function createAssetCollection(string $name)
    {
        $name = trim($name);
        $collection = $this->assetCollectionRepository->findOneByTitle($name);
        if ($collection === null) {
            $collection = new AssetCollection($name);
            $this->assetCollectionRepository->add($collection);
        }
        return $collection;
    }
}

