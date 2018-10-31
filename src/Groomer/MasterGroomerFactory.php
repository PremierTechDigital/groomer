<?php

namespace Drupal\groomer\Groomer;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\groomer\Groomer\EntityGroomer\EntityFieldGroomer\EntityFieldGroomerFactory;
use Drupal\groomer\Groomer\EntityGroomer\EntityGroomerFactory;
use Drupal\groomer\Groomer\FormGroomer\FormGroomerFactory;
use Drupal\groomer\Groomer\MenuGroomer\MenuGroomerFactory;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\groomer\Service\GroomerManager;

/**
 * Handles the creation of the proper groomer for a given Drupal object.
 *
 * There are many types of objects in Drupal. Each
 * object may have their own specific arrangements that will affect the way the
 * data is pre-processed. We will handle these in respective classes.
 *
 * By checking the object type, the Factory builds the appropriate Groomer.
 *
 * @package Drupal\groomer\Groomer
 */
final class MasterGroomerFactory {

  /**
   * Build the appropriate Groomer with the given object.
   *
   * This Master Factory calls appropriate sub-factories.
   *
   * @param mixed $object
   *   Drupal Object that is currently being processed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing many Drupal services.
   *
   * @return mixed|null
   *   Returns the proper Groomer. If it fails, returns null.
   */
  public static function build($object, GroomerManager $groomerManager) {

    // If for some reason we try to groom NULL, let's get out of here.
    if ($object === NULL) {
      // Throw status message saying tried to groom a null object.
      return NULL;
    }

    // First, check if the object is an Entity.
    if (\is_object($object) && \in_array(ContentEntityInterface::class, class_implements($object), TRUE)) {
      return EntityGroomerFactory::build($object, $groomerManager);
    }

    // Next, we'll check if it's a field.
    if (\is_object($object) && \in_array(FieldItemListInterface::class, class_implements($object), TRUE)) {
      return EntityFieldGroomerFactory::build($object, $groomerManager);
    }

    // Check if the object is a render array.
    if (\is_array($object)) {
      // Check if the object is a form render array.
      if (isset($object['#type']) && $object['#type'] === 'form') {
        return FormGroomerFactory::build($object, $groomerManager);
      }
      // Check if this object is a menu render array.
      if (isset($object['menu_name']) && !empty($object['items'])) {
        return MenuGroomerFactory::build($object, $groomerManager);
      }
    }

    // Throw a Drupal log error if no Groomer was found for the given object.
    // Throw a status message too!
    // @todo - USE DI!
    \Drupal::logger('groomer')->notice(
      t(
        'Error: Unhandled Groomer. An object was trying to be pre-processed, but no Groomer class was found. Please check the backend code for details.'
      )
    );

    return NULL;
  }

}
