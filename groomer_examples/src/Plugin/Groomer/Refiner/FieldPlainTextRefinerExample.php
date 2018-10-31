<?php

namespace Drupal\groomer_examples\Plugin\Groomer\Refiner;

use Drupal\groomer\PluginManager\Refinery\RefinerBase;

/**
 * Provide plugin to alter groomer data for String type (field_plain_text).
 *
 * @Refiner(
 *   id = "groomer_examples_field_plain_text_refine",
 *   target = "field.string.field_plain_text"
 * )
 *
 * @package Drupal\groomer\Plugin\Groomer\Refiner
 */
final class FieldPlainTextRefinerExample extends RefinerBase {

  /**
   * Add personal tweaks to data in this function.
   *
   * {@inheritdoc}
   */
  public function alter(&$data, $field_definition): void {

  }

}
