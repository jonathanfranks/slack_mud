<?php

namespace Drupal\slack_mud\EventSubscriber;

use Drupal\slack_mud\Event\LookAtPlayerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MudEventSubscriber.
 */
class MudEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LookAtPlayerEvent::LOOK_AT_PLAYER_EVENT] = [
      'onLookAtPlayer',
      999,
    ];
    return $events;
  }

  /**
   * Subscriber for the MudEvent LookAtPlayer event.
   *
   * @param \Drupal\slack_mud\Event\LookAtPlayerEvent $event
   *   The LookAtPlayer event.
   */
  public function onLookAtPlayer(LookAtPlayerEvent $event) {
    $targetPlayer = $event->getTargetPlayer();
    $desc = $targetPlayer->body->value;
    $event->setResponse(strip_tags($desc));
  }

}
