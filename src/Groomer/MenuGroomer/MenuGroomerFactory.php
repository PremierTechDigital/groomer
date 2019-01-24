<?php

namespace Drupal\groomer\Groomer\MenuGroomer;

use Drupal\groomer\Groomer\GroomerFactory;
use Drupal\groomer\Service\GroomerManager;
use Drupal\groomer\Constants\GroomerTypes;


/**
 * Manages which MenuGroomer class should be used to pre-process the menu.
 *
 * There is only one type for now, but this factory makes it
 * easy to extend for future form types or different types of form arrays.
 *
 * @package Drupal\groomer\Groomer\MenuGroomer
 */
class MenuGroomerFactory extends GroomerFactory {

  /**
   * Build the appropriate Groomer with the given object.
   *
   * @param array $menu_variables
   *   Variables of the menu obtained through the preprocess function.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing many Drupal services.
   *
   * @return mixed
   *   Returns the proper Entity Groomer.
   */
  public static function build(array $menu_variables, GroomerManager $groomerManager) {

    // Generate a signature for the groomer about to be instantiated.
    // Example: entity.node.basic_page.
    $signature = self::generateGroomerSignature(GroomerTypes::MENU, $menu_variables['menu_name']);

    // Check if the groomer is already created and registered in this factory.
    // If it is, return it.
    $registered = self::requestGroomer($signature);
    if ($registered !== NULL) {
      return $registered;
    }

    // Set the groomer class that we'll be using.
    $class = MenuGroomer::class;

    // Obtain groomer.
    $groomer = self::obtainGroomer($signature, $class, $menu_variables, $groomerManager, GroomerTypes::MENU);

    // Return the groomer that is now ready.
    return $groomer;

  }

}
