<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Seek command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_seek",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Seek extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = [];
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 280') {
      if ($commandText == 'seek truth') {
        $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
        if ($profile->field_kyrandia_level->entity->getName() == '17') {
          $this->gameHandler->advanceLevel($profile, 18);
          // Roughly 50% chance that player makes it or dies.
          $game = $actingPlayer->field_game->entity;
          // Check the forceRandomNumber setting for test values.
          $random = $this->gameHandler->getInstanceSetting($game, 'forceRandomNumber', NULL);
          if ($random == NULL) {
            // Nothing being forced. Generate the real number.
            $random = rand(0, 100);
          }
          if ($random < 50) {
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('TRUM01');
            $this->gameHandler->damagePlayer($actingPlayer, 100, $result);
          }
          else {
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('TRUM02');
            $othersMessage = sprintf($this->gameHandler->getMessage('TRUM03'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
          }
        }
      }
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = 'Nothing happens.';
    }
    return $result;
  }

}
