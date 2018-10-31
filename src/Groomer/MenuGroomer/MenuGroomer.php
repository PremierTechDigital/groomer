<?php

namespace Drupal\groomer\Groomer\MenuGroomer;

use Drupal\groomer\Constants\GroomerConfig;
use Drupal\groomer\Groomer\Groomer;
use Drupal\Component\Utility\Html;
use Drupal\groomer\Constants\GroomerTypes;
use Drupal\groomer\Service\GroomerManager;

/**
 * Provides a Groomer for Drupal menus.
 *
 * @package Drupal\groomer\Groomer\MenuGroomer
 */
class MenuGroomer extends Groomer {

  /**
   * Contains the name of the Drupal menu being groomed.
   *
   * @var string
   */
  public $menuName;

  /**
   * Contains the list of menu items that need to be groomed.
   *
   * @var array
   */
  protected $items;

  /**
   * MenuGroomer constructor.
   *
   * @param array $variables
   *   The render array of the menu object being groomed.
   * @param \Drupal\groomer\Service\GroomerManager $groomerManager
   *   Groomer Manager service containing Drupal services.
   */
  public function __construct(array $variables, GroomerManager $groomerManager) {
    parent::__construct($variables, $groomerManager);

    $this->type = GroomerTypes::MENU;
    $this->menuName = $variables['menu_name'];
    $this->items = $variables['items'];

    $this->sign($this->type, $this->menuName);

  }

  /**
   * Get the name of the Menu being groomed.
   *
   * @return string
   *   Return the menu name of the current groomer.
   */
  public function getMenuName() : string {
    return $this->menuName;
  }

  /**
   * {@inheritdoc}
   */
  protected function getGroomedData() {
    // Checks if this menu is configured to be groomed or not.
    // If not, we'll return without grooming anything.
    $config = \Drupal::config(GroomerConfig::CONFIG_NAME)->get(GroomerConfig::MENU_SETTINGS);

    if ($config[$this->menuName] === 0) {
      return $this->object;
    }

    // Array that will house all groomed menu items.
    $links = [];

    foreach ($this->items as $id => $item) {
      $links[] = $this->groomMenuItem($item);
    }

    return $links;
  }

  /**
   * Format a single menu item and output the data cleanly.
   *
   * @param array $item
   *   Render array of the menu item being groomed.
   *
   * @return array
   *   Data of the menu item in a clean format.
   */
  private function groomMenuItem(array $item) : array {
    $attributes = $item['url']->getOptions()['attributes'] ?? [];
    $attributes['href'] = $item['url']->toString();
    $plugin_id = $item['original_link']->getPluginId();
    $entity_id = $item['original_link']->getRouteParameters()['node'] ?? '';
    $groomedItem = [
      'active'     => $item['in_active_trail'] ?? FALSE,
      'label'      => $item['title'],
      'menuId'     => 'menu-' . Html::cleanCssIdentifier(strtolower($item['title'])),
      'pluginId'   => $plugin_id,
      'node'       => $entity_id,
      'attributes' => $attributes,
      'subnav'     => [],
    ];

    if ($item['url']->isExternal()) {
      $groomedItem['icon'] = 'dmt-icon-external';
    }

    if (isset($item['below']) && !empty($item['below'])) {
      foreach ($item['below'] as $subItem) {
        $groomedItem['subnav'][] = $this->groomMenuItem($subItem);
      }
    }

    return $groomedItem;
  }

}