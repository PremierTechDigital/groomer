<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

use Drupal\groomer\Constants\GroomerConfig;
use Drupal\groomer\Groomer\EntityGroomer\EntityGroomerFactory;

/**
 * Handles exceptions for 'entity_reference' type fields.
 *
 * @property \Drupal\groomer\Service\GroomerManager $groomerManager
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
class EntityReferenceEntityFieldGroomer extends EntityFieldGroomer {

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

    // If we're referencing nodes or paragraphs, we do a depth check.
    // This depth check should be ignored if we are grooming media or terms.
    // This is because medias and terms don't really mind being re-groomed.
    $bundle = $entity->getEntityTypeId();
    if ($bundle === 'node' || $bundle === 'paragraph') {
      if ($this->depth() > $this->groomerManager->config[GroomerConfig::MAX_RECURSIVE_DEPTH]) {
        return $groomer;
      }
    }

    // Return groomed data.
    return $groomer->groom();
  }

}
