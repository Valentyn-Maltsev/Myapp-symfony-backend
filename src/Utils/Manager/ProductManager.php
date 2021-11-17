<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractBaseManager
{
    /**
     * @var string
     */
    private $productImagesDir;

    /**
     * @var ProductImageManager
     */
    private $productImageManager;

    public function __construct(EntityManagerInterface $entityManager, ProductImageManager $productImageManager, string $productImagesDir)
    {
        parent::__construct($entityManager);

        $this->productImagesDir = $productImagesDir;
        $this->productImageManager = $productImageManager;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    /**
     * @param object $entity
     */
    public function remove(object $entity)
    {
        $entity->setIsDeleted(true);
        $this->save($entity);
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductImagesDir(Product $product)
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    /**
     * @param Product $product
     * @param string|null $tempImageFilename
     * @return Product
     */
    public function updateProductImages(Product $product, string $tempImageFilename = null): Product
    {
        if (!$tempImageFilename) {
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);

        $productImage = $this->productImageManager->saveProductImage($productDir, $tempImageFilename);
        $productImage->setProduct($product);

        $product->addProductImage($productImage);

        return $product;
    }
}