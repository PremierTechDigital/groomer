<?php

namespace Drupal\groomer\Groomer\EntityGroomer;

/**
 * Handles grooming exceptions for Media entities.
 *
 * @property \Drupal\media\MediaInterface $entity
 *
 * @package Drupal\groomer\Groomer\EntityGroomer
 */
final class MediaEntityGroomer extends EntityGroomer {

  /**
   * {@inheritdoc}
   */
  protected function getGroomedData() {
    $data = parent::getGroomedData();

    // Add media title to the processed data.
    $data['media_title'] = $this->entity->get('name')->getValue()[0]['value'];

    return $data;
  }

}
