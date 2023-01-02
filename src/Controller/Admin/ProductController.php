<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produit', name: 'admin_product_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $products = $productRepository->findBy([], ['created_at' => 'DESC']);

        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/ajouter', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Product();

        $productForm = $this->createForm(ProductFormType::class, $product);

        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $prix = $product->getPrice();
            $product->setPrice($prix * 100);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté');

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->renderForm('admin/product/add.html.twig', [
            'productForm' => $productForm->createView(),
        ]);
    }

    #[Route('/modifier/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product->setPrice($product->getPrice() / 100);

        $productForm = $this->createForm(ProductFormType::class, $product);

        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $prix = $product->getPrice();
            $product->setPrice($prix * 100);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->renderForm('admin/product/edit.html.twig', [
            'productForm' => $productForm->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé');

        return $this->redirectToRoute('admin_product_index');
    }
}
