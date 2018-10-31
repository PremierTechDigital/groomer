<?php

namespace Drupal\groomer\Constants;

/**
 * Defines constants for the groomer system.
 */
final class GroomerConfig {

  /**
   * Global string defining the name for the main configuration of the module.
   *
   * @var string
   */
  public const CONFIG_NAME = 'groomer.config';

  /**
   * String key for Groomer config to determine if it's on.
   *
   * @var string
   */
  public const POWERED = 'powered';

  /**
   * Auto Pre-Processor config string.
   *
   * @var string
   */
  public const AUTO_PRE_PROCESSOR = 'auto_pre_processor';

  /**
   * Harmony config string.
   *
   * @var string
   */
  public const HARMONY = 'harmony';

  /**
   * Remove field underscore prefix config string.
   *
   * @var string
   */
  public const REMOVE_FIELD_UNDERSCORE_PREFIX = 'remove_field_underscore_prefix';

  /**
   * Configure max iteration of recursive pre-processing in entity grooming.
   *
   * @var string
   */
  public const MAX_RECURSIVE_DEPTH = 'max_recursive_depth';

  /**
   * Entity Settings config string.
   *
   * @var string
   */
  public const ENTITY_TYPE_SETTINGS = 'groomer_entity_settings';

  /**
   * Entity excluded fields key config string.
   *
   * @var string
   */
  public const ENTITY_TYPE_EXCLUDED_FIELDS = 'groomer_entity_type_excluded_fields';

  /**
   * Entity bundles excluded fields key config string.
   *
   * @var string
   */
  public const ENTITY_TYPE_BUNDLE_EXCLUDED_FIELDS = 'groomer_entity_type_bundle_excluded_fields';

  /**
   * Menu Settings config string.
   *
   * @var string
   */
  public const MENU_SETTINGS = 'menu_settings';

}
