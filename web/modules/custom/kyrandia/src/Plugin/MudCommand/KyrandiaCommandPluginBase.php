<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\kyrandia\Service\KyrandiaGameHandlerServiceInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Drupal\slack_mud\Plugin\MudCommand\MudCommandPluginBase;
use Drupal\word_grammar\Service\WordGrammarInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Defines a base MudCommand plugin implementation.
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
abstract class KyrandiaCommandPluginBase extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * The game handler service.
   *
   * @var \Drupal\kyrandia\Service\KyrandiaGameHandlerServiceInterface
   */
  protected $gameHandler;

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
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher'),
      $container->get('word_grammar_service'),
      $container->get('kyrandia.game_handler'),
      $container->get('plugin.manager.mud_command')
    );
  }

  /**
   * Send a message to the acting player.
   *
   * This method is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param string $actorMessage
   *   Message going to the actor.
   * @param array $result
   *   The results array.
   */
  protected function youmsg(NodeInterface $actingPlayer, $actorMessage, array &$result) {
    $result[$actingPlayer->id()][] = $this->gameHandler->getMessage($actorMessage);
  }

  /**
   * Send a message to the acting player.
   *
   * This method is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param string $actorMessage
   *   Message going to the actor.
   * @param array $result
   *   The results array.
   */
  protected function prfmsg(NodeInterface $actingPlayer, $actorMessage, array &$result) {
    $result[$actingPlayer->id()][] = $this->gameHandler->getMessage($actorMessage);
  }

  /**
   * Send a message to two groups.
   *
   * This sends to the actor and players in the actor's location.
   *
   * This method is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param string $actorMessage
   *   Message going to the actor.
   * @param string $otherMessage
   *   Message going to everyone else in the location.
   * @param array $result
   *   The results array.
   */
  protected function msgutl2(NodeInterface $actingPlayer, $actorMessage, $otherMessage, array &$result) {
    $result[$actingPlayer->id()][] = $this->gameHandler->getMessage($actorMessage);
    $loc = $actingPlayer->field_location->entity;
    $othersMessage = sprintf($this->gameHandler->getMessage($otherMessage), $actingPlayer->field_display_name->value);
    $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
  }

  /**
   * Send a message to three groups.
   *
   * This sends to the actor, the target, and players in the actor's location.
   *
   * This method is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The acting player.
   * @param string $actorMessage
   *   Message going to the actor.
   * @param \Drupal\node\NodeInterface $targetPlayer
   *   The target player.
   * @param string $targetMessage
   *   Message going to the target.
   * @param string $otherMessage
   *   Message going to everyone else in the location.
   * @param array $results
   *   The results array.
   */
  protected function msgutl3(NodeInterface $actingPlayer, $actorMessage, NodeInterface $targetPlayer, $targetMessage, $otherMessage, array &$results) {
    $results[$actingPlayer->id()][] = $this->gameHandler->getMessage($actorMessage);
    $results[$targetPlayer->id()][] = sprintf($this->gameHandler->getMessage($targetMessage), $actingPlayer->field_display_name->value);
    $exceptPlayers = [$targetPlayer];
    $loc = $actingPlayer->field_location->entity;
    $othersMessage = sprintf($this->gameHandler->getMessage($otherMessage), $actingPlayer->field_display_name->value, $targetPlayer->field_display_name->value);
    $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results, $exceptPlayers);
  }

  /**
   * Send to others in location with specific text format.
   *
   * This method is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The acting player.
   * @param string $message
   *   The message to send.
   * @param array $results
   *   The result array.
   */
  protected function sndutl(NodeInterface $actingPlayer, $message, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $othersMessage = sprintf("***\n%s is %s\n", $actingPlayer->field_display_name->value, sprintf($message, $this->gameHandler->hisHer($profile)));
    $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
  }

  /**
   * Send message to closely located rooms.
   *
   * This method is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The acting player.
   * @param string $message
   *   The message to send.
   * @param array $results
   *   The result array.
   */
  protected function sndnear(NodeInterface $actingPlayer, $message, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    foreach ($loc->field_exits as $exit) {
      $nextLoc = $exit->entity;
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $nextLoc, $message, $results);
    }
  }

  /**
   * Handles random number generation with forced number instance settings.
   *
   * Mostly, the forced number settings are for testing.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The Kyrandia game node.
   * @param int $min
   *   The minimum random number.
   * @param int $max
   *   The maximum random number.
   *
   * @return int
   *   The random number.
   */
  protected function generateRandomNumber(NodeInterface $game, $min, $max) {
    $random = $this->gameHandler->getInstanceSetting($game, 'forceRandomNumber', NULL);
    if ($random === NULL) {
      // Nothing being forced. Generate the real number.
      $random = rand(0, 100);
    }
    return $random;
  }

}
