<?php

namespace Drupal\slack_mud\EventSubscriber;

use Drupal\node\Entity\Node;
use Drupal\slack_incoming\Event\SlackEvent;
use Drupal\slack_incoming\Service\SlackInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

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
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function onSlackEvent(SlackEvent $event) {
    // Let's respond to a specific message command.
    $package = $event->getSlackPackage();
    if (array_key_exists('type', $package)) {
      switch ($package['type']) {
        case 'event_callback':
          $eventCallback = $package['event'];
          if ($eventCallback['type'] == 'message') {
            // Sender of the message isn't the bot user.
            // If we don't make this check it'll infinitely loop because when
            // the bot sends a DM, it triggers the event_callback.
            $authedUsers = $package['authed_users'];
            $userSender = $eventCallback['user'];
            if (!in_array($userSender, $authedUsers)) {
              $messageText = strtolower(trim($eventCallback['text']));

              if ($messageText == 'inv') {
                $this->inventory($eventCallback);
              }
              elseif ($messageText == 'look') {
                $this->look($eventCallback);
              }

            }
            $event->setResponse(new Response('', 200));
          }
          break;
      }
    }
  }

  /**
   * Returns the current player node.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\node\Entity\Node|null
   *   The player node or NULL if there isn't one.
   */
  protected function currentPlayer() {
    $player = NULL;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'player')
      ->condition('field_active', TRUE);
    $playerNids = $query->execute();
    if ($playerNids) {
      $playerNid = reset($playerNids);
      $player = Node::load($playerNid);
    }
    return $player;
  }

  /**
   * Gets a player's current inventory.
   *
   * @param array $eventCallback
   *   The event info from Slack.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function inventory(array $eventCallback) {
    $message = 'You have ';
    $inv = [];
    $player = $this->currentPlayer();
    foreach ($player->field_inventory as $itemNid => $item) {
      $inv[] = $item->entity->getTitle();
    }
    $message .= implode(', ', $inv);
    $channel = $eventCallback['user'];
    $this->slack->slackApi('chat.postMessage', 'POST', [
      'channel' => $channel,
      'text' => $message,
      'as_user' => TRUE,
    ]);
  }

  /**
   * Gets a player's current inventory.
   *
   * @param array $eventCallback
   *   The event info from Slack.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function look(array $eventCallback) {
    $inv = [];
    $player = $this->currentPlayer();
    $loc = $player->field_location->entity;
    $message = $loc->body->value;
    $channel = $eventCallback['user'];
    $this->slack->slackApi('chat.postMessage', 'POST', [
      'channel' => $channel,
      'text' => $message,
      'as_user' => TRUE,
    ]);
  }

}
