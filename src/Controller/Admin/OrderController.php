<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Form\Admin\EditOrderFormType;
use App\Form\Handler\OrderFormHandler;
use App\Repository\OrderRepository;
use App\Utils\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/admin/order", name="admin_order_")
 * Class CategoryController
 * @package App\Controller\Admin
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function list(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['isDeleted' => false], ['id' => 'DESC']);

        return $this->render('admin/order/list.html.twig', [
            'orders' => $orders,
            'orderStatusChoices' => OrderStaticStorage::getOrderStatusChoices()
        ]);
    }

    /**
     * @Route ("/edit/{id}", name="edit")
     * @Route ("/add", name="add")
     * @param Request $request
     * @param OrderFormHandler $orderFormHandler
     * @param Order|null $order
     * @return Response
     */
    public function edit(Request $request, OrderFormHandler $orderFormHandler, Order $order = null) // Symfony find product by parameter id
    {
        $order = $order ? : new Order();

        $form = $this->createForm(EditOrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $order = $orderFormHandler->processEditForm($order);

                $this->addFlash('success', 'Your changes were saved');

                return $this->redirectToRoute('admin_order_edit', ['id' => $order->getId()]);
            } else {
                $this->addFlash('warning', 'Something went wrong. Please check your form!');
            }
        }

        return $this->render('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route ("/delete/{id}", name="delete")
     * @param $id
     */
    public function delete(Order $order, OrderManager $orderManager)
    {
        $orderManager->remove($order);

        $this->addFlash('warning', 'The order was successfully deleted');

        return $this->redirectToRoute('admin_order _list');
    }
}
