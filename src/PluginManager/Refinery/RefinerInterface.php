<?php

namespace Drupal\groomer\PluginManager\Refinery;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Provides an interface for Refiner plugins.
 *
 * @package Drupal\groomer\PluginManager\Refinery
 */
interface RefinerInterface extends PluginInspectionInterface {

  /**
   * Return the ID of the refiner.
   *
   * @return string
   *   Return the ID of the Refiner.
   */
  public function getId(): string;

  /**
   * Return the target of the Refiner.
   *
   * @return string
   *   Return the Target Signature of the Groomer of the Refiner.
   */
  public function getTarget(): string;

  /**
   * Alter the data provided to the Refiner from it's groomer.
   *
   * @param mixed $data
   *   The data array. It can be altered in any way.
   * @param mixed $original_object
   *   Original object that the Groomer used to build its data.
   */
  public function alter(&$data, $original_object);

}
