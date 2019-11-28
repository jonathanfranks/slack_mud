<?php

namespace Drupal\kyrandia;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;

/**
 * Defines the interface for Kyrandia commands.
 */
interface KyrandiaCommandPluginInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Return the name of the plugin.
   *
   * @param string $commandText
   *   Command text to execute.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player performing the action.
   *
   * @return string
   *   The name of the plugin.
   */
  public function perform($commandText, NodeInterface $actingPlayer);

}
