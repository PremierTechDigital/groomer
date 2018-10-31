<?php

namespace Drupal\groomer\EventSubscriber;

use Drupal\groomer\Event\GroomingEvent;
use Drupal\groomer\Constants\GroomingEvents;
use Drupal\groomer\PluginManager\Refinery\Refinery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to grooming events to apply Plugin Alterations of grooming data.
 *
 * @property Refinery $refinery
 *
 * @package Drupal\custom_events\EventSubscriber
 */
class RefineryEventSubscriber implements EventSubscriberInterface {

  /**
   * Use DI to inject the Refinery Plugin Manager.
   *
   * @var \Drupal\groomer\PluginManager\Refinery\Refinery
   */
  private $refinery;

  /**
   * RefineryEventSubscriber constructor.
   *
   * @param \Drupal\groomer\PluginManager\Refinery\Refinery $refinery
   *   Refinery Plugin Manager injected through DI.
   */
  public function __construct(Refinery $refinery) {
    $this->refinery = $refinery;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() : array {
    return [
      GroomingEvents::CORE => 'onGroom',
    ];
  }

  /**
   * React to a Groomer event.
   *
   * @param \Drupal\groomer\Event\GroomingEvent $event
   *   Event that occurred, containing the groomer that fired the event.
   */
  public function onGroom(GroomingEvent $event) : void {
    $definitions = $this->refinery->getDefinitions();

    /* @see \Drupal\groomer\Groomer\Groomer::getSignatureParts() */
    $signatureParts = $event->groomer->getSignatureParts(TRUE);

    // With each part of the signature, search for Refiners matching the part.
    foreach ($signatureParts as $part) {
      if (isset($definitions[$part])) {
        // The signature part should match the target attribute of the Refiner.
        $refiner_target = $part;

        // Get all plugin definitions with the specified target signature.
        $refiner_definitions = $definitions[$refiner_target];

        // Load all of the plugins found that match, and apply them to data.
        foreach ($refiner_definitions as $plugin_id => $definition) {
          $groomer_data = $event->groomer->getData();
          $refiner = $this->refinery->createRefinerInstance($plugin_id, $refiner_target);
          if ($refiner !== NULL) {
            $refiner->alter($groomer_data, $event->groomer->getObject());
            $event->groomer->setData($groomer_data);
          }
        }
      }
    }
  }

}
