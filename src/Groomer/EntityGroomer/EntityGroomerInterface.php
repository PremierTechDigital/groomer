<?php

namespace Drupal\groomer\Groomer\EntityGroomer;

use Drupal\groomer\Groomer\GroomerInterface;

/**
 * Provides an interface for EntityGroomers.
 *
 * @package Drupal\groomer\Groomer\EntityGroomer
 */
interface EntityGroomerInterface extends GroomerInterface {

  /**
   * Get the parent groomer for this entity.
   *
   * @return \Drupal\groomer\Groomer\EntityGroomer\EntityGroomer
   *   Return the parent groomer class linked to this groomer, if any.
   */
  public function getParentEntityGroomer() : ?EntityGroomerInterface;

  /**
   * Set Parent Groomer of this EntityField Groomer.
   *
   * @param \Drupal\groomer\Groomer\EntityGroomer\EntityGroomerInterface $parent
   *   The parent groomer class.
   */
  public function setParentEntityGroomer(EntityGroomerInterface $parent);

}
