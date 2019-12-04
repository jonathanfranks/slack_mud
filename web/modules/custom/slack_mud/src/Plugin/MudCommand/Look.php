<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\LookAtPlayerEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Look command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "look",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Look extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    if ($commandText == 'look') {
      $result = $this->lookLocation($actingPlayer);
    }
    elseif (strpos($commandText, 'look ') !== FALSE) {
      $result = $this->lookTarget($commandText, $actingPlayer);
    }
    return $result;
  }

  /**
   * Looks in the current location.
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
        $itemTitle = $visible_item->entity->getTitle();
        $article = $this->wordGrammar->getIndefiniteArticle($itemTitle);
        $visible_items[] = $article . ' ' . $itemTitle;
      }
    }
    $where = $loc->field_object_location->value;
    $visible_items_text = $this->wordGrammar->getWordList($visible_items);

    // Currently using "is" for any number.
    switch (count($visible_items)) {
      case 0:
        $here = t('There is nothing :where.', [
          ':where' => $where,
        ]);
        break;

      default:
        $here = t('There is :items :where.', [
          ':items' => $visible_items_text,
          ':where' => $where,
        ]);
    }
    $message .= "\n" . $here;

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
        /** @var \Drupal\slack_mud\Event\LookAtPlayerEvent $mudEvent */
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
