<?php

namespace Drupal\groomer_examples\Plugin\Groomer\Refiner;

use Drupal\groomer\PluginManager\Refinery\RefinerBase;

/**
 * Provide plugin to alter groomer data for all String Type fields.
 *
 * @Refiner(
 *   id = "groomer_examples_field_string_refine",
 *   target = "field.string"
 * )
 *
 * @package Drupal\groomer\Plugin\Groomer\Refiner
 */
final class StringFieldRefinerExample extends RefinerBase {

  /**
   * Add personal tweaks to data in this function.
   *
   * {@inheritdoc}
   */
  public function alter(&$data, $field_definition): void {

  }

}
