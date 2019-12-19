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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Offers hints at silver altar and temple, otherwise just a nothing action.
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 24') {
      // Player is at the silver altar.
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SAPRAY');
      $this->sndutl($actingPlayer, 'praying to the Goddess Tashanna.', $results);
    }
    elseif ($loc->getTitle() == 'Location 7') {
      // In the temple.
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('TMPRAY');
      $this->sndutl($actingPlayer, 'praying to the Goddess Tashanna.', $results);
    }
    elseif ($loc->getTitle() == 'Location 27') {
      // At the rock. Player only has to pray once for the mists to come.
      $results[$actingPlayer->id()][] = "Your prayers are heard.";
      $results[$actingPlayer->id()][] = "***\nThe mists around the rock begin to swirl magically!\n";
      $othersMessage = "***\nThe mists around the rock begin to swirl magically!\n";
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      $game = $actingPlayer->field_game->entity;
      $this->gameHandler->saveInstanceSetting($game, 'currentRockPrayCount', 1);
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PRAYER');
      $this->sndutl($actingPlayer, 'praying piously.', $results);
    }
  }

}
