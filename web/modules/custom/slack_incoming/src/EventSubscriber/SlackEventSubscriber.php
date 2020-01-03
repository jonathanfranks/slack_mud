<?php

namespace Drupal\slack_incoming\EventSubscriber;

use Drupal\slack_incoming\Event\SlackEvent;
use Drupal\slack_incoming\Service\SlackInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class TestStateAlter.
 */
class SlackEventSubscriber implements EventSubscriberInterface {

  /**
   * The Slack service.
   *
   * @var \Drupal\slack_incoming\Service\SlackInterface
   */
  protected $slack;

  /**
   * SlackEventSubscriber constructor.
   *
   * @param \Drupal\slack_incoming\Service\SlackInterface $slack
   *   The Slack service.
   */
  public function __construct(SlackInterface $slack) {
    $this->slack = $slack;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[SlackEvent::SLACK_EVENT] = [
      'onSlackEvent',
      800,
    ];
    return $events;
  }

  /**
   * Subscriber for the SlackEvent.
   *
   * @param \Drupal\slack_incoming\Event\SlackEvent $event
   *   The Slack event.
   */
  public function onSlackEvent(SlackEvent $event) {
    // The first event will be a url verification event where we have to send
    // back the challenge token.
    $package = $event->getSlackPackage();
    if (array_key_exists('type', $package)) {
      switch ($package['type']) {
        case 'url_verification':
          $event->setResponse(new JsonResponse(['challenge' => $package['challenge']]));
          break;
        case 'event_callback':
          switch ($package['event']['type']) {
            case 'app_home_opened':
              $homeBlockContent = "{
                \"type\": \"home\",
                \"blocks\": [
                  {
                    \"type\": \"section\",
                    \"text\": {
                      \"type\": \"mrkdwn\",
                      \"text\": \"Hello! Use this cool Slack app to play old text-based adventure games!\n\n\"
                    }
                  }
                ]
              }";

              $this->slack->slackApi('views.publish', 'POST', [
                'user_id' => $package["event"]["user"],
                'view' => $homeBlockContent,
              ]);


              break;
          }
          break;
      }
    }
  }

}
