<?php

namespace Drupal\teardrop_rest\Plugin\rest\resource\Entity;

use Drupal\groomer_rest\Plugin\rest\resource\GroomerRestResourceBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a Entity Resource.
 *
 * @RestResource(
 *   id = "groomer_single_entity_resource",
 *   label = @Translation("Groomer Rest API - Single Entity Resource"),
 *   uri_paths = {
 *     "canonical" = "api/teardrop/v1/entity/{entity_type}/{id}"
 *   }
 * )
 */
class SingleEntityResource extends GroomerRestResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @param string $entity_type_id
   *   String representing the type of entity being requested.
   * @param int $entity_id
   *   ID of the entity being requested.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Result of the API call.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function get($entity_type_id, $entity_id) : JsonResponse {
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $entity = $this->entityTypeManager->getStorage($entity_type_id)->load(
      $entity_id
    );
    $entity_data = $this->groomerManager->groom($entity);

    $response = new JsonResponse($entity_data);
    return $response;
  }

}
