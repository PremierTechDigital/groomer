<?php

namespace Drupal\groomer\Groomer;

use Drupal\groomer\Constants\GroomingEvents;
use Drupal\groomer\Event\GroomingEvent;
use Drupal\groomer\Event\GroomingEventFactory;
use Drupal\groomer\Service\GroomerManager;

/**
 * Provides a base Class for all Groomers.
 *
 * @package Drupal\groomer\Groomer
 */
abstract class Groomer implements GroomerInterface {

  /**
   * Groomer signature, defined by the object being groomed.
   *
   * @var string
   */
  protected $signature;

  /**
   * Set the type of groomer this is.
   *
   * @var string
   */
  protected $type;

  /**
   * Data obtained from the groomer after all pre-processing is complete.
   *
   * @var array
   */
  protected $data;

  /**
   * The object being groomed.
   *
   * The type-hint is "mixed" since this could be anything out of Drupal's many
   * objects.
   *
   * @var mixed
   */
  protected $object;

  /**
   * Manager service for groomer functionality.
   *
   * @var \Drupal\groomer\Service\GroomerHelpers
   */
  protected $groomerManager;

  /**
   * Flags that can be set on a groomer to alter pre-processing behavior.
   *
   * For a collection of flags that can be set and their effects, check out the
   * GroomerFlags class.
   *
   * @var array
   *
   * @see \Drupal\groomer\Constants\GroomerFlags
   */
  protected $flags;

  /**
   * Groomer constructor.
   *
   * @param mixed $object
   *   The object being groomed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing Drupal services.
   * @param string $type
   *   The type of groomer defined by a string key.
   * @param string $signature
   *   The signature of the groomer generated by the factory.
   */
  public function __construct($object, GroomerManager $groomerManager, string $type, string $signature) {
    $this->object = $object;
    $this->groomerManager = $groomerManager;
    $this->type = $type;
    $this->signature = $signature;
    $this->flags = [];
  }

  /**
   * Pre-process all fields in this groomer and set it as the cached data.
   *
   * @return array|string|null
   *   The data of the groomer if any.
   */
  public function groom() {

    // Check if cache was set for this groomer.
    if ($this->data !== NULL) {
      return $this->data;
    }

    // Get the groomed data.
    $this->data = $this->getGroomedData();

    // Return null or empty array if data is already empty.
    if ($this->data === NULL || empty($this->data)) {
      return $this->data;
    }

    // Dispatch all Grooming events.
    $this->dispatchGroomingEvents();

    // Return the data.
    return $this->data;

  }

  /**
   * Get the initially groomed data for the given object.
   *
   * This quite simply loops through the objects properties, fields or
   * other data accordingly, and outputs all of the data in clean array
   * format.
   *
   * @return mixed
   *   The data properly formatted from the object.
   */
  abstract protected function getGroomedData();

  /**
   * {@inheritdoc}
   */
  public function getData() {
    return $this->data;
  }

  /**
   * {@inheritdoc}
   */
  public function setData($data) : void {
    $this->data = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getObject() {
    return $this->object;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() : string {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function hasFlag(string $key): bool {
    return \in_array($key, $this->flags, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function setFlag(string $key): void {
    $this->flags[] = $key;
  }

  /**
   * {@inheritdoc}
   */
  public function setFlags(array $flags): void {
    $this->flags = $flags;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignature() : string {
    return $this->signature;
  }

  /**
   * {@inheritdoc}
   */
  public function setSignature(string $signature) : void {
    // @todo - Regex to check the format of the signature to make sure it's good.
    $this->signature = $signature;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignatureParts(bool $incremental_parts = FALSE) {

    // Explode into array of elements, using "." as a delimiter.
    // Example: "entity.node" becomes ['entity', 'node'].
    $parts = explode('.', $this->signature);

    // If the incremental parts flag isn't set, return the exploded array.
    if (!$incremental_parts) {
      return $parts;
    }

    // Otherwise, we'll create the desired incremental format.
    // @see this function's documentation for more information.
    $result = [];

    foreach ($parts as $i => $part) {
      $needle = $part;

      if ($i !== 0) {
        $needle = '';
        for ($c = 0; $c <= $i; $c++) {
          $needle .= $parts[$c];
          if ($c !== $i) {
            $needle .= '.';
          }
        }
      }
      $result[] = $needle;
    }

    // Return the formatted result.
    return $result;
  }

  /**
   * Dispatch events related to this Groomer.
   *
   * This allows other modules to react on Grooming events and alter data.
   */
  private function dispatchGroomingEvents() : void {
    // Dispatch the core event.
    $this->groomerManager->eventDispatcher->dispatch(
      GroomingEvents::CORE, new GroomingEvent($this)
    );

    // Build info on the event to dispatch for this Groomer.
    $event_info = GroomingEventFactory::build($this);

    // Dispatch the Groomer Type specific event.
    $this->groomerManager->eventDispatcher->dispatch(
      $event_info['event_name'], $event_info['event_object']
    );
  }

}
