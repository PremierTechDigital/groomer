<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

use Drupal\groomer\Groomer\EntityGroomer\EntityGroomerFactory;

/**
 * Handles exceptions for 'file' type fields.
 *
 * @property \Drupal\groomer\Service\GroomerManager $groomerManager
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
class FileEntityFieldGroomer extends EntityFieldGroomer {

  /**
   * {@inheritdoc}
   */
  public function process(array $value, int $i) {

    // Get the entity from the field value.
    $entity = $this->getFieldData()[$i]->entity;

    if ($entity === NULL) {
      return NULL;
    }

    // Get the translated entity.
    $entity = $this->groomerManager->helpers->ensureTranslated($entity);

    // Build groomer for this entity.
    $groomer = EntityGroomerFactory::build($entity, $this->groomerManager, $this->getEntityGroomer());

    // Return groomed data.
    return $groomer->groom();
  }

}
