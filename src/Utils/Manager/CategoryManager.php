<?php

namespace App\Utils\Manager;

use Doctrine\Persistence\ObjectRepository;
use Entity\Category;

class CategoryManager extends AbstractBaseManager
{
    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Category::class);
    }
}