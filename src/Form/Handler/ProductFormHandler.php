<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Form\Model\EditProductModel;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Doctrine\ORM\productManageInterface;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    /**
     * @var FileSaver
     */
    private $fileSaver;

    /**
     * @var ProductManager
     */
    private $productManager;

    public function __construct(ProductManager $productManager, FileSaver $fileSaver)
    {
        $this->fileSaver = $fileSaver;
        $this->productManager = $productManager;
    }

    /**
     * @param EditProductModel $editProductModel
     * @param Form $form
     * @return Product|null
     */
    public function processEditForm(EditProductModel $editProductModel, Form $form): ?Product
    {
        $product = new Product();

        if ($editProductModel->id) {
            $product = $this->productManager->find($editProductModel->id);
        }

        $product->setTitle($editProductModel->title);
        $product->setPrice($editProductModel->price);
        $product->setQuantity($editProductModel->quantity);
        $product->setDescription($editProductModel->description);
        $product->setIsPublished($editProductModel->isPublished);
        $product->setIsDeleted($editProductModel->isDeleted);
        $product->setCategory($editProductModel->category);

        // ADD NEW IMAGE WITH DIFFERENT SIZES TO THE PRODUCT
        // 1. Save product's changes (+)
        $this->productManager->save($product);

        $newImageFile = $form->get('newImage')->getData();

        // 2. Save uploaded file into temp folder
        $tempImageFilename = $newImageFile
            ? $this->fileSaver->saveUploadedFileIntoTemp($newImageFile)
            : null;

        // 3. Work with product (addProductImage) and ProductImage
        // 3.1 Get path of folder with product images
        // 3.2 Work with ProductImage
        // 3.2.1 Resize and save image into folder (BIG, MIDDLE, SMALL)
        // 3.2.2 Create ProductImage and return it to Product
        $this->productManager->updateProductImages($product, $tempImageFilename);

        // 3.3 Save Product with new ProductImage
        $this->productManager->save($product);

        return $product;
    }
}