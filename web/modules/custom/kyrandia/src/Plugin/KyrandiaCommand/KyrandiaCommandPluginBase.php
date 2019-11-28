<?php

namespace Drupal\kyrandia\Plugin\KyrandiaCommand;

use Drupal\Core\Plugin\PluginBase;
use Drupal\kyrandia\KyrandiaCommandPluginInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a base KyrandiaCommand plugin implementation.
 *
 * @package Drupal\kyrnandia\Plugin\KyrandiaCommand
 */
abstract class KyrandiaCommandPluginBase extends PluginBase implements KyrandiaCommandPluginInterface {

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
      $plugin_definition
    );
  }

}
