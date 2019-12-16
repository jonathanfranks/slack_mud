<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Pray command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_pray",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Pray extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Offers hints at silver altar and temple, otherwise just a nothing action.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 24') {
      // Player is at the silver altar.
      $result = $this->gameHandler->getMessage('SAPRAY');
    }
    elseif ($loc->getTitle() == 'Location 7') {
      // In the temple.
      $result = $this->gameHandler->getMessage('TMPRAY');
    }
    elseif ($loc->getTitle() == 'Location 27') {
      // At the rock. Player only has to pray once for the mists to come.
      $result = "Your prayers are heard.\n***\nThe mists around the rock begin to swirl magically!\n";
      $game = $actingPlayer->field_game->entity;
      $this->gameHandler->saveInstanceSetting($game, 'currentRockPrayCount', 1);
    }
    if (!$result) {
      $result = $this->gameHandler->getMessage('PRAYER');
    }
    return $result;
  }

}
