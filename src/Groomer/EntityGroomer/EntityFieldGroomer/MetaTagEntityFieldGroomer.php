<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

/**
 * Handles grooming exceptions for 'metatag' type fields.
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
class MetaTagEntityFieldGroomer extends EntityFieldGroomer {

  /**
   * {@inheritdoc}
   */
  public function process(array $value, int $i) {
    return $value;
  }

}
