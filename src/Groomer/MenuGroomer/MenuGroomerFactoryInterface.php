<?php

namespace Drupal\groomer\Groomer\MenuGroomer;

use Drupal\groomer\Service\GroomerManager;

/**
 * Provides an Interface for the Menu Groomer Factory.
 *
 * This factory chooses the proper type of MenuGroomer given the type of Menu
 * provided.
 *
 * @package Drupal\groomer\Service
 */
interface MenuGroomerFactoryInterface {

  /**
   * Build the appropriate Groomer with the given object.
   *
   * @param array $menu_variables
   *   Render array of the Drupal Menu that is currently being processed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing many Drupal services.
   *
   * @return mixed
   *   Returns the proper Menu Groomer.
   */
  public static function build(array $menu_variables, GroomerManager $groomerManager);

}
