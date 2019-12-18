<?php

namespace Drupal\slack_mud\Service;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\word_grammar\Service\WordGrammarInterface;

/**
 * Class MudGameHandlerService.
 *
 * @package Drupal\slack_mud\Service
 */
class MudGameHandlerService implements MudGameHandlerServiceInterface {

  /**
   * Word grammar service.
   *
   * @var \Drupal\word_grammar\Service\WordGrammarInterface
   */
  protected $wordGrammar;

  /**
   * MudGameHandlerService constructor.
   *
   * @param \Drupal\word_grammar\Service\WordGrammarInterface $word_grammar
   *   Word grammar service.
   */
  public function __construct(WordGrammarInterface $word_grammar) {
    $this->wordGrammar = $word_grammar;
  }

  /**
   * {@inheritdoc}
   */
  public function otherPlayersInLocation(NodeInterface $location, NodeInterface $actingPlayer = NULL) {
    $players = [];
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'player')
      ->condition('field_location.target_id', $location->id())
      ->condition('field_active', TRUE);
    if ($actingPlayer) {
      $query->condition('nid', $actingPlayer->id(), '<>');
    }
    $playerNids = $query->execute();
    if ($playerNids) {
      $players = Node::loadMultiple($playerNids);
    }
    return $players;
  }

  /**
   * {@inheritdoc}
   */
  public function playerHasItem(NodeInterface $player, $targetItemName, $removeItem = FALSE) {
    foreach ($player->field_inventory as $delta => $item) {
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (strpos($itemName, $targetItemName) === 0) {
        if ($removeItem) {
          unset($player->field_inventory[$delta]);
          $player->save();
        }
        return $item->entity;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function takeItemFromPlayer(NodeInterface $player, $targetItemName) {
    return $this->playerHasItem($player, $targetItemName, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function giveItemToPlayer(NodeInterface $player, string $itemName) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'item')
      ->condition('field_game.entity.title', 'kyrandia')
      ->condition('title', $itemName);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      // @TODO Handling max items for player.
      $player->field_inventory[] = ['target_id' => $id];
      $player->save();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocationByName($locationName) {
    $locationNode = NULL;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'location')
      ->condition('title', $locationName);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $locationNode = Node::load($id);
    }
    return $locationNode;
  }

  /**
   * {@inheritdoc}
   */
  public function placeItemInLocation(NodeInterface $location, string $itemName) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'item')
      ->condition('field_game.entity.title', 'kyrandia')
      ->condition('title', $itemName);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      // @TODO Handling max items for location.
      $location->field_visible_items[] = ['target_id' => $id];
      $location->save();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function locationHasPlayer($target, NodeInterface $location, $excludeActingPlayer, NodeInterface $actingPlayer = NULL) {
    if ($excludeActingPlayer) {
      $slackUsername = $actingPlayer->field_slack_user_name->value;
    }
    else {
      $slackUsername = NULL;
    }
    $otherPlayers = $this->otherPlayersInLocation($location, $actingPlayer);
    foreach ($otherPlayers as $otherPlayer) {
      $otherPlayerDisplayName = strtolower(trim($otherPlayer->field_display_name->value));
      if (strpos($otherPlayerDisplayName, $target) === 0) {
        // Other player's name starts with the string the user
        // typed.
        return $otherPlayer;
      }
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function sendMessageToOthersInLocation(NodeInterface $actingPlayer, NodeInterface $loc, string $othersMessage, array &$result, array $exceptPlayers = []) {
    $otherPlayers = $this->otherPlayersInLocation($loc, $actingPlayer);
    $noMessagePlayerIds = [];
    foreach ($exceptPlayers as $exceptPlayer) {
      $noMessagePlayerIds[] = $exceptPlayer->id();
    }
    foreach ($otherPlayers as $otherPlayer) {
      if (!in_array($otherPlayer->id(), $noMessagePlayerIds)) {
        $result[$otherPlayer->id()][] = $othersMessage;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function movePlayer(NodeInterface $player, $locationName, array &$result, $exitMessage, $entranceMessage) {
    $originalLocation = $player->field_location->entity;
    if ($exitMessage) {
      $exit = sprintf("***\n%s has just %s!\n", $player->field_display_name->value, $exitMessage);
      $this->sendMessageToOthersInLocation($player, $originalLocation, $exit, $result);
    }
    $gameId = $player->field_game->target_id;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'location')
      ->condition('field_game.target_id', $gameId)
      ->condition('title', $locationName);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $newLocation = Node::load($id);
      $player->field_location = $id;
      $player->save();
      if ($entranceMessage) {
        $entrance = sprintf("***\n%s has just %s!\n", $player->field_display_name->value, $entranceMessage);
        $this->sendMessageToOthersInLocation($player, $newLocation, $entrance, $result);
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function playerInventoryString(NodeInterface $player) {
    $inv = [];
    foreach ($player->field_inventory as $itemNid => $item) {
      $itemTitle = $item->entity->getTitle();
      $article = $this->wordGrammar->getIndefiniteArticle($itemTitle);
      $inv[] = $article . ' ' . $itemTitle;
    }
    $results = $this->wordGrammar->getWordList($inv);
    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function locationHasItem(NodeInterface $location, $commandText, $removeItem = FALSE) {
    $words = explode(' ', $commandText);
    // Assume first word is always the verb.
    $verb = array_shift($words);
    foreach ($location->field_visible_items as $delta => $item) {
      // Item names have to be exact matches.
      // Any of the other words could target an item in the room.
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (in_array($itemName, $words)) {
        // We have an exact match on the name. Use this item.
        if ($removeItem) {
          unset($location->field_visible_items[$delta]);
          $location->save();
        }
        return $item->entity;
      }
    }
    return NULL;
  }

}
