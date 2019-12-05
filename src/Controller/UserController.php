<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    public function countryAction() {
        $countries = $this->getEntityManager()->getRepository('App:Country')->findAll();

        $country = null;
        if ($this->getUser()->getCountry()) {
            $country = $this->getEntityManager()->getRepository('App:Country')->find($this->getUser()->getCountry());
        }

        return $this->render('user/user-country.html.twig', array(
                    'countries' => $countries,
                    'country' => $country
        ));
    }

    public function editAction(Request $request) {
        $entity = $this->getUser();

        $editForm = $this->createForm('App\Form\UserType', $entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('user/edit.html.twig', array(
                    'entity' => $entity,
                    'form' => $editForm->createView(),
        ));
    }

    public function updateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getUser();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm('App\Form\UserType', $entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_edit'));
        }

        return $this->render('user/edit.html.twig', array(
                    'entity' => $entity,
                    'form' => $editForm->createView()
        ));
    }

    public function saveLocationAction($id) {
        $entity = $this->getUser();
        $entity->setCountry($id);

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return $this->returnJsonResponse(array('success' => true));
    }

}
