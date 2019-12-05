<?php

namespace App\Controller;

class SchemaAuthorController extends Controller {

    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:SchemaAuthor')->findAll();
        $schemaAuthors = $this->get('knp_paginator')->paginate($result, $page, 20);

        return $this->render('schema-author/index.html.twig', array(
                    'schemaAuthors' => $schemaAuthors
        ));
    }

}
