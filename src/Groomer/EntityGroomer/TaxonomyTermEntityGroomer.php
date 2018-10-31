<?php

namespace Drupal\groomer\Groomer\EntityGroomer;

/**
 * Handles grooming exceptions for TaxonomyTerm entities.
 *
 * @property \Drupal\taxonomy\Entity\Term $entity
 *
 * @package Drupal\groomer\Groomer\TaxonomyTermEntityGroomer
 */
final class TaxonomyTermEntityGroomer extends EntityGroomer {

  /**
   * {@inheritdoc}
   */
  protected function getGroomedData() {

    // Run the EntityGroomerBase function. No exceptions here.
    $data = parent::getGroomedData();

    // If there is no field data, just return the name.
    if (empty($data)) {
      return $this->entity->getName();
    }

    // Add media title to the processed data.
    $data['name'] = $this->entity->getName();

    return $data;
  }

}
