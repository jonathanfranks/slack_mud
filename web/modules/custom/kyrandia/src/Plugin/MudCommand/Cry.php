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
    $profile = $this->getKyrandiaProfile($actingPlayer);
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
        $result = "As you cry your tears for the sorrow of the ash trees, they fall to the ground and magically transform into a beautiful crystal shard!";
        if ($this->placeItemInLocation($loc, 'shard')) {
        }
        else {
          $result .= "\nUnfortunately, the shard vanishes a moment later!";
        }
      }

    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
