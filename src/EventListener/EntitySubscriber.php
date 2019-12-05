<?php

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntitySubscriber implements EventSubscriber {

    private $container;
    public $needsFlush = false;
    public $entityManager;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getSubscribedEvents() {
        return array(
            'postPersist',
            'postUpdate',
            'preUpdate',
            'postRemove',
            'preRemove',
            'postFlush'
        );
    }

    public function performActions($args) {
        $this->entityManager = $args->getEntityManager();
        $this->needsFlush = true;
    }

    public function preUpdate($args) {
        $this->entityManager = $args->getEntityManager();
        $entity = $args->getEntity();

        if ($entity instanceof \App\Entity\PluginVersion && $args->hasChangedField('archive') && $args->getOldValue('archive') && $args->getNewValue('archive')) {
            $this->deleteFile($args->getOldValue('archive'));
        }
    }

    public function preRemove($args) {
        $this->entityManager = $args->getEntityManager();
        $entity = $args->getEntity();

        if ($entity instanceof \App\Entity\PluginVersion) {
            $this->deleteFile($entity->getArchive());
        }
    }

    public function deleteFile($file) {
        $filename = $this->container->getParameter('upload_directory') . '/' . $file;
        @unlink($filename);
    }

    public function postPersist($args) {
        $this->performActions($args);
    }

    public function postUpdate($args) {
        $this->performActions($args);
    }

    public function postRemove($args) {
        $this->performActions($args);
    }

    public function postFlush($eventArgs) {
        if ($this->needsFlush) {
            $this->needsFlush = false;
            $eventArgs->getEntityManager()->flush();
        }
    }

}
