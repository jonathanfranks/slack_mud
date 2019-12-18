<?php

namespace Drupal\slack_mud;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;

/**
 * Defines the interface for MUD commands.
 */
interface MudCommandPluginInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Return the name of the plugin.
   *
   * @param string $commandText
   *   Command text to execute.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player performing the action.
   * @param array $results
   *   The results array that the command plugin adds to or modifies.
   *
   * @return array
   *   Response array where the player node ID is the key and the value is an
   *   array of the response messages to return to that player. Multiple players
   *   may receive responses. Example:
   *     [
   *       1735 => [
   *         'You do not see anything here.',
   *         'You might be eaten by a grue.',
   *       ],
   *       18203 => [
   *         'Jack is looking around.',
   *       ],
   *     ]
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results);

}
