<?php

namespace Drupal\groomer\Event;

use Drupal\groomer\Constants\GroomingEvents;
use Drupal\groomer\Groomer\Groomer;
use Drupal\groomer\Constants\GroomerTypes;

/**
 * Handles the creation of the proper groomer event with a given type.
 *
 * @package Drupal\groomer\Event
 */
class GroomingEventFactory {

  /**
   * Builds and returns the appropriate event class.
   *
   * @param \Drupal\groomer\Groomer\Groomer $groomer
   *   Groomer that launched this event.
   *
   * @return array
   *   Return array of data with event information.
   */
  public static function build(Groomer $groomer): array {

    $class = NULL;
    $name = '';

    // Depending on the type, create the appropriate GroomerInterface.
    switch ($groomer->getType()) {

      case GroomerTypes::ENTITY:
        $class = EntityGroomingEvent::class;
        $name = GroomingEvents::ENTITY;
        break;

      case GroomerTypes::ENTITY_FIELD:
        $class = EntityFieldGroomingEvent::class;
        $name = GroomingEvents::ENTITY_FIELD;
        break;

      case GroomerTypes::MENU:
        $class = MenuGroomingEvent::class;
        $name = GroomingEvents::MENU;
        break;

      case GroomerTypes::FORM:
        $class = FormGroomingEvent::class;
        $name = GroomingEvents::FORM;
        break;

      default:
        $class = GroomingEvent::class;
        break;
    }

    $event = new $class($groomer);

    return [
      'event_name' => $name,
      'event_object' => $event,
    ];

  }

}
