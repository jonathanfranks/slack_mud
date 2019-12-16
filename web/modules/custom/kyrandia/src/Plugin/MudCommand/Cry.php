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
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players say a command at the temple to get to level 3.
    $result = NULL;
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
        $result = $this->gameHandler->getMessage('ASHM00');
        if ($this->placeItemInLocation($loc, 'shard')) {
        }
        else {
          $result .= "\n" . $this->gameHandler->getMessage('ASHM02');
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
