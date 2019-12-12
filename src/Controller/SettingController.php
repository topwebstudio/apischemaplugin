<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Setting;

class SettingController extends Controller {

    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:Setting')->findAll();
        $settings = $this->get('knp_paginator')->paginate($result, $page);

        return $this->render('setting/index.html.twig', array(
                    'settings' => $settings,
        ));
    }

    public function editAction(Request $request, Setting $setting) {
        $editForm = $this->createForm('App\Form\SettingType', $setting);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('setting_edit', array('id' => $setting->getId()));
        }

        return $this->render('setting/edit.html.twig', array(
                    'setting' => $setting,
                    'form' => $editForm->createView(),
        ));
    }

}
