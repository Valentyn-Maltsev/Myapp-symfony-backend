<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FilesystemWorker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ProductImageManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $uploadsTempDir;
    /**
     * @var FilesystemWorker
     */
    private $filesystemWorker;

    /**
     * @var ImageResizer
     */
    private $imageResizer;

    public function __construct(EntityManagerInterface $entityManager, FilesystemWorker $filesystemWorker, ImageResizer $imageResizer,string $uploadsTempDir)
    {
        $this->entityManager = $entityManager;
        $this->filesystemWorker = $filesystemWorker;
        $this->uploadsTempDir = $uploadsTempDir;
        $this->imageResizer = $imageResizer;
    }

    /**
     * @param string $productDir
     * @param string|null $tempImageFilename
     * @return null
     */
    public function saveProductImage(string $productDir, string $tempImageFilename = null)
    {
        if (!$tempImageFilename) {
            return null;
        }

        $this->filesystemWorker->createFolderIfItNotExist($productDir);

        $filenameId = uniqid();

        $imageSmallParams = [
            'width' => 60,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.png', $filenameId, 'small')
        ];
        $imageSmall = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageSmallParams);
        $imageMiddleParams = [
            'width' => 430,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.png', $filenameId, 'middle')
        ];
        $imageMiddle = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageMiddleParams);

        $imageBigParams = [
            'width' => 800,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.png', $filenameId, 'big')
        ];
        $imageBig = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageBigParams);

        $productImage = new ProductImage();
        $productImage->setFilenameSmall($imageSmall);
        $productImage->setFilenameMiddle($imageMiddle);
        $productImage->setFilenameBig($imageBig);

        return $productImage;


    }
}