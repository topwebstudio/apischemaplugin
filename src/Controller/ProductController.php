<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;

 
class ProductController extends Controller {

    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:Product')->findAll();
        $products = $this->get('knp_paginator')->paginate($result, $page);

        return $this->render('product/index.html.twig', array(
                    'products' => $products,
        ));
    }

    public function newAction(Request $request) {
        $product = new Product();
        $form = $this->createForm('App\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/new.html.twig', array(
                    'product' => $product,
                    'form' => $form->createView(),
                   
        ));
    }

    public function editAction(Request $request, Product $product) {
        $editForm = $this->createForm('App\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
       
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
                    'product' => $product,
                    'form' => $editForm->createView(),
                  
        ));
    }

    public function deleteAction($id) {
        $product = $this->getEntityManager()->getRepository('App:Product')->find($id);

        if ($product) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->returnJsonResponse(array(
                    'success' => true,
                    'data' => $this->generateUrl('product_index')
        ));
    }

   

}
