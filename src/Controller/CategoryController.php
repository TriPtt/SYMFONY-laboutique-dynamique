<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Category $category): Response
    {
        //Liste des produits d'une catégorie
        $product = $category->getProducts();

        return $this->render('category/list.html.twig', [
            'category' => $category,
            'products' => $product,
        ]);
    }
}
