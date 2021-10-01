<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/home", methods="GET", name="homepage")
     */
    public function index(ProductRepository $productRepository): Response
    {
        $productList = $productRepository->findAll();
        dump($productList); die();

        return $this->render('main/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    /**
     * @Route("/product-add", methods="GET", name="product_add")
     */
    public function productAdd(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $product->setTitle('Product' . rand(1, 100));
        $product->setDescription('smth');
        $product->setPrice(10);
        $product->setQuantity(1);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }
}
