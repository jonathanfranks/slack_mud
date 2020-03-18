<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    if ($commandText == 'look') {
      $this->lookLocation($actingPlayer, $results);
    }
    elseif (strpos($commandText, 'look ') !== FALSE) {
      $this->lookTarget($commandText, $actingPlayer, $results);
    }
  }

  /**
   * Looks in the current location.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The current player node.
   * @param array $results
   *   The results array.
   */
  protected function lookLocation(NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $results[$actingPlayer->id()][] = $loc->body->value;
    $results[$actingPlayer->id()][] = $this->seeOtherPlayersInLocation($actingPlayer, $loc);
    $results[$actingPlayer->id()][] = $this->seeItemsInLocation($loc);
  }

  /**
   * Handler for looking at a target.
   *
   * @param string $messageText
   *   The message from Slack.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The current player.
   * @param array $results
   *   The results array.
   */
  protected function lookTarget(string $messageText, NodeInterface $actingPlayer, array &$results) {
    // Player is looking AT something.
    // Remove the AT if there is one.
    $target = str_replace(' at ', '', $messageText);
    // Now remove the LOOK and we'll see who or what they're looking
    // at.
    $target = str_replace('look', '', $target);
    $target = trim($target);

    $foundSomething = FALSE;

    $loc = $actingPlayer->field_location->entity;
    $slackUsername = $actingPlayer->field_slack_user_name->value;
    // Allow players to look at themselves.
    $otherPlayers = $this->gameHandler->otherPlayersInLocation($loc);
    foreach ($otherPlayers as $otherPlayer) {
      $otherPlayerDisplayName = strtolower(trim($otherPlayer->field_display_name->value));
      if (strpos($otherPlayerDisplayName, $target) === 0) {
        // Other player's name starts with the string the user
        // typed.
        $results[$actingPlayer->id()][] = $otherPlayer->body->value;
        $foundSomething = TRUE;
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
          $results[$actingPlayer->id()][] = $item->entity->body->value;
          $foundSomething = TRUE;
        }
      }
    }

    if (!$foundSomething) {
      // Finally, the items in the player's inventory.
      foreach ($actingPlayer->field_inventory as $item) {
        $itemName = strtolower(trim($item->entity->getTitle()));
        if (strpos($itemName, $target) === 0) {
          // Other item's name starts with the string the user
          // typed.
          $results[$actingPlayer->id()][] = $item->entity->body->value;
          $foundSomething = TRUE;
        }
      }
    }
    // Didn't find anything.
    if (!array_key_exists($actingPlayer->id(), $results)) {
      $results[$actingPlayer->id()][] = t("There's nothing like that here.");
    }
  }

  /**
   * Returns the description line for other players in the location.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player looking in the room (to be excluded from list).
   * @param \Drupal\node\NodeInterface $loc
   *   The location being looked at.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The line showing what other players are in the location.
   */
  protected function seeOtherPlayersInLocation(NodeInterface $actingPlayer, NodeInterface $loc) {
    $message = NULL;
    $otherPlayers = $this->gameHandler->otherPlayersInLocation($loc, $actingPlayer);
    if ($otherPlayers) {
      $playerNames = [];
      if (count($otherPlayers) == 1) {
        $verb = 'is';
      }
      else {
        $verb = 'are';
      }
      foreach ($otherPlayers as $otherPlayer) {
        $playerNames[] = $otherPlayer->field_display_name->value;
      }
      $playerNameList = $this->wordGrammar->getWordList($playerNames);
      $message = t(':otherPlayers :verb here.', [
        ':otherPlayers' => $playerNameList,
        ':verb' => $verb,
      ]);
    }
    return $message;
  }

  /**
   * Returns the description line for items in the location.
   *
   * @param \Drupal\node\NodeInterface $loc
   *   The location node being looked at.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The description line for items in this location.
   */
  protected function seeItemsInLocation(NodeInterface $loc) {
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
    return $here;
  }

}
