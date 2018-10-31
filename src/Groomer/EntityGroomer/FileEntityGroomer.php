<?php

namespace Drupal\groomer\Groomer\EntityGroomer;

/**
 * Handles groomer exceptions for File entities.
 *
 * @property \Drupal\file\Entity\File $entity
 *
 * @package Drupal\groomer\Groomer\EntityGroomer
 */
final class FileEntityGroomer extends EntityGroomer {

  /**
   * {@inheritdoc}
   */
  protected function getGroomedData() {
    // Return only the relevant information.
    return [
      'uri'      => $this->entity->getFileUri(),
      'filename' => $this->entity->getFilename(),
      'filesize' => $this->formatFilesize($this->entity->getSize()),
      'href'     => file_create_url($this->entity->getFileUri()),
    ];
  }

  /**
   * Unique function to FileGroomer. Formats the filesize accordingly.
   *
   * @param int $size
   *   Filesize provided in bytes.
   *
   * @return string
   *   Formatted filesize in text readable.
   *
   * @todo - Move this function to another module or to a services module.
   */
  public function formatFilesize(int $size) : string {

    // Format the filesize given the bytes returned.
    if ($size >= 1073741824) {
      $size /= 1073741824;
      $unit = 'GB';
    }
    elseif ($size >= 1048576) {
      $size /= 1048576;
      $unit = 'MB';
    }
    elseif ($size >= 1024) {
      $size /= 1024;
      $unit = 'KB';
    }
    else {
      $unit = 'B';
    }

    // Format the number.
    $size = $this->numberFormat($size);

    return $size . ' ' . $unit;

  }

  /**
   * Format numbers considering the current language.
   *
   * Numbers have different formats per language.
   *
   * @param mixed $number
   *   Provided number in text format.
   *
   * @return string
   *   Formatted number.
   *
   * @todo - Move this function to another module or to a services module.
   */
  public function numberFormat($number) : string {
    // Get the current language.
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // Defaults.
    // @todo - Change these...I guess?
    $format_decimals = 2;
    $format_dec_point = '.';
    $format_thousands_sep = ',';

    // Change format given the language.
    switch ($lang) {

      case 'en':
        $format_decimals = 2;
        $format_dec_point = '.';
        $format_thousands_sep = ',';
        break;

      case 'fr':
        $format_decimals = 3;
        $format_dec_point = ',';
        $format_thousands_sep = ' ';
        break;

      default:
        // Do nothing.
        break;

    }

    return number_format(
      $number, $format_decimals, $format_dec_point, $format_thousands_sep
    );
  }

}
