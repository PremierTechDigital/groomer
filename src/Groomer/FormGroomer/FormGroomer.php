<?php

namespace Drupal\groomer\Groomer\FormGroomer;

use Drupal\groomer\Groomer\Groomer;
use Drupal\groomer\Constants\GroomerTypes;
use Drupal\groomer\Service\GroomerManager;

/**
 * Provides a Groomer for Drupal Forms.
 *
 * @package Drupal\groomer\Groomer\FormGroomer
 */
class FormGroomer extends Groomer {

  /**
   * Drupal Variables array of the form being groomed.
   *
   * @var array
   */
  protected $form;

  /**
   * FormGroomer constructor.
   *
   * @param array $form
   *   Form object currently being groomed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing Drupal services.
   */
  public function __construct(array $form, GroomerManager $groomerManager) {
    parent::__construct($form, $groomerManager);

    $this->type = GroomerTypes::FORM;
    $this->form = $form;

    // Example: form.node.basic_page.
    $this->sign($this->type, $this->form['id']);

  }

  /**
   * Gets the form from the current groomer.
   *
   * @return array
   *   Raw render array of the form associated to this groomer.
   */
  public function getForm() : array {
    return $this->form;
  }

  /**
   * {@inheritdoc}
   */
  protected function getGroomedData() {
    $action = $this->getForm()['#action'] ?? NULL;
    $method = $this->getForm()['#method'] ?? NULL;
    $elements = $this->getFormElements();

    $data = [
      'action'   => $action,
      'method'   => $method,
      'elements' => $elements,
    ];

    return $data;
  }

  /**
   * Parse variables array and get all form elements.
   *
   * @return array
   *   Returns all form elements properly separated.
   */
  public function getFormElements() : array {
    $elements = [];

    foreach ($this->getForm() as $id => $value) {
      if (!\is_array($value)) {
        continue;
      }

      if (isset($value['#input']) && $value['#input'] === TRUE) {
        $fieldType = $value['#type'];

        switch ($fieldType) {
          case 'input':
          case 'textarea':
          case 'textfield':
          case 'search':
          case 'password':
            $elements[$id] = $this->inputTextField($value);
            break;

          case 'radio':
          case 'select':
            $elements[$id] = $this->inputOptionsField($value);
            break;

          default:
            $elements[$id] = $value;
            break;

        }
      }
    }

    return $elements;
  }

  /**
   * Properly format (groom) form text field.
   *
   * @param array $field
   *   An array of the field attributes.
   *
   * @return array
   *   Formatted array of the attributes for the input field element.
   */
  private function inputTextField(array $field) : array {
    return [
      'type'       => 'input',
      'label'      => $field['#title'] ?? NULL,
      'attributes' => [
        'type' => $field['#type'] ?? NULL,
        'name' => $field['#name'] ?? NULL,
      ],
    ];
  }

  /**
   * Properly format (groom) input options field.
   *
   * @param array $field
   *   An array of the field attributes.
   *
   * @return array
   *   Formatted array of the attributes for the input field element.
   */
  private function inputOptionsField(array $field) : array {
    return [
      'type'    => $field['#type'],
      'options' => $this->formatOptions($field['#options']),
    ];
  }

  /**
   * Properly format (groom) form field format options.
   *
   * @param array $options
   *   Raw format options of a given field.
   *
   * @return array
   *   Return the format options of the form field.
   */
  public function formatOptions(array $options) : array {
    $formatted_options = [];
    foreach ($options as $key => $option) {
      $formatted_options[] = [
        'label'      => $option,
        'attributes' => [
          'value' => !\is_int($key) ? $key : strtolower($option),
        ],
      ];
    }
    return $formatted_options;
  }

}
