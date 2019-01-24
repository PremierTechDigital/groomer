<?php

namespace Drupal\groomer\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Theme\ThemeManager;
use Drupal\groomer\Constants\GroomerConfig;
use Drupal\groomer\Groomer\MasterGroomerFactory;

/**
 * Service used to access the Groomer module's main functionality.
 *
 * @package Drupal\groomer\Service
 */
final class GroomerManager {

  /**
   * GroomerHelpers class.
   *
   * Provides helper functions specific to Groomer functionality.
   *
   * @var \Drupal\groomer\Service\GroomerHelpers
   */
  public $helpers;

  /**
   * Drupal's Theme Manager injected through DI.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  public $themeManager;

  /**
   * Drupal's module handler injected through DI.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  public $moduleHandler;

  /**
   * Drupal's event dispatcher injected through DI.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcher
   */
  public $eventDispatcher;

  /**
   * Use DI to inject Drupal's configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  public $configFactory;

  /**
   * Drupal's Admin Context router service injected through DI.
   *
   * @var \Drupal\Core\Routing\AdminContext
   */
  public $adminContext;

  /**
   * Drupal's Current Route Match service injected through DI.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  public $currentRouteMatch;

  /**
   * Groomer cache factory service injected through DI.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  public $cacheBackend;

  /**
   * Global configuration determined on page load.
   *
   * @var array
   */
  public $config;

  /**
   * Provides a service that contains all functionality for the groomer module.
   *
   * This service manages the creation and loading of all groomers.
   *
   * @param \Drupal\groomer\Service\GroomerHelpers $groomerHelpers
   *   Groomer Helpers service injected through DI.
   * @param \Drupal\Core\Theme\ThemeManager $themeManager
   *   Theme Manager service injected through DI.
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   Module Handler service injected through DI.
   * @param \Symfony\Component\EventDispatcher\EventDispatcher|\Drupal\webprofiler\EventDispatcher\TraceableEventDispatcher $eventDispatcher
   *   Event Dispatcher service injected through DI.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Configuration Factory service injected through DI.
   * @param \Drupal\Core\Routing\AdminContext $adminContext
   *   Admin Context service injected through DI.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   *   Current Route Match service injected through DI.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Groomer cache factory service injected through DI.
   */
  public function __construct(
    GroomerHelpers $groomerHelpers,
    ThemeManager $themeManager,
    ModuleHandler $moduleHandler,
    $eventDispatcher,
    ConfigFactory $configFactory,
    AdminContext $adminContext,
    CurrentRouteMatch $currentRouteMatch,
    CacheBackendInterface $cacheBackend
  ) {
    $this->helpers = $groomerHelpers;
    $this->themeManager = $themeManager;
    $this->moduleHandler = $moduleHandler;
    $this->eventDispatcher = $eventDispatcher;
    $this->configFactory = $configFactory;
    $this->adminContext = $adminContext;
    $this->currentRouteMatch = $currentRouteMatch;
    $this->cacheBackend = $cacheBackend;
    $this->prepare();
  }

  /**
   * Grooms an object, providing it's values in a neatly organized array.
   *
   * This function uses the MasterGroomerFactory to build a groomer for the
   * provided object. This Master Factory then calls sub-factories, to determine
   * the best type of Groomer class for the job.
   *
   * @param mixed $object
   *   Object that will be groomed.
   * @param array $flags
   *   Flags to set on the groomers that alter groomer behavior.
   *
   * @return mixed
   *   Result of the grooming process. Returns data if successful.
   */
  public function groom($object, array $flags = []) {

    // Only run grooming if it's activated.
    if (!$this->isPowered()) {
      return [];
    }

    // Fetch/build the groomer for this object.
    /* @var \Drupal\groomer\Groomer\GroomerInterface $groomer */
    $groomer = $this->getGroomer($object);

    // Return nothing if the groomer could not be loaded.
    if ($groomer === NULL) {
      return [];
    }

    // Set some flags if we have any.
    if (!empty($flags)) {
      $groomer->setFlags($flags);
    }

    // Return the data.
    return $groomer->groom();
  }

  /**
   * Grooms an object, providing it's values in a neatly organized array.
   *
   * This checks if the Auto Pre-Process functionality is activated.
   *
   * @param mixed $object
   *   Object that will be groomed.
   * @param array $flags
   *   Flags to set on the groomers that alter groomer behavior.
   *
   * @return mixed
   *   Result of the grooming process. Returns data if successful.
   */
  public function autoGroom($object, array $flags = []) {
    if (isset($this->config[GroomerConfig::AUTO_PRE_PROCESSOR]) && $this->config[GroomerConfig::AUTO_PRE_PROCESSOR] === 1) {
      return $this->groom($object, $flags);
    }
  }

  /**
   * Fetch groomer using the Master Factory.
   *
   * @param mixed $object
   *   Object to fetch a groomer for.
   *
   * @return mixed|null
   *   The created groomer if successful. Returns NULL otherwise.
   */
  public function getGroomer($object) {
    // Build the groomer appropriate groomer for the given object.
    return MasterGroomerFactory::build($object, $this);
  }

  /**
   * Build the Groomer Manager's properties.
   */
  private function prepare() : void {

    // Only do stuff if the groomer is activated.
    if (!$this->isPowered()) {
      return;
    }

    // Set Global Configurations once upon construction.
    $this->config[GroomerConfig::HARMONY] = $this->configFactory->get(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::HARMONY) ?? 0;
    $this->config[GroomerConfig::AUTO_PRE_PROCESSOR] = $this->configFactory->get(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::AUTO_PRE_PROCESSOR) ?? 0;
    $this->config[GroomerConfig::REMOVE_FIELD_UNDERSCORE_PREFIX] = $this->configFactory->get(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::REMOVE_FIELD_UNDERSCORE_PREFIX) ?? 0;
    $this->config[GroomerConfig::MAX_RECURSIVE_DEPTH] = $this->configFactory->get(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::MAX_RECURSIVE_DEPTH) ?? 3;

  }

  /**
   * Determines whether or not the Groomer should run.
   *
   * Certain conditions will disable it completely for certain pages.
   *
   * @return bool
   *   Return boolean stating if the groomer is activated.
   */
  private function isPowered() : bool {

    // If it already ran, return the stored value.
    if (isset($this->config[GroomerConfig::POWERED])) {
      return $this->config[GroomerConfig::POWERED];
    }

    // Initialize the flag variable. By default, the groomer is on.
    $powered = TRUE;

    // Disable grooming in admin context.
    if ($this->adminContext->isAdminRoute($this->currentRouteMatch->getRouteObject())) {
      $powered = FALSE;
    }

    // Set the flag to the Manager's configuration array.
    $this->config[GroomerConfig::POWERED] = $powered;

    return $powered;
  }

}
