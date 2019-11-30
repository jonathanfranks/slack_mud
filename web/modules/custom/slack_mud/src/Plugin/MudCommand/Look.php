<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\LookAtPlayerEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Level command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "look",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Look extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    if ($commandText == 'look') {
      $result = $this->lookLocation($actingPlayer);
    }
    elseif (strpos($commandText, 'look ') !== FALSE) {
      $result = $this->lookTarget($commandText, $actingPlayer);
    }
    return $result;
  }

  /**
   * Gets a player's current inventory.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The current player node.
   *
   * @return string
   *   The response.
   */
  protected function lookLocation(NodeInterface $player) {
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

    $response = strip_tags($message);
    return $response;
  }

  /**
   * Handler for looking at a target.
   *
   * @param string $messageText
   *   The message from Slack.
   * @param \Drupal\node\NodeInterface $player
   *   The current player.
   *
   * @return string
   *   The result.
   */
  protected function lookTarget(string $messageText, NodeInterface $player) {
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
          return $response;
        }
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
          $response = $item->entity->body->value;
          return $response;
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
          return $desc;
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
          return $desc;
        }
      }
    }
    // Didn't find anything.
    return t("There's nothing like that here.");
  }

}
