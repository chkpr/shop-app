<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\AddressType;
use App\Repository\OrderRepository;
use App\Service\CartService;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class OrderController extends AbstractController
{
    #[Route('/order/create', name: 'app_order_create')]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        #[CurrentUser] User $user,
        CartService $cartService,
        OrderService $orderService,
        EntityManagerInterface $entityManager,
    ): Response {
        $items = $cartService->getFullCart();

        // Panier vide → pas de commande possible
        if (empty($items)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            $order = $orderService->createOrderFromCart($this->getUser(), $address);

            $this->addFlash('success', 'Commande enregistrée !');

            return $this->redirectToRoute('app_order_confirmation', [
                'id' => $order->getId(),
            ]);
        }

        return $this->render('order/create.html.twig', [
            'items' => $items,
            'total' => $cartService->getTotal(),
            'form' => $form,
        ]);
    }

    #[Route('/order/{id}/confirmation', name: 'app_order_confirmation')]
    #[IsGranted('ROLE_USER')]
    public function confirmation(int $id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
        ]);
    }
}
