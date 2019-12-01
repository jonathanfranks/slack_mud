<?php

namespace Drupal\slack_mud\EventSubscriber;

use Drupal\node\Entity\Node;
use Drupal\slack_incoming\Event\SlackEvent;
use Drupal\slack_incoming\Service\SlackInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SlackEventSubscriber.
 */
class SlackEventSubscriber implements EventSubscriberInterface {

  /**
   * Array of valid direction commands.
   *
   * @var array
   */
  protected $directions = [
    'up',
    'down',
    'north',
    'south',
    'east',
    'west',
    'northwest',
    'southwest',
    'northeast',
    'southeast',
  ];

  /**
   * The Slack service.
   *
   * @var \Drupal\slack_incoming\Service\SlackInterface
   */
  protected $slack;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * SlackEventSubscriber constructor.
   *
   * @param \Drupal\slack_incoming\Service\SlackInterface $slack
   *   The Slack service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(SlackInterface $slack, EventDispatcherInterface $event_dispatcher) {
    $this->slack = $slack;
    $this->eventDispatcher = $event_dispatcher;
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
              // There are some aliases/shortcuts, so we'll translate those.
              $messageAliases = [
                'n' => 'north',
                's' => 'south',
                'w' => 'west',
                'e' => 'east',
                'ne' => 'northeast',
                'se' => 'southeast',
                'nw' => 'northwest',
                'sw' => 'southwest',
                'inv' => 'inventory',
                'take' => 'get',
              ];
              if (array_key_exists($messageText, $messageAliases)) {
                $messageText = $messageAliases[$messageText];
              }

              $actingPlayer = $this->currentPlayer($eventCallback['user']);

              if (in_array($messageText, $this->directions)) {
                $messageText = 'move ' . $messageText;
              }
              else {
                // Command handler.
                $mudEvent = new CommandEvent($actingPlayer, $messageText);
                $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
                $response = $mudEvent->getResponse();
                if (!$response) {
                  // If the command event didn't return any response, then
                  // you can't do that here.
                  $response = t("You can't do that here.");
                }
                $channel = $eventCallback['user'];
                $this->slack->slackApi('chat.postMessage', 'POST', [
                  'channel' => $channel,
                  'text' => strip_tags($response),
                  'as_user' => TRUE,
                ]);
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
   * @param string $slackUserName
   *   The player's Slack user name.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\node\Entity\Node|null
   *   The player nodes.
   */
  protected function currentPlayer($slackUserName) {
    $player = NULL;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'player')
      ->condition('field_slack_user_name', $slackUserName)
      ->condition('field_active', TRUE);
    $playerNids = $query->execute();
    if ($playerNids) {
      $playerNid = reset($playerNids);
      $player = Node::load($playerNid);
    }
    return $player;
  }

}
