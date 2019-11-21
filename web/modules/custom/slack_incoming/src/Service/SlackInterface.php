<?php

namespace Drupal\slack_incoming\Service;

/**
 * Slack service interface.
 */
interface SlackInterface {

  /**
   * Hits the Slack API with a generic message payload.
   *
   * @param string $service
   *   Service API endpoint.
   * @param string $method
   *   HTTP method to use.
   * @param array $arguments
   *   Array of arguments for the service.
   *
   * @return string
   *   The return.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function slackApi($service, $method, array $arguments);

}
