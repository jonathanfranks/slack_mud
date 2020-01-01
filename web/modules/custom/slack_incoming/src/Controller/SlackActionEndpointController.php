<?php

namespace Drupal\slack_incoming\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\slack_incoming\Event\SlackEvent;
use Drupal\slack_incoming\Service\SlackInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SlackActionEndpointController.
 */
class SlackActionEndpointController extends ControllerBase {

  /**
   * Slack service.
   *
   * @var \Drupal\slack_incoming\Service\SlackInterface
   */
  protected $slack;

  protected $dispatcher;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('slack_incoming.slack_service'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * SlackActionEndpointController constructor.
   *
   * @param \Drupal\slack_incoming\Service\SlackInterface $slack
   *   Slack service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   */
  public function __construct(SlackInterface $slack, EventDispatcherInterface $dispatcher) {
    $this->slack = $slack;
    $this->dispatcher = $dispatcher;
  }

  /**
   * Empty return.
   *
   * @return mixed
   *   Response.
   */
  public function action(Request $request) {
    $this->slack = \Drupal::getContainer()->get('slack_incoming.slack_service');
    $rawContent = $request->getContent();

    \Drupal::logger('slack_incoming')
      ->debug('request content: %content',
        [
          '%content' => $rawContent,
        ]
      );

    $package = json_decode($rawContent, TRUE);

    $signingSecret = 'c5150acb88aea3f60e09c0afc7e4a727';
    if (empty($_SERVER['HTTP_X_SLACK_SIGNATURE']) || empty($_SERVER['HTTP_X_SLACK_REQUEST_TIMESTAMP'])) {
      header('HTTP/1.1 400 Bad Request', TRUE, 400);
      exit;
    }
    else {
      $version = explode("=", $_SERVER['HTTP_X_SLACK_SIGNATURE']);
      $timestamp = $_SERVER['HTTP_X_SLACK_REQUEST_TIMESTAMP'];
      if (abs(time() - $timestamp) > 60 * 5) {
        // Repeat request? More than 5 minutes old. Ignore it.
        header('HTTP/1.1 400 Bad Request', TRUE, 400);
        exit;
      }
      $sig_basestring = "{$version[0]}:$timestamp:$rawContent";
      $hash_signature = hash_hmac('sha256', $sig_basestring, $signingSecret);
      if (!hash_equals($_SERVER['HTTP_X_SLACK_SIGNATURE'], "v0=$hash_signature")) {
        header('HTTP/1.1 400 Bad Request', TRUE, 400);
        exit;
      }
    }


    /** @var \Drupal\slack_incoming\Event\SlackEvent $slackEvent */
    $slackEvent = new SlackEvent($package);
    $slackEvent = $this->dispatcher->dispatch(SlackEvent::SLACK_EVENT, $slackEvent);
    if ($response = $slackEvent->getResponse()) {
      return $response;
    }
  }

}
