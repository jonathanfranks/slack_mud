<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kneel command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_kneel",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Kneel extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can kneel at the willow tree at location 0 to go from level 1 to
    // level 2.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 0') {
      // Player is at the willow tree.
      $level = $profile->field_kyrandia_level->entity;
      if ($level->getName() == '1') {
        $this->advanceLevel($profile, 1);
        $result = $this->getMessage("LVL200");
      }
    }
    if (!$result) {
      $result = 'You kneel.';
    }
    return $result;
  }

}
