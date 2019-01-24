<?php

namespace Drupal\groomer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\groomer\Constants\GroomerConfig;
use Drupal\groomer\Service\GroomerHelpers;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a configuration form for the Groomer module.
 *
 * @package Drupal\groomer\Form
 */
class GroomerConfigForm extends ConfigFormBase {

  /**
   * Groomer Helper service providing helper functions.
   *
   * @var \Drupal\groomer\Service\GroomerHelpers
   */
  private $groomerHelpers;

  /**
   * GroomerConfigForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Drupal's Configuration Factory injected through DI.
   * @param \Drupal\groomer\Service\GroomerHelpers $groomerHelpers
   *   Groomer Helpers service injected through DI.
   */
  public function __construct(ConfigFactoryInterface $config_factory, GroomerHelpers $groomerHelpers) {
    parent::__construct($config_factory);

    $this->groomerHelpers = $groomerHelpers;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('groomer.helpers')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return 'groomer_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() : array {
    return [
      GroomerConfig::CONFIG_NAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) : array {

    // Set this for better organization of form values later.
    $form['#tree'] = TRUE;

    // Get the current configurations.
    $auto_pre_processor = $this->config(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::AUTO_PRE_PROCESSOR);
    $harmony = $this->config(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::HARMONY);
    $remove_field_underscore_prefix = $this->config(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::REMOVE_FIELD_UNDERSCORE_PREFIX);
    $max_recursive_depth = $this->config(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::MAX_RECURSIVE_DEPTH);

    // Enable the Auto-Preprocessor.
    $form[GroomerConfig::AUTO_PRE_PROCESSOR] = [
      '#type'          => 'checkbox',
      '#title'         => t('Enable the Auto-Pre-Processor for Templates'),
      '#description'   => t(
        '<em>Check this box to automatically pre-process entities and objects. This will add a <strong>data</strong> variable to most templates, including groomed data.</em>'
      ),
      '#default_value' => !empty($auto_pre_processor) ? $auto_pre_processor : 0,
      '#weight'        => -15,
    ];

    // Enable the automatic removal "field_" prefix when grooming fields.
    $form[GroomerConfig::REMOVE_FIELD_UNDERSCORE_PREFIX] = [
      '#type'          => 'checkbox',
      '#title'         => t('Remove <strong>"field_"</strong> prefix when grooming Entity Fields.'),
      '#description'   => t(
        '<em>Check this box to remove the prefix from field names when grooming entity fields. Array keys for specific fields will have the full field name otherwise.'
      ),
      '#default_value' => !empty($remove_field_underscore_prefix) ? $remove_field_underscore_prefix : 0,
      '#weight'        => -15,
    ];

    // Enable the automatic removal "field_" prefix when grooming fields.
    $form[GroomerConfig::MAX_RECURSIVE_DEPTH] = [
      '#type'          => 'select',
      '#title'         => t('Select the max depth for recursive grooming in Entities.'),
      '#description'   => t(
        '<em>A failsafe is developed to prevent infinite loops. After reaching a certain depth, the data will no longer be automatically pre-processed. Instead, the data will be replaced by the groomer class itself. You can then call the getData() function manually on that class to obtain the data desired. You can customize how low the module goes by default.'
      ),
      '#options'       => [
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
      ],
      '#default_value' => !empty($max_recursive_depth) ? $max_recursive_depth : 3,
      '#weight'        => -15,
    ];

    // Enable the use of Harmony.
    $form[GroomerConfig::HARMONY] = [
      '#type'          => 'checkbox',
      '#title'         => t('Enable Harmony for your Custom Theme.'),
      '#description'   => t(
        '<em>Check this box to enable Harmony. After enabling this, copy the <strong>harmony</strong> folder found in the ./tools folder of the module to the <strong>root of your custom theme</strong>. Please refer to the README.md documentation for instructions!'
      ),
      '#default_value' => !empty($harmony) ? $harmony : 0,
      '#weight'        => -15,
    ];

    // Attach entity exclusions fields.
    // @todo - This will be changed to exclude certain content types altogether.
    $this->attachEntityExclusionsForm($form);

    // Attach menu exclusions fields.
    // @todo - This will be changed to exclude certain content types altogether.
    $this->attachMenuExclusionsForm($form);

    // Return the form with all necessary fields.
    return parent::buildForm($form, $form_state);
  }

  /**
   * Add form fields allowing exclusion of certain fields from grooming.
   *
   * @param mixed $form
   *   Form to attach new elements to.
   */
  public function attachEntityExclusionsForm(&$form): void {

    // Retrieve current values from site configuration.
    $entity_settings = $this->config(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::ENTITY_TYPE_SETTINGS);

    // Get the site's entity bundles.
    $entity_bundles = $this->groomerHelpers->getSiteEntityBundles();

    // This part of the form handles the concept of filtering in groomers.
    // Instantiate a Vertical Tabs collection to organize the form.
    $form[GroomerConfig::ENTITY_TYPE_SETTINGS] = [
      '#type'        => 'details',
      '#title'       => t('Exclusion Settings for Entities'),
      '#description' => t(
        'By default, when using the groomer on an Entity, it pre-processes all fields in the entity. You can filter which fields are pre-processed and omit certain fields.<br>It is recommended to exclude fields that may produce a lot of overhead or are prone to inducing infinite loops.'
      ),
      '#weight'      => -1,
    ];

    // Loop in all entity bundles.
    // Create a form for each entity type and bundle.
    foreach ($entity_bundles as $entity_type_id => $bundles) {

      // Add a details section for the Entity Type.
      $form[GroomerConfig::ENTITY_TYPE_SETTINGS][$entity_type_id] = [
        '#type'  => 'details',
        '#title' => t(
          '<strong>@entity_type_id</strong>',
          ['@entity_type_id' => ucwords($entity_type_id) . 's']
        ),
        '#description' => t(
          '<br>Enable exclusions on fields for all entities of this type here, or exclude fields for certain bundles further below.<br>'
        ),
      ];

      // Instantiate a fieldset for the fields of the entity type.
      $form[GroomerConfig::ENTITY_TYPE_SETTINGS][$entity_type_id][GroomerConfig::ENTITY_TYPE_EXCLUDED_FIELDS] = [
        '#type'        => 'fieldset',
        '#title'       => t(
          'Fields to exclude for the <strong>@entity_type_id</strong> entity type',
          ['@entity_type_id' => ucwords($entity_type_id)]
        ),
        '#description' => t(
          '<br><strong>Fields excluded at this level will be excluded for grooming in all entities of this type.</strong>'
        ),
        '#collapsible' => TRUE,
        '#collapsed'   => TRUE,
        '#tree'        => TRUE,
      ];

      // Get field definitions for the given bundle.
      $entity_type_field_configs = $this->groomerHelpers->getEntityTypeFieldDefinitions($entity_type_id);

      // Generate checkboxes to exclude grooming of fields for an entity type.
      /* @var integer $id */
      /* @var \Drupal\field\FieldStorageConfigInterface $entity_type_field_config */
      foreach ($entity_type_field_configs as $id => $entity_type_field_config) {
        $entity_type_field_configs[$id]
          = "Exclude {$entity_type_field_config->getLabel()} <strong>[{$entity_type_field_config->getName()}]</strong> ({$entity_type_field_config->getType()})";
      }
      // Configure which fields should be pre-processed.
      $form[GroomerConfig::ENTITY_TYPE_SETTINGS][$entity_type_id][GroomerConfig::ENTITY_TYPE_EXCLUDED_FIELDS] = [
        '#type'          => 'checkboxes',
        '#options'       => $entity_type_field_configs,
        '#default_value' => !empty($entity_settings[$entity_type_id][GroomerConfig::ENTITY_TYPE_EXCLUDED_FIELDS]) ? $entity_settings[$entity_type_id][GroomerConfig::ENTITY_TYPE_EXCLUDED_FIELDS] : [],
      ];

      // Loop in all bundles for the given entity type.
      foreach ($bundles as $bundle => $label) {

        // Add a details section for the Entity Bundle.
        $form[GroomerConfig::ENTITY_TYPE_SETTINGS][$entity_type_id][$bundle] = [
          '#type'  => 'details',
          '#title' => t(
            '<strong>@bundle</strong>', [
              '@bundle' => ucwords(
                str_replace('_', ' ', $bundle)
              ),
            ]
          ),
        ];

        // Instantiate a fieldset for the fields of the bundle.
        // This fieldset is only visible when the above checkbox is checked.
        $form[GroomerConfig::ENTITY_TYPE_SETTINGS][$entity_type_id][$bundle][GroomerConfig::ENTITY_TYPE_BUNDLE_EXCLUDED_FIELDS] = [
          '#type'        => 'fieldset',
          '#title'       => t(
            'Fields to exclude for <strong>@bundle</strong>',
            ['@bundle' => ucwords($label)]
          ),
          '#collapsible' => TRUE,
          '#collapsed'   => TRUE,
          '#tree'        => TRUE,
          '#states'      => [
            'invisible' => [
              ":input[name=\"entity_settings[{$entity_type_id}][{$bundle}][enabled]\"]" => ['checked' => FALSE],
            ],
          ],
        ];

        // Get field definitions for the given bundle.
        $entity_type_bundle_field_configs = $this->groomerHelpers->getEntityTypeBundleFieldDefinitions($entity_type_id, $bundle);

        /* @var integer $id */
        /* @var \Drupal\field\FieldConfigInterface $entity_type_bundle_field_config */
        foreach ($entity_type_bundle_field_configs as $id => $entity_type_bundle_field_config) {
          $entity_type_bundle_field_configs[$id]
            = "Exclude {$entity_type_bundle_field_config->getLabel()} <strong>[{$entity_type_bundle_field_config->getName()}]</strong> ({$entity_type_bundle_field_config->getType()})";
        }

        // Configure which fields should be pre-processed.
        $form[GroomerConfig::ENTITY_TYPE_SETTINGS][$entity_type_id][$bundle][GroomerConfig::ENTITY_TYPE_BUNDLE_EXCLUDED_FIELDS] = [
          '#type'          => 'checkboxes',
          '#options'       => $entity_type_bundle_field_configs,
          '#default_value' => !empty($entity_settings[$entity_type_id][$bundle][GroomerConfig::ENTITY_TYPE_BUNDLE_EXCLUDED_FIELDS]) ? $entity_settings[$entity_type_id][$bundle][GroomerConfig::ENTITY_TYPE_BUNDLE_EXCLUDED_FIELDS] : [],
        ];
      }
    }
  }

  /**
   * Add form fields allowing exclusion of certain menus from grooming.
   *
   * @param mixed $form
   *   Form to attach new elements to.
   */
  public function attachMenuExclusionsForm(&$form): void {

    // Retrieve current values from site configuration.
    $menu_settings = $this->config(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::MENU_SETTINGS);

    // Get the site's custom menus.
    $custom_menus = menu_ui_get_menus('true');

    // This part of the form handles the concept of grooming Menus.
    $form[GroomerConfig::MENU_SETTINGS] = [
      '#type'  => 'details',
      '#title' => t('Enable grooming for select Menus'),
      '#description' => t('Menu grooming can be very heavy. As a result, grooming for all menus is disabled by default. You can enable grooming for select menus here.'),
    ];

    // Loop in the list of menu names to set parts of the form.
    foreach ($custom_menus as $id => $name) {
      // Add a checkbox to enable grooming for the given menu.
      $form[GroomerConfig::MENU_SETTINGS][$id] = [
        '#type'          => 'checkbox',
        '#title'         => t(
          'Enable grooming for the <strong>@id</strong> menu', ['@id' => $id]
        ),
        '#description'   => t(
          "<em>Check this box to enable grooming for the <strong>@id</strong> menu.\n
						If the box is left unchecked, the menu will not be groomed and the <strong>data</strong> variable will not be present in this menu's theme file.
						</em>", ['@id' => $id]
        ),
        '#default_value' => !empty($menu_settings[$id]) ? $menu_settings[$id] : 0,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) : void {
    // Retrieve the configuration.
    $values = $form_state->getValues();

    unset($values['actions']);

    $this->configFactory->getEditable(GroomerConfig::CONFIG_NAME)
      ->set(GroomerConfig::AUTO_PRE_PROCESSOR, $values[GroomerConfig::AUTO_PRE_PROCESSOR])
      ->set(GroomerConfig::HARMONY, $values[GroomerConfig::HARMONY])
      ->set(GroomerConfig::REMOVE_FIELD_UNDERSCORE_PREFIX, $values[GroomerConfig::REMOVE_FIELD_UNDERSCORE_PREFIX])
      ->set(GroomerConfig::ENTITY_TYPE_SETTINGS, $values[GroomerConfig::ENTITY_TYPE_SETTINGS])
      ->set(GroomerConfig::MENU_SETTINGS, $values[GroomerConfig::MENU_SETTINGS])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
