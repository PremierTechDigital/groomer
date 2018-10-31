<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

use Drupal\Core\Url;

/**
 * Handles grooming exceptions for 'link' type fields.
 *
 * @property \Drupal\groomer\Service\GroomerManager $groomerManager
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
class LinkEntityFieldGroomer extends EntityFieldGroomer {

  /**
   * {@inheritdoc}
   */
  public function process(array $value, int $i) {
    $label = $value['title'];
    $attributes = [
      'href'   => Url::fromUri($value['uri'])->toString(),
      'target' => $value['options']['attributes']['target'] ?? '_self',
    ];

    return $this->groomerManager->helpers->buildLink($label, $attributes);
  }

}
