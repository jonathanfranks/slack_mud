<?php

namespace Drupal\slack_incoming\EventSubscriber;

use Drupal\slack_incoming\Event\SlackEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class TestStateAlter.
 */
class SlackEventSubscriber implements EventSubscriberInterface {

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
      }
    }
  }

}
