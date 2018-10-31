<?php

namespace Drupal\groomer\Service;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfo;
use Drupal\Core\Url;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Provides helper functions to be used across the module's code.
 *
 * @package Drupal\groomer\Service
 */
final class GroomerHelpers {

  /**
   * EntityTypeBundleInfo service from Drupal injected through DI.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  private $entityTypeBundleInfo;

  /**
   * Entity Field Manager Service from Drupal injected through DI.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  private $entityFieldManager;

  /**
   * GroomerHelpers constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeBundleInfo $entityTypeBundleInfo
   *   Drupal's EntityTypeBundleInfo service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   Drupal's EntityManager service.
   */
  public function __construct(EntityTypeBundleInfo $entityTypeBundleInfo, EntityFieldManagerInterface $entityFieldManager) {
    $this->entityTypeBundleInfo = $entityTypeBundleInfo;
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * Return the site's entity types and it's bundles in a custom array.
   *
   * This array will be used by the form to create a nice configuration
   * space.
   *
   * @return array
   *   Site's entity types and bundles.
   */
  public function getSiteEntityBundles() : array {
    $bundles = [];

    // @todo - Oh boy. This shouldn't be hardcoded...Find a solution to this.
    // @todo - You can add a configuration page for these values.
    $entity_types = [
      'node',
      'media',
      'paragraph',
      'taxonomy_term',
      'file',
    ];

    foreach ($entity_types as $entity_type) {
      $entity_type_bundles = $this->entityTypeBundleInfo->getBundleInfo(
        $entity_type
      );

      foreach ($entity_type_bundles as $bundle_machine_name => $bundle) {
        $bundles[$entity_type][$bundle_machine_name] = $bundle['label'];
      }
    }

    return $bundles;
  }

  /**
   * Get the list of fields for a given entity.
   *
   * @param string $entity_type_id
   *   Entity Type in string machine_name format.
   *
   * @return array
   *   Return list of all fields for the given entity/bundle pair.
   */
  public function getEntityTypeFieldDefinitions(string $entity_type_id) : array {
    // Return only custom fields, filtering out base fields.
    $fields = array_filter($this->entityFieldManager->getFieldStorageDefinitions($entity_type_id), function ($object) {
      return \in_array(FieldStorageConfigInterface::class, class_implements($object), TRUE);
    }
    );
    return $fields;
  }

  /**
   * Get the list of fields for a given entity.
   *
   * @param string $entity_type_id
   *   Entity Type in string machine_name format.
   * @param string $bundle
   *   Entity Bundle in string machine_name format.
   *
   * @return array
   *   Return list of all fields for the given entity/bundle pair.
   */
  public function getEntityTypeBundleFieldDefinitions(string $entity_type_id, string $bundle) : array {
    // Return only custom fields, filtering out base fields.
    $fields = array_filter($this->entityFieldManager->getFieldDefinitions($entity_type_id, $bundle), function ($object) {
      return \in_array(FieldConfigInterface::class, class_implements($object), TRUE);
    }
    );
    return $fields;
  }

  /**
   * Ensure an entity is translated to the current running language.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Entity to fetch translation from.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   Return translated entity in current language if found.
   */
  public function ensureTranslated(ContentEntityInterface $entity) : ContentEntityInterface {
    // Return null if we somehow try to translate null.
    if ($entity === NULL) {
      return NULL;
    }

    // Get current language.
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // Remember to check if translation exists.
    // Drupal isn't handling the printing of the translation automatically.
    if ($entity->hasTranslation($language)) {
      $entity = $entity->getTranslation($language);
    }

    return $entity;
  }

  /**
   * Build groomed data for links.
   *
   * @param string $label
   *   The label of the link.
   * @param array $attributes
   *   The HTML attributes of the link. (target, href, class, id, etc)
   * @param array $variables
   *   Extra variables that the link may have.
   *
   * @return array
   *   Return link data in a properly formatted array.
   */
  public function buildLink(string $label, array $attributes, array $variables = []) : array {
    // The 'href' key may sometimes have a node object instead of a URL.
    // Use an above function to format the link to this node.
    if (isset($attributes['href']) && $attributes['href'] instanceof NodeInterface) {
      $attributes['href'] = $this->getNodeHref($attributes['href']);
    }

    // Return an array formatted appropriately.
    return array_merge(
      [
        'label'      => $label,
        'attributes' => $attributes,
      ],
      $variables
    );
  }

  /**
   * Build groomed data for images.
   *
   * @param array $data
   *   Image data (url, uri, etc).
   * @param array $attributes
   *   Image attributes (src, target, etc).
   *
   * @return array
   *   Image data properly formatted in a clean array.
   */
  public function buildImage(array $data, array $attributes) : array {
    return [
      'uri'        => $data['uri'],
      'attributes' => $attributes,
    ];
  }

  /**
   * Get the link to a given node.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Drupal node object to get URL from.
   *
   * @return \Drupal\Core\GeneratedUrl|string
   *   Generated Node URL.
   */
  public function getNodeHref(Node $node) {
    // @todo - You may want to modify this function to get the node's link and ALIAS if it has one, as this is cleaner.
    return Url::fromUri('internal:/node/' . $node->id(), ['absolute' => TRUE])->toString();
  }

}
