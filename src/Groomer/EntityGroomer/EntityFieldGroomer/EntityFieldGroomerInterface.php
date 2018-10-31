<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

use Drupal\groomer\Groomer\EntityGroomer\EntityGroomerInterface;
use Drupal\groomer\Groomer\GroomerInterface;

/**
 * Provides an interface for Entity Field Groomers.
 *
 * @package Drupal\groomer\Groomer\EntityGroomer
 */
interface EntityFieldGroomerInterface extends GroomerInterface {

  /**
   * Get the Groomer of the entity containing this Field.
   *
   * @return \Drupal\groomer\Groomer\EntityGroomer\EntityGroomer
   *   Return the parent groomer class linked to this groomer, if any.
   */
  public function getEntityGroomer() : ?EntityGroomerInterface;

  /**
   * Set the Groomer of the entity containing this Field.
   *
   * @param \Drupal\groomer\Groomer\EntityGroomer\EntityGroomerInterface $entityGroomer
   *   The parent groomer class.
   */
  public function setEntityGroomer(EntityGroomerInterface $entityGroomer);

}
