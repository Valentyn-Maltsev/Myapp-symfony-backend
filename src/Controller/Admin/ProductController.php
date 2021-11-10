<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/admin/product", name="admin_product_")
 * Class ProductController
 * @package App\Controller\Admin
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['isDeleted' => false], ['id' => 'DESC'], 50);

        return $this->render('admin/product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route ("/edit/{id}", name="edit")
     * @Route ("/add", name="add")
     * @param $id
     */
    public function edit($id)
    {

    }


    /**
     * @Route ("/delete/{id}", name="delete")
     * @param $id
     */
    public function delete($id)
    {

    }
}
