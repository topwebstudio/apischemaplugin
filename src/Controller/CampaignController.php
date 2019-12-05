<?php

namespace App\Controller;

use App\Entity\Campaign;
use Symfony\Component\HttpFoundation\Request;

class CampaignController extends Controller {

    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:Campaign')->findAll();
        $campaigns = $this->get('knp_paginator')->paginate($result, $page);

        return $this->render('campaign/index.html.twig', array(
                    'campaigns' => $campaigns,
        ));
    }

    public function newAction(Request $request) {
        $campaign = new Campaign();
        $form = $this->createForm('App\Form\CampaignType', $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($campaign);
            $em->flush();

            return $this->redirectToRoute('campaign_edit', array('id' => $campaign->getId()));
        }

        return $this->render('campaign/new.html.twig', array(
                    'campaign' => $campaign,
                    'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, Campaign $campaign) {
        $editForm = $this->createForm('App\Form\CampaignType', $campaign);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('campaign_edit', array('id' => $campaign->getId()));
        }

        return $this->render('campaign/edit.html.twig', array(
                    'campaign' => $campaign,
                    'form' => $editForm->createView(),
        ));
    }

    public function deleteAction($id) {
        $campaign = $this->getEntityManager()->getRepository('App:Campaign')->find($id);

        if ($campaign) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($campaign);
            $em->flush();
        }

        return $this->returnJsonResponse(array(
                    'success' => true,
                    'data' => $this->generateUrl('campaign_index')
        ));
    }

}
