<?php

namespace Drupal\groomer\Constants;

/**
 * Defines events for the groomer system.
 */
final class GroomingEvents {

  /**
   * Name of the event fired when grooming any object.
   *
   * This event allows modules to perform an action whenever a entity
   * object is groomed. The event listener method receives a
   * \Drupal\groomer\Event\GroomingEvent instance.
   *
   * @Event
   *
   * @var string
   */
  public const CORE = 'groom.core';

  /**
   * Name of the event fired when grooming an entity object.
   *
   * @Event
   *
   * @var string
   */
  public const ENTITY = 'groom.entity';

  /**
   * Name of the event fired when grooming an entity object's field.
   *
   * @Event
   *
   * @var string
   */
  public const ENTITY_FIELD = 'groom.entity.field';

  /**
   * Name of the event fired when grooming a form object.
   *
   * @Event
   *
   * @var string
   */
  public const FORM = 'groom.form';

  /**
   * Name of the event fired when grooming a menu object.
   *
   * @Event
   *
   * @var string
   */
  public const MENU = 'groom.menu';

}
