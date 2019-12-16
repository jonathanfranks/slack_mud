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
   * Returns other player nodes who are in the same location.
   *
   * @param \Drupal\node\NodeInterface $location
   *   The location where the user is.
   * @param \Drupal\node\NodeInterface|null $actingPlayer
   *   The player looking in the room (to be excluded from list). If no player
   *   is specified, return all the players in the location.
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]
   *   An array of players who are also in the same location.
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
   * Checks if the player has the specified item.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player whose inventory we are checking.
   * @param string $targetItemName
   *   The item we're checking for.
   * @param bool $removeItem
   *   If TRUE, remove the item from the player's inventory when found.
   *
   * @return \Drupal\node\NodeInterface|bool
   *   FALSE if the player doesn't the item, otherwise the item in
   *   the player's inventory field.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
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
   * Removes the named item from the player's inventory.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player.
   * @param string $targetItemName
   *   The name of the item.
   *
   * @return bool
   *   TRUE if the item was present and removed.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function takeItemFromPlayer(NodeInterface $player, $targetItemName) {
    return $this->playerHasItem($player, $targetItemName, TRUE);
  }

  /**
   * Gives the named item to the specified player.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player.
   * @param string $itemName
   *   The item to give.
   *
   * @return bool
   *   TRUE if the item was given.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
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
   * Puts the named item in the specified location.
   *
   * @param \Drupal\node\NodeInterface $location
   *   The location.
   * @param string $itemName
   *   The item to place.
   *
   * @return bool
   *   TRUE if the item was placed.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
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
   * Does the specified target player exist in the specified location?
   *
   * @param string $target
   *   Partial player display name to look for.
   * @param \Drupal\node\NodeInterface $location
   *   Location node.
   * @param bool $excludeActingPlayer
   *   TRUE if we should exclude the acting player.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The acting player, so we can exclude them if specified.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\node\Entity\Node|mixed|null
   *   The targeted player.
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
   * Moves a player to the specified location.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player being moved.
   * @param string $locationName
   *   The name of the new location.
   *
   * @return bool
   *   TRUE if the move was successful (location exists).
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function movePlayer(NodeInterface $player, $locationName) {
    $gameId = $player->field_game->target_id;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'location')
      ->condition('field_game.target_id', $gameId)
      ->condition('title', $locationName);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $player->field_location = $id;
      $player->save();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Gets human-readable items a player is holding.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player whose items we're looking at.
   *
   * @return string
   *   The human-readable stringified inventory.
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
   * Gets the item targetted in the command in the specified location.
   *
   * @param \Drupal\node\NodeInterface $location
   *   The location.
   * @param string $commandText
   *   The command text.
   * @param bool $removeItem
   *   If TRUE, remove the item from the location when found.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The item if found, otherwise NULL.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
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
