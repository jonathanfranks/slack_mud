<?php

namespace Drupal\slack_mud\EventSubscriber;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_incoming\Event\SlackEvent;
use Drupal\slack_incoming\Service\SlackInterface;
use Drupal\slack_mud\Event\LookAtPlayerEvent;
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

              $player = $this->currentPlayer($eventCallback['user']);

              if ($messageText == 'inventory') {
                $this->inventory($eventCallback, $player);
              }
              elseif ($messageText == 'look') {
                $this->lookLocation($eventCallback, $player);
              }
              elseif (strpos($messageText, 'look ') !== FALSE) {
                $this->lookTarget($messageText, $player, $eventCallback);
              }
              elseif (strpos($messageText, 'get ') !== FALSE) {
                $this->getHandler($messageText, $player, $eventCallback);
              }
              elseif (strpos($messageText, 'drop ') !== FALSE) {
                $this->dropHandler($messageText, $player, $eventCallback);
              }
              elseif (in_array($messageText, $this->directions)) {
                $this->moveHandler($messageText, $player, $eventCallback);
              }
              else {
                // Command handler.
                // If the command was a command, you can't do that here.
                $message = "You can't do that here.";
                $channel = $eventCallback['user'];
                $this->slack->slackApi('chat.postMessage', 'POST', [
                  'channel' => $channel,
                  'text' => strip_tags($message),
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
   * Returns other player nodes who are in the same location.
   *
   * @param string $slackUserName
   *   The current player's Slack username.
   * @param \Drupal\node\NodeInterface $location
   *   The location where the user is.
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]
   *   An array of players who are also in the same location.
   */
  protected function otherPlayersInLocation($slackUserName, NodeInterface $location) {
    $players = [];
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'player')
      ->condition('field_slack_user_name', $slackUserName, '<>')
      ->condition('field_location.target_id', $location->id())
      ->condition('field_active', TRUE);
    $playerNids = $query->execute();
    if ($playerNids) {
      $players = Node::loadMultiple($playerNids);
    }
    return $players;
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

  /**
   * Gets a player's current inventory.
   *
   * @param array $eventCallback
   *   The event info from Slack.
   * @param \Drupal\node\NodeInterface $player
   *   The current player node.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function inventory(array $eventCallback, NodeInterface $player) {
    $message = 'You have ';
    $inv = [];
    foreach ($player->field_inventory as $itemNid => $item) {
      $inv[] = $item->entity->getTitle();
    }
    $message .= implode(', ', $inv);
    $channel = $eventCallback['user'];
    $this->slack->slackApi('chat.postMessage', 'POST', [
      'channel' => $channel,
      'text' => strip_tags($message),
      'as_user' => TRUE,
    ]);
  }

  /**
   * Gets a player's current inventory.
   *
   * @param array $eventCallback
   *   The event info from Slack.
   * @param \Drupal\node\NodeInterface $player
   *   The current player node.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function lookLocation(array $eventCallback, NodeInterface $player) {
    $inv = [];
    $loc = $player->field_location->entity;
    $slackUsername = $player->field_slack_user_name->value;
    $message = $loc->body->value;

    $otherPlayers = $this->otherPlayersInLocation($slackUsername, $loc);
    if ($otherPlayers) {
      $here = '';
      $playerNames = [];
      if (count($otherPlayers) == 1) {
        $here = ' is here.';
      }
      else {
        $here = ' are here.';
      }
      foreach ($otherPlayers as $otherPlayer) {
        $playerNames[] = $otherPlayer->field_display_name->value;
      }
      $otherPlayersMessage = implode(' and ', $playerNames) . $here;
      $message .= "\n" . $otherPlayersMessage;
    }

    $visible_items = [];
    foreach ($loc->field_visible_items as $visible_item) {
      if ($visible_item->entity->field_visible->value) {
        $visible_items[] = $visible_item->entity->getTitle();
      }
    }
    $where = $loc->field_object_location->value;
    switch (count($visible_items)) {
      case 1:
        $here = t(' is :where.', [':where' => $where]);
        break;

      case 0:
        $here = t('There is nothing :where.', [':where' => $where]);
        break;

      default:
        $here = t(' are :where.', [':where' => $where]);
    }
    $visible_items_message = implode(' and ', $visible_items) . $here;
    $message .= "\n" . $visible_items_message;

    $channel = $eventCallback['user'];
    $this->slack->slackApi('chat.postMessage', 'POST', [
      'channel' => $channel,
      'text' => strip_tags($message),
      'as_user' => TRUE,
    ]);
  }

  /**
   * Handler for looking at a target.
   *
   * @param string $messageText
   *   The message from Slack.
   * @param \Drupal\node\NodeInterface $player
   *   The current player.
   * @param array $eventCallback
   *   The Slack event.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function lookTarget(string $messageText, NodeInterface $player, array $eventCallback): void {
    // Player is looking AT something.
    // Remove the AT if there is one.
    $target = str_replace(' at ', '', $messageText);
    // Now remove the LOOK and we'll see who or what they're looking
    // at.
    $target = str_replace('look', '', $target);
    $target = trim($target);

    $foundSomething = FALSE;

    $loc = $player->field_location->entity;
    $slackUsername = $player->field_slack_user_name->value;
    $otherPlayers = $this->otherPlayersInLocation($slackUsername, $loc);
    foreach ($otherPlayers as $otherPlayer) {
      $otherPlayerDisplayName = strtolower(trim($otherPlayer->field_display_name->value));
      if (strpos($otherPlayerDisplayName, $target) === 0) {
        // Other player's name starts with the string the user
        // typed.
        $mudEvent = new LookAtPlayerEvent($player, $otherPlayer);
        $mudEvent = $this->eventDispatcher->dispatch(LookAtPlayerEvent::LOOK_AT_PLAYER_EVENT, $mudEvent);
        if ($response = $mudEvent->getResponse()) {
          $channel = $eventCallback['user'];
          $this->slack->slackApi('chat.postMessage', 'POST', [
            'channel' => $channel,
            'text' => $response,
            'as_user' => TRUE,
          ]);
        }
        $foundSomething = TRUE;
        break;
      }
    }

    if (!$foundSomething) {
      // Didn't find a player. Let's look for items.
      // First visible items.
      foreach ($loc->field_visible_items as $item) {
        $itemName = strtolower(trim($item->entity->getTitle()));
        if (strpos($itemName, $target) === 0) {
          // Other item's name starts with the string the user
          // typed.
          $desc = $item->entity->body->value;
          $channel = $eventCallback['user'];
          $this->slack->slackApi('chat.postMessage', 'POST', [
            'channel' => $channel,
            'text' => strip_tags($desc),
            'as_user' => TRUE,
          ]);
          $foundSomething = TRUE;
          break;
        }
      }
    }

    if (!$foundSomething) {
      // Now description items.
      foreach ($loc->field_description_items as $item) {
        $itemName = strtolower(trim($item->entity->getTitle()));
        if (strpos($itemName, $target) === 0) {
          // Other item's name starts with the string the user
          // typed.
          $desc = $item->entity->body->value;
          $channel = $eventCallback['user'];
          $this->slack->slackApi('chat.postMessage', 'POST', [
            'channel' => $channel,
            'text' => strip_tags($desc),
            'as_user' => TRUE,
          ]);
          $foundSomething = TRUE;
          break;
        }
      }
    }

    if (!$foundSomething) {
      // Finally, the items in the player's inventory.
      foreach ($player->field_inventory as $item) {
        $itemName = strtolower(trim($item->entity->getTitle()));
        if (strpos($itemName, $target) === 0) {
          // Other item's name starts with the string the user
          // typed.
          $desc = $item->entity->body->value;
          $channel = $eventCallback['user'];
          $this->slack->slackApi('chat.postMessage', 'POST', [
            'channel' => $channel,
            'text' => strip_tags($desc),
            'as_user' => TRUE,
          ]);
          $foundSomething = TRUE;
          break;
        }
      }
    }
  }

  /**
   * Handler for getting.
   *
   * @param string $messageText
   *   The message from Slack.
   * @param \Drupal\node\NodeInterface $player
   *   The current player.
   * @param array $eventCallback
   *   The Slack event.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function getHandler(string $messageText, NodeInterface $player, array $eventCallback) {
    // Now remove the GET and we'll see who or what they're taking.
    $target = str_replace('get', '', $messageText);
    $target = trim($target);

    $foundSomething = FALSE;
    $loc = $player->field_location->entity;
    $slackUsername = $player->field_slack_user_name->value;
    $otherPlayers = $this->otherPlayersInLocation($slackUsername, $loc);

    foreach ($loc->field_visible_items as $delta => $item) {
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (strpos($itemName, $target) === 0) {
        // Item's name starts with the string the user typed.

        // If the item is gettable, add item to player's inventory.
        if ($item->entity->field_can_pick_up->value) {
          $player->field_inventory[] = ['target_id' => $item->entity->id()];
          $player->save();

          // Remove item from location.
          unset($loc->field_visible_items[$delta]);
          $loc->save();

          $channel = $eventCallback['user'];
          $message = 'You picked up the ' . $itemName;
          $this->slack->slackApi('chat.postMessage', 'POST', [
            'channel' => $channel,
            'text' => strip_tags($message),
            'as_user' => TRUE,
          ]);
        }
        else {
          // Can't pick up - show its deny get message.
          $channel = $eventCallback['user'];
          $message = $item->entity->field_deny_get_message->value;
          $this->slack->slackApi('chat.postMessage', 'POST', [
            'channel' => $channel,
            'text' => strip_tags($message),
            'as_user' => TRUE,
          ]);
        }
        $foundSomething = TRUE;
        break;
      }
    }

    if (!$foundSomething) {
      $channel = $eventCallback['user'];
      $where = $loc->field_object_location->value;
      $message = t("Sorry, there is no :target :where.",
        [
          ':target' => $target,
          ':where' => $where,
        ]
      );
      $this->slack->slackApi('chat.postMessage', 'POST', [
        'channel' => $channel,
        'text' => strip_tags($message),
        'as_user' => TRUE,
      ]);
    }

  }

  /**
   * Handler for dropping.
   *
   * @param string $messageText
   *   The message from Slack.
   * @param \Drupal\node\NodeInterface $player
   *   The current player.
   * @param array $eventCallback
   *   The Slack event.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function dropHandler(string $messageText, NodeInterface $player, array $eventCallback): void {
    // Now remove the DROP and we'll see who or what they're taking.
    $target = str_replace('drop', '', $messageText);
    $target = trim($target);

    $foundSomething = FALSE;
    $loc = $player->field_location->entity;
    $slackUsername = $player->field_slack_user_name->value;
    $otherPlayers = $this->otherPlayersInLocation($slackUsername, $loc);

    foreach ($player->field_inventory as $delta => $item) {
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (strpos($itemName, $target) === 0) {
        // Item's name starts with the string the user typed.
        // Add item to location.
        $loc->field_visible_items[] = ['target_id' => $item->entity->id()];
        $loc->save();

        // Remove item from inventory.
        unset($player->field_inventory[$delta]);
        $player->save();

        $channel = $eventCallback['user'];
        $message = 'You dropped the ' . $itemName;
        $this->slack->slackApi('chat.postMessage', 'POST', [
          'channel' => $channel,
          'text' => strip_tags($message),
          'as_user' => TRUE,
        ]);
        $foundSomething = TRUE;
        break;
      }
    }
  }

  /**
   * @param string $messageText
   * @param $player
   * @param $eventCallback
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function moveHandler(string $messageText, $player, $eventCallback): void {
    // Check if the text entered is a direction from the location's
    // exists.
    // Alias north/n, west/w, south/s, east/e.
    $loc = $player->field_location->entity;
    $foundExit = FALSE;
    foreach ($loc->field_exits as $exit) {
      if ($messageText == $exit->label) {
        $nextLoc = $exit->entity;
        $player->field_location = $nextLoc;
        $player->save();
        $this->lookLocation($eventCallback, $player);
        $foundExit = TRUE;
        break;
      }
    }
    if (!$foundExit) {
      // If the command was a direction, you can't go that way.
      if (in_array($messageText, $this->directions)) {
        $message = "You can't go that way.";
      }
      $channel = $eventCallback['user'];
      $this->slack->slackApi('chat.postMessage', 'POST', [
        'channel' => $channel,
        'text' => strip_tags($message),
        'as_user' => TRUE,
      ]);
    }
  }

}
