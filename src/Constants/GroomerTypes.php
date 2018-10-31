<?php

namespace Drupal\groomer\Constants;

/**
 * Defines types for the groomer system.
 */
final class GroomerTypes {

  /**
   * Global string defining Groomers that pre-process Drupal "Entities".
   *
   * @var string
   */
  public const ENTITY = 'entity';

  /**
   * Global string defining Groomers that pre-process Drupal "Fields".
   *
   * @var string
   */
  public const ENTITY_FIELD = 'field';

  /**
   * Global string defining Groomers that pre-process Drupal "Menus".
   *
   * @var string
   */
  public const MENU = 'menu';

  /**
   * Global string defining Groomers that pre-process Drupal "Forms".
   *
   * @var string
   */
  public const FORM = 'form';

}
