<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Defines Move command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "move",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Move extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * Array of valid direction commands.
   *
   * @var array
   */
  protected $directions = [
    'up',
    'down',
    'north',
    'south',
    'east',
    'west',
    'northwest',
    'southwest',
    'northeast',
    'southeast',
  ];

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = '';
    // Remove the "move " from "move north".
    $commandText = str_replace('move ', '', $commandText);
    // Check if the text entered is a direction from the location's
    // exists.
    // Alias north/n, west/w, south/s, east/e.
    $loc = $actingPlayer->field_location->entity;
    $foundExit = FALSE;
    foreach ($loc->field_exits as $exit) {
      if ($commandText == $exit->label) {
        $nextLoc = $exit->entity;
        $actingPlayer->field_location = $nextLoc;
        $actingPlayer->save();

        // The result is LOOKing at the new location.
        $mudEvent = new CommandEvent($actingPlayer, 'look');
        $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
        $result = $mudEvent->getResponse();

        $foundExit = TRUE;
        break;
      }
    }
    if (!$foundExit) {
      // If the command was a direction, you can't go that way.
      if (in_array($commandText, $this->directions)) {
        $result = "You can't go that way.";
      }
    }
    return $result;
  }

}
