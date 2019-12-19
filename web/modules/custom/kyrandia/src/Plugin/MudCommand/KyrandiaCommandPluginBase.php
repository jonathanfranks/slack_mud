<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\kyrandia\Service\KyrandiaGameHandlerServiceInterface;
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
      $container->get('event_dispatcher'),
      $container->get('word_grammar_service'),
      $container->get('kyrandia.game_handler'),
      $container->get('plugin.manager.mud_command')
    );
  }

  /**
   * Send to others in location with specific text format.
   *
   * This method is ported by namem from original source code.
   */
  protected function sndutl(NodeInterface $actingPlayer, $message, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $othersMessage = sprintf("***\n%s is %s\n", $actingPlayer->field_display_name->value, sprintf($message, $this->gameHandler->hisHer($profile)));
    $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
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
    if ($random == NULL) {
      // Nothing being forced. Generate the real number.
      $random = rand(0, 100);
    }
    return $random;
  }

}
