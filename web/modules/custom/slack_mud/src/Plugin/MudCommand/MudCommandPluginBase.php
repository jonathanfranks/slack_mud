<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\Core\Plugin\PluginBase;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Drupal\slack_mud\Service\MudGameHandlerServiceInterface;
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
   * @var \Drupal\word_grammar\Service\WordGrammarInterface
   */
  protected $wordGrammar;

  /**
   * The game handler service.
   *
   * @var \Drupal\slack_mud\Service\MudGameHandlerServiceInterface
   */
  protected $gameHandler;

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
   * @param \Drupal\slack_mud\Service\MudGameHandlerServiceInterface $game_handler
   *   The game handler.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EventDispatcherInterface $event_dispatcher, WordGrammarInterface $word_grammar, MudGameHandlerServiceInterface $game_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->eventDispatcher = $event_dispatcher;
    $this->wordGrammar = $word_grammar;
    $this->gameHandler = $game_handler;
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
      $container->get('word_grammar_service'),
      $container->get('slack_mud.game_handler'),
      $container->get('plugin.manager.mud_command')
    );
  }

  /**
   * Performs another action and adds it to the current results.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function performAnotherAction($commandText, NodeInterface $actingPlayer, array &$results) {
    $mudEvent = new CommandEvent($actingPlayer, $commandText, $results);
    $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
    $results = $mudEvent->getResponse();
  }

}
