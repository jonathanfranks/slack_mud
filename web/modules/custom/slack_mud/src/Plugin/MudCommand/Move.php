<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Move command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "move",
 *   module = "slack_mud",
 *   synonyms = {
 *     "up",
 *     "down",
 *     "north",
 *     "n",
 *     "south",
 *     "s",
 *     "east",
 *     "e",
 *     "west",
 *     "w",
 *     "northeast",
 *     "ne",
 *     "southeast",
 *     "se",
 *     "southwest",
 *     "sw",
 *     "northwest",
 *     "nw"
 *   },
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
   * Array of valid direction opposites in same order as $directions.
   *
   * @var array
   */
  protected $oppositeDirections = [
    'down',
    'up',
    'south',
    'north',
    'west',
    'east',
    'southeast',
    'northeast',
    'southwest',
    'northwest',
  ];

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Remove the "move " from "move north".
    $words = explode(' ', $commandText);
    // Assume 'move' is word 0.
    if (count($words) > 1) {
      $direction = $words[1];
    }
    else {
      $direction = NULL;
    }
    // Check if the text entered is a direction from the location's
    // exists.
    // @TODO Alias north/n, west/w, south/s, east/e.
    $loc = $actingPlayer->field_location->entity;
    $foundExit = FALSE;
    foreach ($loc->field_exits as $exit) {
      if ($direction == $exit->label) {
        $nextLoc = $exit->entity;

        // Add leaving and entering messages.
        $exitMessage = sprintf('moved off to the %s', $direction);

        $directionIndex = array_search($direction, $this->directions);
        $oppositeDirection = $this->oppositeDirections[$directionIndex];
        $entranceMessage = sprintf('appeared from the %s', $oppositeDirection);

        // Handle the move.
        $this->gameHandler->movePlayer($actingPlayer, $nextLoc->getTitle(), $results, $exitMessage, $entranceMessage);

        // The result for the acting player is LOOKing at the new location.
        $this->performAnotherAction('look', $actingPlayer, $results);
        $foundExit = TRUE;
        break;
      }
    }
    if (!$foundExit) {
      // If the command was a direction, you can't go that way.
      if (in_array($commandText, $this->directions)) {
        $results[$actingPlayer->id()][] = "You can't go that way.";
      }
    }
  }

}
