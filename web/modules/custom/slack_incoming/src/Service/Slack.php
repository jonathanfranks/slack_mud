<?php

namespace Drupal\slack_incoming\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;

/**
 * Send messages to Slack.
 */
class Slack implements SlackInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $config;

  /**
   * The client interface.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $httpClient;

  /**
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $logger;

  /**
   * Constructs a Slack object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   Guzzle.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger.
   */
  public function __construct(ConfigFactoryInterface $config, ClientInterface $http_client, LoggerChannelFactoryInterface $logger) {
    $this->config = $config;
    $this->httpClient = $http_client;
    $this->logger = $logger;
  }

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
  public function slackApi($service, $method, array $arguments) {
    $config = $this->config->get('slack_incoming.slackapplicationconfig');
    $slackApiBaseUrl = $config->get('base_slack_api_url');
    $url = $slackApiBaseUrl . $service;
    $token = $config->get('bot_user_oauth_access_token');
    $arguments['token'] = $token;

    $request = $this->httpClient->request($method, $url, [
      'form_params' => $arguments,
    ]);
    $code = $request->getStatusCode();
    $response = $request->getBody()->getContents();

    \Drupal::logger('slack_incoming')
      ->debug('api call to %service:<br/>%call <br/><br/><br/> response: %content',
        [
          '%service' => $service,
          '%call' => json_encode($arguments),
          '%content' => $response,
        ]
      );

    return $response;
  }

}
