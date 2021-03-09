<?php

namespace Drupal\background_information\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Returns responses for Background Information routes.
 */
class BackgroundInformationController extends ControllerBase {

  /**
   * Builds the response.
   *
   * @param string $api_key
   *   The api key passed in request URL.
   * @param int $nid
   *   The nid passed in request URL.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The json response with node object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getPageJson(string $api_key, int $nid): JsonResponse {
    // Get siteapikey configuration.
    $site_api_key = $this->config('system.site')->get('siteapikey');

    // Return result only if siteapikey & nid exists along
    // with siteapikey is equal to the value provided in the request.
    if (($site_api_key && $nid) && $site_api_key === $api_key) {
      // Now get the node date for page content type based on nid provided.
      $page_node = $this->getPageNodeData($nid);
      if ($page_node) {
        return new JsonResponse(['data' => $page_node, 'status' => 200]);
      }
      // Throw access denied page in case of provided
      // nid does not belongs to page content type.
      throw new AccessDeniedHttpException();
    }

    // Throw access denied page in case of invalid nid or site api key.
    throw new AccessDeniedHttpException();
  }

  /**
   * Get node array of page content type based on provided nid.
   *
   * @param int $nid
   *   The page node id.
   *
   * @return array
   *   The node array of page type.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getPageNodeData(int $nid): array {
    $node = [];
    // Load give node id and check it's a type of page.
    $page_node = $this->entityTypeManager()->getStorage('node')->load($nid);
    if ($page_node && $page_node->bundle() == 'page') {
      // Convert the node object to array so that we can return the
      // response in JSON format.
      $node = $page_node->toArray();
    }
    return $node;
  }

}
