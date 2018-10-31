<?php

namespace Drupal\groomer\Groomer;

/**
 * Provides an interface for Groomers.
 *
 * @package Drupal\groomer\Groomer
 */
interface GroomerInterface {

  /**
   * Pre-process data.
   *
   * @return mixed
   *   Return the groomer's pre-processed data.
   */
  public function groom();

  /**
   * Get Data field of the groomer.
   *
   * @return mixed
   *   Return the groomer's data.
   */
  public function getData();

  /**
   * Set Data field of the groomer.
   *
   * @param mixed $data
   *   Set the groomer's data to a given value.
   */
  public function setData($data): void;

  /**
   * Get the object property of the current groomer.
   *
   * @return mixed
   *   Return the object of the current groomer.
   */
  public function getObject();

  /**
   * Get the type of the groomer.
   *
   * @return string
   *   Return the type of groomer.
   */
  public function getType(): string;

  /**
   * Check if the current groomer has a flag set.
   *
   * Flags can be used to customize the actions of a groomer.
   *
   * @param string $key
   *   Key of the flag to look for.
   *
   * @return bool
   *   Returns true if the flag is planted. Returns false otherwise.
   */
  public function hasFlag(string $key): bool;

  /**
   * Set a flag to the current groomer.
   *
   * Flags can be used to customize the actions of a groomer.
   *
   * @param string $key
   *   Key of the flag to be set.
   */
  public function setFlag(string $key): void;

  /**
   * Set a flag to the current groomer.
   *
   * Flags can be used to customize the actions of a groomer.
   *
   * @param array $flags
   *   Array of flags to set on this groomer.
   */
  public function setFlags(array $flags): void;

  /**
   * Retrieve signature from this Groomer.
   *
   * @return string
   *   Signature in string format.
   */
  public function getSignature() : string;

  /**
   * Set signature for this groomer.
   *
   * @param string $signature
   *   String to set the signature to.
   */
  public function setSignature(string $signature): void;

  /**
   * Get the signature property of the current groomer.
   *
   * If incremental parts are requested, an array will be returned containing
   * each part of the groomer's signature incrementally appended to each other.
   *
   * Example: A signature signed "entity.node.basic_page" will return an array
   * with the following values:
   * ['entity', 'entity.node', 'entity.node.basic_page'].
   *
   * The incremental signature array form is used in many of the module's
   * features.
   *
   * @param bool $incremental_parts
   *   Determines whether to get the signature in incremental parts. This will
   *   return the signature in an array instead of a string, and it will be
   *   split in the proper parts according to the rule above.
   *
   * @return mixed
   *   Return the signature of the current groomer.
   *   Return array if parts are requested.
   */
  public function getSignatureParts(bool $incremental_parts = FALSE);

}
