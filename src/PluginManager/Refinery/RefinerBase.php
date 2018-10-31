<?php

namespace Drupal\groomer\PluginManager\Refinery;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\groomer\Service\GroomerManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for Refiner plugins.
 *
 * @package Drupal\groomer\Plugin
 */
abstract class RefinerBase extends PluginBase implements RefinerInterface, ContainerFactoryPluginInterface {

  /**
   * Groomer Manager injected through DI.
   *
   * @var \Drupal\groomer\Service\GroomerManager
   */
  protected $groomerManager;

  /**
   * Dependency injection create method override.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Dependency Injection container.
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('groomer.manager')
    );
  }

  /**
   * The Refiner base constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   The groomer manager, injected through DI.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    GroomerManager $groomerManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->groomerManager = $groomerManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getId(): string {
    return $this->pluginDefinition['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function getTarget(): string {
    return $this->pluginDefinition['target'];
  }

  /**
   * {@inheritdoc}
   */
  abstract public function alter(&$data, $original_object);

}
