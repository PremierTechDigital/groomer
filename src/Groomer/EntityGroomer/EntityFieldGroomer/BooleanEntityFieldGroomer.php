<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

/**
 * Handles exceptions for 'boolean' type fields.
 *
 * @property \Drupal\groomer\Service\GroomerManager $groomerManager
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
class BooleanEntityFieldGroomer extends EntityFieldGroomer {

  /**
   * {@inheritdoc}
   */
  public function process(array $value, int $i) {
    if ($value['value']) {
      $value = TRUE;
    }

    return $value;
  }

}
