<?php

namespace Drupal\groomer\Groomer\MenuGroomer;

use Drupal\groomer\Groomer\GroomerFactory;
use Drupal\groomer\Service\GroomerManager;

/**
 * Manages which MenuGroomer class should be used to pre-process the menu.
 *
 * There is only one type for now, but this factory makes it
 * easy to extend for future form types or different types of form arrays.
 *
 * @package Drupal\groomer\Groomer\MenuGroomer
 */
class MenuGroomerFactory extends GroomerFactory implements MenuGroomerFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public static function build(array $menu_variables, GroomerManager $groomerManager) {
    // Get the name of the menu.
    // i.e. "node", "paragraph", "media", etc.
    $name = $menu_variables['menu_name'];

    // Check for some ugly caching.
    if (isset(self::$storage[$name])) {
      return self::$storage[$name];
    }

    // Create the Groomer.
    $groomer = new MenuGroomer($menu_variables, $groomerManager);

    // Add the groomer to the ugly caching.
    self::$storage[$name] = $groomer;

    return $groomer;
  }

}
