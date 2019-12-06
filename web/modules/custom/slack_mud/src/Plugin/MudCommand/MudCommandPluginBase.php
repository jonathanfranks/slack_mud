<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\a_or_an\Service\IndefiniteArticleInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\LookAtPlayerEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Drupal\word_grammar\Service\WordGrammarInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Defines a base MudCommand plugin implementation.
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
abstract class MudCommandPluginBase extends PluginBase implements MudCommandPluginInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Indefinite article generator.
   *
   * @var \Drupal\a_or_an\Service\IndefiniteArticleInterface
   */
  protected $wordGrammar;

  /**
   * MudCommandPluginBase constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\word_grammar\Service\WordGrammarInterface $word_grammar
   *   The indefinite article service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EventDispatcherInterface $event_dispatcher, WordGrammarInterface $word_grammar) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->eventDispatcher = $event_dispatcher;
    $this->wordGrammar = $word_grammar;
  }

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
      $plugin_definition,
      $container->get('event_dispatcher'),
      $container->get('word_grammar_service')
    );
  }

  /**
   * Returns other player nodes who are in the same location.
   *
   * @param string|null $slackUserName
   *   The current player's Slack username. If no slackUserName is specified,
   *   return all the players in the location.
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
      ->condition('field_location.target_id', $location->id())
      ->condition('field_active', TRUE);
    if ($slackUserName) {
      $query->condition('field_slack_user_name', $slackUserName, '<>');

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
   *
   * @return bool|int
   *   FALSE if the player doesn't the item, otherwise the delta of the item in
   *   the player's inventory field.
   */
  protected function playerHasItem(NodeInterface $player, $targetItemName) {
    foreach ($player->field_inventory as $delta => $item) {
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (strpos($itemName, $targetItemName) === 0) {
        return $delta;
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
  protected function takeItemFromPlayer(NodeInterface $player, $targetItemName) {
    $invDelta = $this->playerHasItem($player, $targetItemName);
    if ($invDelta) {
      // Remove item from inventory whether it matches or not.
      unset($player->field_inventory[$invDelta]);
      $player->save();
      return TRUE;
    }
    return FALSE;
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
  protected function giveItemToPlayer(NodeInterface $player, string $itemName) {
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
  protected function placeItemInLocation(NodeInterface $location, string $itemName) {
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
  protected function locationHasPlayer($target, NodeInterface $location, $excludeActingPlayer, NodeInterface $actingPlayer = NULL) {
    if ($excludeActingPlayer) {
      $slackUsername = $actingPlayer->field_slack_user_name->value;
    }
    else {
      $slackUsername = NULL;
    }
    $otherPlayers = $this->otherPlayersInLocation($slackUsername, $location);
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
  protected function movePlayer(NodeInterface $player, $locationName) {
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
  protected function playerInventoryString(NodeInterface $player) {
    $inv = [];
    foreach ($player->field_inventory as $itemNid => $item) {
      $itemTitle = $item->entity->getTitle();
      $article = $this->wordGrammar->getIndefiniteArticle($itemTitle);
      $inv[] = $article . ' ' . $itemTitle;
    }
    $results = $this->wordGrammar->getWordList($inv);
    return $results;
  }

}
