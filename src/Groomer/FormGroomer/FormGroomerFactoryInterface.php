<?php

namespace Drupal\groomer\Groomer\FormGroomer;

use Drupal\groomer\Service\GroomerManager;

/**
 * Provides an Interface for the Form Groomer Factory.
 *
 * @package Drupal\groomer\Service
 */
interface FormGroomerFactoryInterface {

  /**
   * Build the appropriate Groomer with the given object.
   *
   * @param array $form
   *   Form render array being groomed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing many Drupal services.
   *
   * @return mixed
   *   Returns the proper Form Groomer.
   */
  public static function build(array $form, GroomerManager $groomerManager);

}
