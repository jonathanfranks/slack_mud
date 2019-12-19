<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Cry command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_cry",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Cry extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Players say a command at the temple to get to level 3.
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 26') {
      // Player is at the ash trees.
      $words = explode(' ', $commandText);
      $synonyms = [
        'tree',
        'trees',
        'ash',
        'ashes',
      ];
      $synonymMatch = array_intersect($synonyms, $words);
      if ($synonymMatch) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('ASHM00');
        $othersMessage = $this->gameHandler->getMessage('ASHM01');
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        if (!$this->gameHandler->placeItemInLocation($loc, 'shard')) {
          // Location item maximum, does not appear.
          // Message goes to everyone.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('ASHM02');
          $othersMessage = $this->gameHandler->getMessage('ASHM02');
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
