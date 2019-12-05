<?php

namespace App\Controller;

use App\Entity\PluginVersion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

/**
 * PluginVersion controller.
 *
 */
class PluginVersionController extends Controller {

    /**
     * Lists all pluginVersion entities.
     *
     */
    public function indexAction($page) {
        $result = $this->getEntityManager()->getRepository('App:PluginVersion')->findAll();
        $pluginVersions = $this->get('knp_paginator')->paginate($result, $page);

        return $this->render('pluginversion/index.html.twig', array(
                    'pluginVersions' => $pluginVersions,
        ));
    }

    /**
     * Creates a new pluginVersion entity.
     *
     */
    public function newAction(Request $request) {
        $pluginVersion = new PluginVersion();
        $form = $this->createForm('App\Form\PluginVersionType', $pluginVersion);
        $form->handleRequest($request);

        $this->checkVersion($form, $pluginVersion);

        if ($form->isSubmitted() && $form->isValid()) {
         

            $this->manageArchive($pluginVersion, $form);

            $em = $this->getDoctrine()->getManager();
            $em->persist($pluginVersion);
            $em->flush();

            return $this->redirectToRoute('plugin-version_edit', array('id' => $pluginVersion->getId()));
        }

        return $this->render('pluginversion/new.html.twig', array(
                    'pluginVersion' => $pluginVersion,
                    'form' => $form->createView(),
        ));
    }

    public function checkVersion($form, $pluginVersion) {
        if ($form->isSubmitted()) {
            $v = $this->getEntityManager()->getRepository('App:PluginVersion')->checkForHigherReleaseUpdateVersion($form["version"]->getData(), $pluginVersion);
            if ($v and $form["version"]->getData() != $pluginVersion->getVersion()) {
                $form->get('version')->addError(new FormError('Error! This version is not higher than existing Plugin Version.'));
            }
        }
    }

    public function manageArchive($pluginVersion, $form) {
        $file = $pluginVersion->getArchive();

        if ($form['archive']->getData()) {
            // Generate a unique name for the file before saving it
            $id = $form['version']->getData();

            $fileName = md5(uniqid()) . '.' . $file->guessExtension();


            // Move the file to the directory where archives are stored
            $file->move(
                    $this->getParameter('upload_directory'), $fileName
            );

            // Update the 'archive' property to store the ZIP file name
            // instead of its contents
            $pluginVersion->setArchive($fileName);
        }
    }

    /**
     * Displays a form to edit an existing pluginVersion entity.
     *
     */
    public function editAction(Request $request, PluginVersion $pluginVersion) {

        $editForm = $this->createForm('App\Form\PluginVersionType', $pluginVersion);
        $editForm->handleRequest($request);

        $this->checkVersion($editForm, $pluginVersion);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
           

            $this->manageArchive($pluginVersion, $editForm);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plugin-version_edit', array('id' => $pluginVersion->getId()));
        }

        return $this->render('pluginversion/edit.html.twig', array(
                    'pluginVersion' => $pluginVersion,
                    'form' => $editForm->createView(),
        ));
    }

    public function deleteAction($id) {
        $pluginVersion = $this->getEntityManager()->getRepository('App:PluginVersion')->find($id);

        if ($pluginVersion) {
         

            $em = $this->getDoctrine()->getManager();
            $em->remove($pluginVersion);
            $em->flush();
        }

        return $this->returnJsonResponse(array(
                    'success' => true,
                    'data' => $this->generateUrl('plugin-version_index')
        ));
    }

}
