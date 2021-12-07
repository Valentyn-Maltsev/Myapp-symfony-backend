<?php

namespace App\Controller\Main;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/api", name="main_api_")
     */
class CartApiController extends AbstractController
{

    /**
     * @Route("/cart", methods="POST", name="cart_save")
     */
    public function saveCart(
        Request $request,
        CartRepository $cartRepository,
        ProductRepository $productRepository,
        CartProductRepository $cartProductRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $phpSessionId = $request->cookies->get('PHPSESSID');
        $productId = $request->request->get('productId');

        $product = $productRepository->findOneBy(['uuid' => $productId]);
        $cart = $cartRepository->findOneBy(['sessionId' => $phpSessionId]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($phpSessionId);
        }

        $cartProduct = $cartProductRepository->findOneBy(['cart' => $cart, 'product' => $product]);
        if (!$cartProduct) {
            $cartProduct = new CartProduct();
            $cartProduct->setCart($cart);
            $cartProduct->setQuantity(1);
            $cartProduct->setProduct($product);

            $cart->addCartProduct($cartProduct);
        } else {
            $cartProduct->setQuantity($cartProduct->getQuantity() + 1);
        }

        $entityManager->persist($cart);
        $entityManager->persist($cartProduct);
        $entityManager->flush();

        return new JsonResponse([
            'success' => false,
            'data' => [
                'test' => 123
            ]
        ]);
    }
}
