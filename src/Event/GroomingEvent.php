<?php

namespace Drupal\groomer\Event;

use Drupal\groomer\Groomer\GroomerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Provides an Event for when Entities are groomed.
 *
 * @package Drupal\groomer\Event
 */
class GroomingEvent extends Event {
  /**
   * The groomer that fired the event.
   *
   * @var \Drupal\groomer\Groomer\Groomer
   */
  public $groomer;

  /**
   * Constructs the object.
   *
   * @param \Drupal\groomer\Groomer\GroomerInterface $groomer
   *   Groomer attached to the event that was triggered.
   */
  public function __construct(GroomerInterface $groomer) {
    $this->groomer = $groomer;
  }

}
