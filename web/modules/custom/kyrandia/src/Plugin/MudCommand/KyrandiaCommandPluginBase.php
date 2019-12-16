<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\kyrandia\Service\KyrandiaGameHandlerServiceInterface;
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
      $container->get('kyrandia.game_handler')
    );
  }

}
