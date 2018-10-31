<?php

namespace Drupal\groomer\PluginManager\Refinery;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\groomer\PluginManager\Refinery\Annotation\Refiner as RefinerAnnotation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Plugin Manager for Refiners.
 *
 * @package Drupal\groomer\PluginManager
 */
final class Refinery extends DefaultPluginManager implements ContainerInjectionInterface {

  /**
   * Drupal's Messenger service, injected through DI.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  private $messenger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) : Refinery {
    return new static(
      $container->get('container.namespaces'),
      $container->get('cache.discovery'),
      $container->get('module_handler'),
      $container->get('messenger')
    );
  }

  /**
   * Constructs an RefinerPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   Drupal's Messenger service, injected through DI.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
    MessengerInterface $messenger
  ) {
    parent::__construct(
      'Plugin/Groomer/Refiner',
      $namespaces,
      $module_handler,
      RefinerInterface::class,
      RefinerAnnotation::class
    );

    $this->alterInfo('groomer_refiners_info');
    $this->setCacheBackend($cache_backend, 'groomer_refiners');
    $this->messenger = $messenger;
  }

  /**
   * Override Discovery and use the custom one made for the Refinery.
   *
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!$this->discovery) {
      $discovery = new RefineryDiscovery(
        $this->subdir,
        $this->namespaces,
        $this->pluginDefinitionAnnotationName,
        $this->additionalAnnotationNamespaces
      );
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($discovery);
    }
    return $this->discovery;
  }

  /**
   * Override Factory and use the custom one made for the Refinery.
   *
   * {@inheritdoc}
   */
  protected function getFactory() {
    if (!$this->factory) {
      $this->factory = new RefineryFactory($this->getDiscovery(), $this->pluginInterface);
    }
    return $this->factory;
  }

  /**
   * {@inheritdoc}
   */
  public function createRefinerInstance($plugin_id, $plugin_target, array $configuration = []) {
    try {
      return $this->getFactory()->createRefinerInstance($plugin_id, $plugin_target, $configuration);
    }
    catch (PluginNotFoundException $e) {
      $this->handlePluginNotFoundException($plugin_id, $e);
    }
    catch (PluginException $e) {
      $this->handlePluginException($plugin_id, $e);
    }
  }

  /**
   * Handle PluginException errors thrown by the factory.
   *
   * @param string $plugin_id
   *   ID of the plugin that threw the error.
   * @param \Drupal\Component\Plugin\Exception\PluginException $exception
   *   Exception thrown.
   */
  public function handlePluginException(string $plugin_id, PluginException $exception) : void {
    $this->messenger->addMessage($exception->getMessage());
  }

  /**
   * Handle PluginNotFoundException errors thrown by the factory.
   *
   * @param string $plugin_id
   *   ID of the plugin that threw the error.
   * @param \Drupal\Component\Plugin\Exception\PluginException $exception
   *   Exception thrown.
   */
  public function handlePluginNotFoundException(string $plugin_id, PluginException $exception) : void {
    $this->messenger->addMessage($exception->getMessage());
  }

}
