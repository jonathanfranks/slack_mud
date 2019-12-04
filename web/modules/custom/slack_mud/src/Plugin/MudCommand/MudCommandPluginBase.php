<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\a_or_an\Service\IndefiniteArticleInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
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

}
