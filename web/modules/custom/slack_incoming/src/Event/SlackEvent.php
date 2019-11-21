<?php

namespace Drupal\slack_incoming\Event;

use Drupal\node\NodeInterface;
use stdClass;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the SlackEvent event.
 */
class SlackEvent extends Event {

  const SLACK_EVENT = 'slack_incoming.slack_event';

  /**
   * The event package sent from Slack.
   *
   * @var array
   */
  protected $slackPackage;

  /**
   * Response to send.
   *
   * @var \Symfony\Component\HttpFoundation\Response
   */
  protected $response;

  /**
   * Constructs a new ExpiredTestSessionMessageEvent.
   *
   * @param array $slackPackage
   *   The JSON that comes from the Slack event.
   */
  public function __construct(array $slackPackage) {
    $this->slackPackage = $slackPackage;
  }

  /**
   * Gets the test session.
   *
   * @return array
   *   The Slack event package.
   */
  public function getSlackPackage() {
    return $this->slackPackage;
  }

  /**
   * Get the current response to be sent.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response.
   */
  public function getResponse(): Response {
    return $this->response;
  }

  /**
   * Set a new response to be sent.
   *
   * @param \Symfony\Component\HttpFoundation\Response $response
   *   The response.
   */
  public function setResponse(Response $response) {
    $this->response = $response;
  }

}
