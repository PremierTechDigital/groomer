<?php

namespace Drupal\groomer\EventSubscriber;

use Drupal\Core\Theme\ThemeManager;
use Drupal\groomer\Event\GroomingEvent;
use Drupal\groomer\Constants\GroomingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to grooming events to apply Harmonize Strategy of grooming data.
 *
 * @package Drupal\custom_events\EventSubscriber
 */
class HarmonyEventSubscriber implements EventSubscriberInterface {

  /**
   * Define the path that will be crawled in the theme for Handshake files.
   *
   * This is the path relative to the root of the Drupal theme.
   *
   * @var string
   */
  public const ROOT = '/harmony';

  /**
   * Define the path that will be crawled in the theme for Handshake files.
   *
   * This is the path relative to the root of the Drupal theme.
   *
   * @var string
   */
  public const FILE_EXTENSION = '.harmony.php';

  /**
   * Houses the path to the handshake directory for the current theme.
   *
   * @var string
   */
  private $directory;

  /**
   * HarmonyEventSubscriber constructor.
   *
   * @param \Drupal\Core\Theme\ThemeManager $themeManager
   *   Theme Manager injected through DI.
   */
  public function __construct(ThemeManager $themeManager) {
    $this->directory = drupal_get_path('theme', $themeManager->getActiveTheme()->getName()) . self::ROOT;
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
    $data = [];
    $groomer_data = $event->groomer->getData();

    /* @see \Drupal\groomer\Groomer\Groomer::getSignatureParts() */
    $signatureParts = $event->groomer->getSignatureParts(TRUE);

    // With each part of the signature, include files matching the part.
    foreach ($signatureParts as $part) {
      $this->includeAll(
        $this->directory, $part, $groomer_data, $data
      );
    }

    // If the data is empty, that means it wasn't reformatted.
    // We'll return the default preprocessor data.
    if (!empty($data)) {
      $event->groomer->setData($data);
    }
    else {
      $event->groomer->setData($groomer_data);
    }
  }

  /**
   * Scan the api path, recursively including all PHP files.
   *
   * @param string $dir
   *   Directory to look through to include files.
   * @param string $filename
   *   Filename to look for recursively.
   * @param mixed $groomer_data
   *   Data from the groomer, already processed by default.
   * @param mixed $data
   *   Data of the groomer that will be released to templates.
   */
  private function includeAll(string $dir, string $filename, &$groomer_data, &$data) : void {
    $escaped_filename = str_replace('.', '\.', $filename);
    $escaped_file_extension = str_replace(
      '.', '\.', self::FILE_EXTENSION
    );

    $scan = glob("{$dir}/*");
    foreach ($scan as $path) {
      $test = '/\/' . $escaped_filename . $escaped_file_extension . '$/';
      if (preg_match($test, $path)) {
        include $path;
      }
      elseif (is_dir($path)) {
        $this->includeAll($path, $filename, $groomer_data, $data);
      }
    }
  }

}
