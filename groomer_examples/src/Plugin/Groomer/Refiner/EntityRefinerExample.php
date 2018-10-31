<?php

namespace Drupal\groomer_examples\Plugin\Groomer\Refiner;

use Drupal\groomer\PluginManager\Refinery\RefinerBase;

/**
 * Provide plugin to alter groomer data for all Entities.
 *
 * @Refiner(
 *   id = "groomer_examples_entity_refine",
 *   target = "entity"
 * )
 *
 * @package Drupal\groomer\Plugin\Groomer\Refiner
 */
final class EntityRefinerExample extends RefinerBase {

  /**
   * Add personal tweaks to data in this function.
   *
   * {@inheritdoc}
   */
  public function alter(&$data, $entity): void {

  }

}
