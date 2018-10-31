<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

/**
 * Handles exceptions for 'image' type fields.
 *
 * @property \Drupal\groomer\Service\GroomerManager $groomerManager
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
class ImageEntityFieldGroomer extends EntityFieldGroomer {

  /**
   * {@inheritdoc}
   */
  public function process(array $value, int $i) {
    // Get the entity from the field value.
    $entity = $this->getFieldData()[$i]->entity;

    if ($entity === NULL) {
      return NULL;
    }

    // Image file data, groomed by yours truly.
    $file = $this->groomerManager->groom($entity);
    $src = $file['href'];
    $alt = $this->getFieldData()->getValue()[$i]['alt'] ?? '';
    $title = $this->getFieldData()->getValue()[$i]['title'] ?? '';
    $data = [
      'uri' => $file['uri'],
    ];
    $attributes = [
      'src'   => $src,
      'alt'   => $alt,
      'title' => $title,
    ];

    return $this->groomerManager->helpers->buildImage($data, $attributes);

  }

}
