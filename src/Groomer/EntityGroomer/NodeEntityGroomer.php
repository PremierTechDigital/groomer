<?php

namespace Drupal\groomer\Groomer\EntityGroomer;

use Drupal\Core\Url;

/**
 * Handles grooming exceptions for Node entities.
 *
 * @property \Drupal\node\Entity\Node $entity
 *
 * @package Drupal\groomer\Groomer\EntityGroomer
 */
final class NodeEntityGroomer extends EntityGroomer {

  /**
   * {@inheritdoc}
   */
  protected function getGroomedData() {
    $data = parent::getGroomedData();

    if (!\is_array($data)) {
      $data = ['content' => $data];
    }

    // Get Entity Title if it exists.
    if ($this->entity->getTitle() !== NULL) {
      $data['title'] = $this->entity->getTitle();
    }

    $url = Url::fromUri(
      'internal:/node/' . $this->entity->id(), ['absolute' => TRUE]
    )->toString();

    // Add other relevant information for nodes.
    $data['href'] = $url;

    return $data;
  }

}
