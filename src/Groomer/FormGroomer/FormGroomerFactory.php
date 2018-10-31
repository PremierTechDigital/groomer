<?php

namespace Drupal\groomer\Groomer\FormGroomer;

use Drupal\groomer\Groomer\GroomerInterface;
use Drupal\groomer\Groomer\GroomerFactory;
use Drupal\groomer\Service\GroomerManager;

/**
 * Manages which FormGroomer class should be used to pre-process Drupal forms.
 *
 * There is only one type for now, but this factory makes it easy to
 * extend for future form types or different types of form arrays.
 *
 * @package Drupal\groomer\Groomer\FormGroomer
 */
class FormGroomerFactory extends GroomerFactory implements FormGroomerFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public static function build(array $form, GroomerManager $groomerManager) : GroomerInterface {
    return new FormGroomer($form, $groomerManager);
  }

}
