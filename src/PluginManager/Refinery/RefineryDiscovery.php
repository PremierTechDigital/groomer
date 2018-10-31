<?php

namespace Drupal\groomer\PluginManager\Refinery;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Component\Annotation\Reflection\MockFileFinder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Reflection\StaticReflectionParser;

/**
 * Custom Discovery class provided for the Refinery.
 *
 * This class extends the AnnotatedClassDiscovery class and takes most of
 * its functionality from it.
 *
 * @package Drupal\groomer\PluginManager\Refinery
 */
final class RefineryDiscovery extends AnnotatedClassDiscovery {

  /**
   * Get all Refiner definitions in the codebase.
   *
   * This function overrides and alters the
   * AnnotatedClassDiscovery::getDefinitions() function.
   *
   * The way definitions are organized has been mildly altered.
   *
   * {@inheritdoc}
   */
  public function getDefinitions() : array {
    $definitions = [];

    $reader = $this->getAnnotationReader();

    // Clear the annotation loaders of any previous annotation classes.
    AnnotationRegistry::reset();

    // Register the namespaces of classes that can be used for annotations.
    AnnotationRegistry::registerLoader('class_exists');

    // Search for classes within all PSR-0 namespace locations.
    foreach ($this->getPluginNamespaces() as $namespace => $dirs) {
      foreach ($dirs as $dir) {
        if (file_exists($dir)) {
          $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
              $dir, \RecursiveDirectoryIterator::SKIP_DOTS
            )
          );
          foreach ($iterator as $fileinfo) {
            if ($fileinfo->getExtension() === 'php') {
              if ($cached = $this->fileCache->get($fileinfo->getPathName())) {
                if (isset($cached['id'])) {
                  $content = unserialize($cached['content']);
                  // Un-serialize this to create a new object instance.
                  $definitions[$content['target']][$cached['id']] = $content;
                }
                continue;
              }

              $sub_path = $iterator->getSubIterator()->getSubPath();
              $sub_path = $sub_path ? str_replace(
                  DIRECTORY_SEPARATOR, '\\', $sub_path
                ) . '\\' : '';
              $class = $namespace . '\\' . $sub_path . $fileinfo->getBasename(
                  '.php'
                );

              // The filename is already known, so there is no need to find the
              // file. However, StaticReflectionParser needs a finder, so use a
              // mock version.
              $finder = MockFileFinder::create($fileinfo->getPathName());
              $parser = new StaticReflectionParser($class, $finder, TRUE);

              /* @var $annotation \Drupal\Component\Annotation\AnnotationInterface */
              if ($annotation = $reader->getClassAnnotation($parser->getReflectionClass(), $this->pluginDefinitionAnnotationName)) {
                $this->prepareAnnotationDefinition($annotation, $class);

                $id = $annotation->getId();
                $content = $annotation->get();
                $target = $annotation->get()['target'] ?? $id;
                $definitions[$target][$id] = $content;
                // Explicitly serialize this to create a new object instance.
                $this->fileCache->set(
                  $fileinfo->getPathName(),
                  ['id' => $id, 'content' => serialize($content)]
                );
              }
              else {
                // Store a NULL object, so the file is not re-parsed again.
                $this->fileCache->set($fileinfo->getPathName(), [NULL]);
              }
            }
          }
        }
      }
    }

    // Don't let annotation loaders pile up.
    AnnotationRegistry::reset();

    return $definitions;
  }

  /**
   * Get definition of a Refiner Plugin.
   *
   * This function overrides and alters the
   * AnnotatedClassDiscovery::getDefinition() function.
   *
   * Since the plugins definitions array is altered in the above function,
   * fetching them must also be altered. Here, we make the necessary changes.
   *
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getRefinerDefinition($plugin_id, $target, $exception_on_invalid = TRUE) : ?array {
    $definitions = $this->getDefinitions();
    return $this->doGetRefinerDefinition(
      $definitions, $target, $plugin_id, $exception_on_invalid
    );
  }

  /**
   * Get definition of a Refiner Plugin.
   *
   * This function overrides and alters the
   * AnnotatedClassDiscovery::doGetDefinition() function.
   *
   * Since the plugins definitions array is altered in the above function,
   * fetching them must also be altered. Here, we make the necessary changes.
   *
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function doGetRefinerDefinition(array $definitions, $target, $plugin_id, $exception_on_invalid) : ?array {
    // Avoid using a ternary that would create a copy of the array.
    if (isset($definitions[$target][$plugin_id])) {
      return $definitions[$target][$plugin_id];
    }

    if (!$exception_on_invalid) {
      return NULL;
    }

    throw new PluginNotFoundException(
      $plugin_id, sprintf('The "%s" plugin does not exist.', $plugin_id)
    );
  }

}
