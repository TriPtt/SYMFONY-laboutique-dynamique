<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit', name: 'product_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            
        ]);
    }

    #[Route('/{slug}', name: 'detail')]
    public function details(Product $product): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product
        ]);
    }
}
