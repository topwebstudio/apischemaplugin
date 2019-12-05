<?php

namespace App\Controller;

use App\Entity\SchemaBuilder;
use App\Entity\JsonSchema;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\FormError;

class SchemaBuilderController extends Controller {

    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:SchemaBuilder')->findAll();
        $schemaBuilders = $offers = $this->get('knp_paginator')->paginate($result, $page, 20);

        return $this->render('schemabuilder/index.html.twig', array(
                    'schemaBuilders' => $schemaBuilders,
        ));
    }

    public function newAction(Request $request) {

        $schemaBuilder = new Schemabuilder();
        $form = $this->createForm('App\Form\SchemaBuilderType', $schemaBuilder);
        $form->handleRequest($request);

        $authorEmail = $form->get("authorEmail")->getData();

        if (!$authorEmail) {
            $form->get('authorEmail')->addError(new FormError('Please enter author email'));
        } else {
            $author = $this->getEntityManager()->getRepository('App:SchemaAuthor')->findOneByEmail($authorEmail);

            if (!$author) {
                $form->get('authorEmail')->addError(new FormError('Author with this email has not been found in the system'));
            }
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $schemaBuilder->setAuthor($author);
            
            $em->persist($author);
            $em->persist($schemaBuilder);
            $em->flush();

            return $this->redirectToRoute('schemabuilder_edit', array('id' => $schemaBuilder->getId()));
        }

        return $this->render('schemabuilder/new.html.twig', array(
                    'schemaBuilder' => $schemaBuilder,
                    'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, SchemaBuilder $schemaBuilder) {
        $deleteForm = $this->createDeleteForm($schemaBuilder);
        $editForm = $this->createForm('App\Form\SchemaBuilderType', $schemaBuilder);
        $editForm->remove('authorEmail');
        
        
        $editForm->handleRequest($request);
        
        

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('schemabuilder_edit', array('id' => $schemaBuilder->getId()));
        }

        return $this->render('schemabuilder/edit.html.twig', array(
                    'schemaBuilder' => $schemaBuilder,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    public function deleteAction($id) {
        $schema = $this->getEntityManager()->getRepository('App:SchemaBuilder')->find($id);

        if ($schema) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($schema);
            $em->flush();
        }

        return $this->returnJsonResponse(array(
                    'success' => true,
                    'data' => $this->generateUrl('schemabuilder_index')
        ));
    }

    public function deleteJsonAction(Request $request) {
        $id = $request->get('id');

        if ($id) {
            $em = $this->getDoctrine()->getManager();
            $jsonSchema = $em->getRepository('App:JsonSchema')->find($id);

            if ($jsonSchema) {
                $em->remove($jsonSchema);
                $em->flush();

                return $this->returnJsonResponse(array(
                            'success' => true
                ));
            }
        }

        return $this->returnJsonResponse(array(
                    'success' => false,
        ));
    }

    public function updateJsonAction(Request $request) {
        $id = $request->get('id');
        $schemaBuilderId = $request->get('schema-builder-id');
        $em = $this->getDoctrine()->getManager();
        if (!$id) {
            $jsonSchema = new JsonSchema();

            if ($schemaBuilderId) {
                $schemaBuilder = $em->getRepository('App:SchemaBuilder')->find($schemaBuilderId);
                if ($schemaBuilder) {
                    $schemaBuilder->addJsonSchema($jsonSchema);
                    $em->persist($schemaBuilder);
                }
            }
        } else {
            $jsonSchema = $this->getEntityManager()->getRepository('App:JsonSchema')->find($id);
        }


        if ($jsonSchema) {

            $jsonSchema->setSchemaArray(json_decode($request->get('json')));

            $em->flush();

            return $this->returnJsonResponse(array(
                        'success' => true,
                        'id' => $jsonSchema->getId()
            ));
        }

        return $this->returnJsonResponse(array(
                    'success' => false,
        ));
    }

    private function createDeleteForm(SchemaBuilder $schemaBuilder) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('schemabuilder_delete', array('id' => $schemaBuilder->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
