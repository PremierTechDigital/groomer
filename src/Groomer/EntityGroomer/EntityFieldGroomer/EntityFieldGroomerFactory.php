<?php

namespace Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\groomer\Constants\GroomerTypes;
use Drupal\groomer\Groomer\EntityGroomer\EntityGroomer;
use Drupal\groomer\Groomer\EntityGroomer\EntityGroomerFactory;
use Drupal\groomer\Groomer\GroomerInterface;
use Drupal\groomer\Groomer\GroomerFactory;
use Drupal\groomer\Service\GroomerManager;

/**
 * Handles the creation of the proper field groomer with a given Field.
 *
 * There are many types of Fields in Drupal. Each
 * Field will have their own specific arrangements that will affect the way the
 * data is pre-processed. We will handle these in respective classes.
 *
 * By checking the field type, the Factory builds the appropriate
 * EntityFieldGroomer.
 *
 * @package Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer
 */
final class EntityFieldGroomerFactory extends GroomerFactory {

  /**
   * Build the appropriate Groomer with the given object.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $entity_field_data
   *   Drupal Field data of the field that is currently being processed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing many Drupal services.
   * @param \Drupal\groomer\Groomer\EntityGroomer\EntityGroomer $entityGroomer
   *   The Groomer of the entity that contains this field.
   *
   * @return mixed
   *   Returns the proper Entity Groomer.
   */
  public static function build(FieldItemListInterface $entity_field_data, GroomerManager $groomerManager, EntityGroomer $entityGroomer = NULL): GroomerInterface {

    // Generate a signature for the groomer about to be instantiated.
    // Example: field.email.field_contact_email.
    $signature = self::generateGroomerSignature(
      GroomerTypes::ENTITY_FIELD,
      $entity_field_data->getFieldDefinition()->getType(),
      $entity_field_data->getName(),
      $entity_field_data->getFieldDefinition()->getTargetEntityTypeId(),
      $entity_field_data->getFieldDefinition()->getTargetBundle(),
      $entity_field_data->getEntity()->id()
    );

    // Check if the groomer is already created and registered in this factory.
    // If it is, return it.
    $registered = self::requestGroomer($signature);
    if ($registered !== NULL) {
      return $registered;
    }

    // Otherwise, we instantiate it and register it in the factory.
    // Get the type of field.
    $field_type = $entity_field_data->getFieldDefinition()->getType();

    // Initialize variable to hold class.
    $class = NULL;

    // Depending on the type, create the appropriate GroomerInterface.
    switch ($field_type) {

      case 'file':
        $class = FileEntityFieldGroomer::class;
        break;

      case 'boolean':
        $class = BooleanEntityFieldGroomer::class;
        break;

      case 'datetime':
        $class = DateTimeEntityFieldGroomer::class;
        break;

      case 'image':
        $class = ImageEntityFieldGroomer::class;
        break;

      case 'metatag':
        $class = MetaTagEntityFieldGroomer::class;
        break;

      case 'link':
        $class = LinkEntityFieldGroomer::class;
        break;

      case 'entity_reference':
        $class = EntityReferenceEntityFieldGroomer::class;
        break;

      case 'entity_reference_revisions':
        $class = EntityReferenceRevisionsEntityFieldGroomer::class;
        break;

      case 'text_with_summary':
        $class = TextWithSummaryEntityFieldGroomer::class;
        break;

      default:
        $class = EntityFieldGroomer::class;
        break;
    }

    // Obtain groomer.
    $groomer = self::obtainGroomer($signature, $class, $entity_field_data, $groomerManager, GroomerTypes::ENTITY_FIELD);

    // If the parent parameter is not null, we'll set it to the groomer.
    /* @var \Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer\EntityFieldGroomer $groomer */
    if ($entityGroomer !== NULL) {
      $groomer->setEntityGroomer($entityGroomer);
    }
    else {
      $groomer->setEntityGroomer(EntityGroomerFactory::build($entity_field_data->getEntity(), $groomerManager));
    }

    // Return the groomer that is now ready.
    return $groomer;
  }

}
