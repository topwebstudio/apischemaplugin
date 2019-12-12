<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\SchemaAuthor;

class SchemaAuthorController extends Controller {

    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:SchemaAuthor')->findAll();
        $schemaAuthors = $this->get('knp_paginator')->paginate($result, $page, 20);

        return $this->render('schema-author/index.html.twig', array(
                    'schemaAuthors' => $schemaAuthors
        ));
    }

    public function editAction(Request $request, SchemaAuthor $schemaAuthor) {
        $editForm = $this->createForm('App\Form\SchemaAuthorType', $schemaAuthor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('schema_author_edit', array('id' => $schemaAuthor->getId()));
        }

        return $this->render('schema-author/edit.html.twig', array(
                    'schemaAuthor' => $schemaAuthor,
                    'form' => $editForm->createView(),
        ));
    }

}
