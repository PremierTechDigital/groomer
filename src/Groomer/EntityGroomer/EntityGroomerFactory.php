<?php

namespace Drupal\groomer\Groomer\EntityGroomer;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\groomer\Constants\GroomerTypes;
use Drupal\groomer\Groomer\GroomerInterface;
use Drupal\groomer\Groomer\GroomerFactory;
use Drupal\groomer\Service\GroomerManager;

/**
 * Handles the creation of the proper groomer for a given Entity.
 *
 * There are many types of Entities in Drupal. Each Entity may
 * have their own specific arrangements that will affect the way the data is
 * pre-processed. We will handle these in respective classes.
 *
 * By checking the entity type, the Factory builds the appropriate Groomer.
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityGroomer
 */
final class EntityGroomerFactory extends GroomerFactory {

  /**
   * Build the appropriate Groomer with the given object.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Drupal Entity that is currently being processed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing many Drupal services.
   * @param \Drupal\groomer\Groomer\EntityGroomer\EntityGroomer $parentEntityGroomer
   *   Parent entity groomer that called this groomer, if applicable.
   *
   * @return mixed
   *   Returns the proper Entity Groomer.
   */
  public static function build(ContentEntityInterface $entity, GroomerManager $groomerManager, EntityGroomer $parentEntityGroomer = NULL) : GroomerInterface {

    // Generate a signature for the groomer about to be instantiated.
    // Example: entity.node.basic_page.
    $signature = self::generateGroomerSignature(GroomerTypes::ENTITY, $entity->getEntityTypeId(), $entity->bundle(), $entity->id());

    // Check if the groomer is already created and registered in this factory.
    // If it is, return it.
    $registered = self::requestGroomer($signature);
    if ($registered !== NULL) {
      return $registered;
    }

    // Otherwise, we instantiate it and register it in the factory.
    // Get the type of entity.
    // i.e. "node", "paragraph", "media", etc.
    $entity_type = $entity->getEntityTypeId();

    // Depending on the type, create the appropriate "Groomer".
    switch ($entity_type) {
      case 'node':
        $class = NodeEntityGroomer::class;
        break;

      case 'file':
        $class = FileEntityGroomer::class;
        break;

      case 'media':
        $class = MediaEntityGroomer::class;
        break;

      case 'paragraph':
        $class = ParagraphEntityGroomer::class;
        break;

      case 'taxonomy_term':
        $class = TaxonomyTermEntityGroomer::class;
        break;

      default:
        $class = EntityGroomer::class;
        break;
    }

    // Obtain groomer.
    $groomer = self::obtainGroomer($signature, $class, $entity, $groomerManager, GroomerTypes::ENTITY);

    // If the parent parameter is not null, we'll set it to the groomer.
    if ($parentEntityGroomer !== NULL) {
      /* @var \Drupal\groomer\Groomer\EntityGroomer\EntityGroomer $groomer */
      $groomer->setParentEntityGroomer($parentEntityGroomer);
    }

    // Return the groomer that is now ready.
    return $groomer;
  }

}
