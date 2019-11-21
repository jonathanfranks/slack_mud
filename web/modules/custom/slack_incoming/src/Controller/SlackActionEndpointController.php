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
    /** @var \Drupal\slack_incoming\Event\SlackEvent $slackEvent */
    $slackEvent = new SlackEvent($package);
    $slackEvent = $this->dispatcher->dispatch(SlackEvent::SLACK_EVENT, $slackEvent);
    if ($response = $slackEvent->getResponse()) {
      return $response;
    }
//    if (array_key_exists('type', $package)) {
//      switch ($package['type']) {
//        case 'url_verification':
//          return new JsonResponse(['challenge' => $package['challenge']]);
//
//        case 'event_callback':
//          $event = $package['event'];
//          if ($event['type'] == 'message') {
//            $authedUsers = $package['authed_users'];
//            $userSender = $event['user'];
//            if (!in_array($userSender, $authedUsers)) {
//              // Sender of the message isn't the bot user.
//              // If we don't make this check it'll infinitely loop because when
//              // the bot sends a DM, it triggers the event_callback.
//              $channel = $event['user'];
//              $message = 'Got your message: ' . $event['text'];
//              $this->slack->slackApi('chat.postMessage', 'POST', [
//                'channel' => $channel,
//                'text' => $message,
//                'as_user' => TRUE,
//              ]);
//            }
//            return new Response('', 200);
//          }
//          break;
//      }
//    }
  }

}
