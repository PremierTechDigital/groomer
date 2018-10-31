<?php

namespace Drupal\groomer_examples\Plugin\Groomer\Refiner;

use Drupal\groomer\PluginManager\Refinery\RefinerBase;

/**
 * Provide plugin to alter groomer data for all Basic Pages.
 *
 * @Refiner(
 *   id = "groomer_examples_basic_page_refine",
 *   target = "entity.node.basic_page"
 * )
 *
 * @package Drupal\groomer\Plugin\Groomer\Refiner
 */
final class BasicPageRefinerExample extends RefinerBase {

  /**
   * Add personal tweaks to data in this function.
   *
   * {@inheritdoc}
   */
  public function alter(&$data, $entity): void {

  }

}
