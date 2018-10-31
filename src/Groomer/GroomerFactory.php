<?php

namespace Drupal\groomer\Groomer;

use Drupal\groomer\Service\GroomerManager;

/**
 * Provides a base class for all Groomer Sub-Factories.
 *
 * @package Drupal\groomer\Groomer
 */
abstract class GroomerFactory {

  /**
   * Storage used to keep groomers that have already been generated.
   *
   * Works wonders for pages that groom the same object multiple times. This
   * will allow groomers to be stored using their signature. If they're called
   * again on the same page, they will be fetched instead of re-instantiated.
   *
   * @var array
   */
  protected static $registry = [];

  /**
   * Create and obtain a groomer using the provided data.
   *
   * This function will instantiate a groomer using the class determined to be
   * the most optimal by the factory. It will then register it in the factory.
   *
   * @param string $signature
   *   Groomer signature generated in the respective factory.
   * @param string $class
   *   Optimal class for grooming determined by the factory.
   * @param mixed $object
   *   Object being groomed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service injected.
   * @param string $type
   *   The type of groomer being created.
   *
   * @return \Drupal\groomer\Groomer\GroomerInterface
   *   Return the groomer that was retrieved/created.
   */
  final public static function obtainGroomer(string $signature, string $class, $object, GroomerManager $groomerManager, string $type) : GroomerInterface {

    // Instantiate the groomer.
    $groomer = new $class($object, $groomerManager, $type, $signature);

    // Register the groomer.
    self::registerGroomer($signature, $groomer);

    // Return the requested groomer.
    return $groomer;
  }

  /**
   * Request a groomer from this factory's registry using the signature.
   *
   * @param string $signature
   *   Signature to search for in the registry.
   *
   * @return \Drupal\groomer\Groomer\GroomerInterface|null
   *   Returns a groomer if it's found in the registry. Otherwise return null.
   */
  final public static function requestGroomer(string $signature) : ?GroomerInterface {
    if (isset(self::$registry[$signature])) {
      return self::$registry[$signature];
    }

    return NULL;
  }

  /**
   * Saves an instantiated groomer in the factory class.
   *
   * This reduces execution load as we will always obtain a groomer that has
   * already been instantiated instead of making duplicates.
   *
   * @param string $signature
   *   Signature key to use as an identity to save this groomer under.
   * @param \Drupal\groomer\Groomer\GroomerInterface $groomer
   *   Groomer class to save in the factory's registry.
   */
  final public static function registerGroomer(string $signature, GroomerInterface $groomer): void {
    self::$registry[$signature] = $groomer;
  }

  /**
   * Create a Groomer Signature.
   *
   * Texts provided will be merged together.
   *
   * Example: If "entity" and "node" are provided, the generated signature will
   * be "entity.node".
   *
   * @param mixed $strings
   *   Texts that should be merged, separated by '.' characters.
   *
   * @return string
   *   The generated signature string.
   */
  final public static function generateGroomerSignature(...$strings) : string {

    // Initialize the string.
    $signature = '';

    // Append all the strings, separated by ".".
    end($strings);
    $last_key = key($strings);
    foreach ($strings as $key => $text) {
      $signature .= $text;
      if ($key !== $last_key) {
        $signature .= '.';
      }
    }

    // Return the signature from the function call.
    return $signature;
  }

}
