<?php

namespace Drupal\groomer_examples\Plugin\Groomer\Refiner;

use Drupal\groomer\PluginManager\Refinery\RefinerBase;

/**
 * Provide plugin to alter groomer data for all Nodes.
 *
 * @Refiner(
 *   id = "groomer_examples_node_refine",
 *   target = "entity.node"
 * )
 *
 * @package Drupal\groomer\Plugin\Groomer\Refiner
 */
final class NodeRefinerExample extends RefinerBase {

  /**
   * Add personal tweaks to data in this function.
   *
   * {@inheritdoc}
   */
  public function alter(&$data, $node): void {

  }

}
