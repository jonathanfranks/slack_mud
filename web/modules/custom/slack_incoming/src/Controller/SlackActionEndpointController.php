<?php

namespace Drupal\slack_incoming\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
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
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Slack service.
   *
   * @var \Drupal\slack_incoming\Service\SlackInterface
   */
  protected $slack;

  /**
   * Event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('slack_incoming.slack_service'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * SlackActionEndpointController constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\slack_incoming\Service\SlackInterface $slack
   *   Slack service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   */
  public function __construct(ConfigFactoryInterface $configFactory, SlackInterface $slack, EventDispatcherInterface $dispatcher) {
    $this->config = $configFactory;
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


    $decodedContent = urldecode($rawContent);
    parse_str($decodedContent, $incoming);
    if (array_key_exists('payload', $incoming)) {
      $package = json_decode($incoming['payload'], TRUE);
    }
    else {
      //    $package = json_decode($rawContent, TRUE);
      $package = json_decode(urldecode($rawContent), TRUE);
    }


    /** @var \Drupal\slack_incoming\Event\SlackEvent $slackEvent */
    $slackEvent = new SlackEvent($package);
    $slackEvent = $this->dispatcher->dispatch(SlackEvent::SLACK_EVENT, $slackEvent);
    if ($response = $slackEvent->getResponse()) {
      return $response;
    }
  }

}
