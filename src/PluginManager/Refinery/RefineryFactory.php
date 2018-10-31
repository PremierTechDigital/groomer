<?php

namespace Drupal\groomer\PluginManager\Refinery;

use Drupal\Core\Plugin\Factory\ContainerFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a custom factory for the Refinery Plugin Manager.
 *
 * This adds further functionality by determining which plugins are
 * created given a Plugin Type.
 *
 * @property \Drupal\groomer\PluginManager\Refinery\RefineryDiscovery $discovery
 *
 * @package Drupal\groomer\PluginManager\Refinery
 */
final class RefineryFactory extends ContainerFactory {

  /**
   * Creates a pre-configured instance of a refiner plugin.
   *
   * @param string $plugin_id
   *   The ID of the plugin being instantiated.
   * @param string $target
   *   The Target Signature of the Groomer this Refiner is attached to.
   * @param array $configuration
   *   An array of configuration relevant to the plugin instance.
   *
   * @return object
   *   A fully configured plugin instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function createRefinerInstance($plugin_id, $target, array $configuration = []) {
    $plugin_definition = $this->discovery->getRefinerDefinition($plugin_id, $target);
    $plugin_class = static::getPluginClass($plugin_id, $plugin_definition, $this->interface);

    // If the plugin provides a factory method, pass the container to it.
    if (is_subclass_of($plugin_class, ContainerFactoryPluginInterface::class)) {
      /* @var \Drupal\Core\Plugin\ContainerFactoryPluginInterface $plugin_class */
      return $plugin_class::create(\Drupal::getContainer(), $configuration, $plugin_id, $plugin_definition);
    }

    // Otherwise, create the plugin directly.
    return new $plugin_class($configuration, $plugin_id, $plugin_definition);
  }

}
