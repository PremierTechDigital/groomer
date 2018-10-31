<?php

namespace Drupal\groomer\PluginManager\Refinery\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Refiner item annotation object.
 *
 * Plugin Namespace: Plugin\Groomer\Refiner.
 *
 * @see Drupal\groomer\PluginManager\Refinery\Refinery
 * @see plugin_api
 *
 * @package Drupal\groomer\Annotation
 *
 * @Annotation
 */
final class Refiner extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The target groomer signature of Refiner.
   *
   * @var string
   */
  public $target;

}
