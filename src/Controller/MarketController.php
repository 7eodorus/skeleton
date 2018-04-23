<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MarketController extends Controller
{
    const basket_SESSION_KEY = 'basket';

    /**
     * @Route("/", name="market")
     */
    public function index(SessionInterface $session)
    {
        $products = $this->getProducts();
        $basket = $this->getBasket($session);
        $count = 0;

        if (!empty($basket)) {
            foreach ($basket as $item) {
                $count += $item;
            }
        }

        return $this->render('market/index.html.twig', [
            'products' => $products,
            'basket_count' => $count,
        ]);
    }

    /**
     * @Route("/basket/add/{product_id}", name="add_to_basket")
     */
    public function addToBasket(SessionInterface $session, $product_id)
    {
        $products = $this->getProducts();

        if (!isset($products[$product_id])) {
            $this->addFlash(
                'error',
                'Товар не найден'
            );

            return $this->redirectToRoute('market');
        }

        $basket = $this->getBasket($session);
        $basket[$product_id] = !empty($basket[$product_id]) ? $basket[$product_id] : 0;
        $basket[$product_id] += 1;
        $this->setBasket($session, $basket);

        $this->addFlash(
            'notice',
            'Вы добавили продукт в корзину'
        );

        return $this->redirectToRoute('market');
    }

    /**
     * @Route("/basket", name="basket")
     */
    public function basket(SessionInterface $session)
    {
        $basket = $this->getBasket($session);
        $products = $this->getProducts();

        return $this->render('market/basket.html.twig', [
            'basket' => $basket,
            'products' => $products,
        ]);
    }

    private function getProducts()
    {
        return $this->getParameter('products');
    }

    private function getBasket(SessionInterface $session)
    {
        return $session->get(self::basket_SESSION_KEY, []);
    }

    private function setBasket(SessionInterface $session, $basket)
    {
        $session->set(self::basket_SESSION_KEY, $basket);
    }
}
