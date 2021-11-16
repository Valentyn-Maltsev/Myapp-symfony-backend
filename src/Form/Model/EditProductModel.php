<?php

namespace App\Form\Model;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditProductModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $price;

    /**
     * @var UploadedFile/null
     */
    public $newImage;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $isPublished;

    /**
     * @var bool
     */
    public $isDeleted;


    public static function makeFromProduct(?Product $product): self
    {
        $model = new self();

        if (!$product) {
            return $model;
        }

        $model->id = $product->getId();
        $model->title = $product->getTitle();
        $model->price = $product->getPrice();
        $model->quantity = $product->getQuantity();
        $model->description = $product->getDescription();
        $model->isPublished = $product->getIsPublished();
        $model->isDeleted = $product->getIsDeleted();

        return $model;
    }
}