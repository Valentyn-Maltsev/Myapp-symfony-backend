<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\EditProductFormType;
use App\Form\Handler\ProductFormHandler;
use App\Form\Model\EditProductModel;
use App\Repository\ProductRepository;
use App\Utils\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function edit(Request $request, ProductFormHandler $productFormHandler, Product $product = null) // Symfony find product by parameter id
    {
        $product = $product ? : new Product();

        $editProductModel = EditProductModel::makeFromProduct($product);

        $form = $this->createForm(EditProductFormType::class, $editProductModel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $product = $productFormHandler->processEditForm($editProductModel, $form);

                $this->addFlash('success', 'Your changes were saved');

                return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
            } else {
                $this->addFlash('warning', 'Something went wrong. Please check your form!');
            }
        }

        $images = $product->getProductImages()
            ? $product->getProductImages()->getValues()
            : [];

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'images' => $images
        ]);
    }



    /**
     * @Route ("/delete/{id}", name="delete")
     * @param $id
     */
    public function delete(Product $product, ProductManager $productManager)
    {
        $productManager->remove($product);

        $this->addFlash('warning', 'The product was successfully deleted');

        return $this->redirectToRoute('admin_product_list');
    }
}
