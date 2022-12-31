<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Category $category, ProductRepository $productRepository, Request $request): Response
    {
        // numero de page dans l'url :
        $page = $request->query->getInt('page', 1);

        //Liste des produits d'une catégorie
        $product = $productRepository->findProductPaginated($page, $category->getSlug(), 9);

        return $this->render('category/list.html.twig', [
            'category' => $category,
            'products' => $product,
        ]);
    }
}
