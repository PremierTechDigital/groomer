<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

/**
 * Handles exceptions for 'text_with_summary' type fields.
 *
 * @property \Drupal\groomer\Service\GroomerManager $groomerManager
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
class TextWithSummaryEntityFieldGroomer extends EntityFieldGroomer {

  /**
   * {@inheritdoc}
   */
  public function process(array $value, int $i) {
    unset($value['format'], $value['_attributes']);
    return $value;
  }

}
