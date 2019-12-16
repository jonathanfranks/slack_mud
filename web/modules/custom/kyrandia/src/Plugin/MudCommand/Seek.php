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
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 280') {
      if ($commandText == 'seek truth') {
        $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
        if ($profile->field_kyrandia_level->entity->getName() == '17') {
          $this->gameHandler->advanceLevel($profile, 18);
          // Roughly 50% chance that player makes it or dies.
          $random = rand(0, 100);
          if ($random < 50) {
            $result = $this->gameHandler->getMessage('TRUM01');
            $this->gameHandler->damagePlayer($actingPlayer, 100);
          }
          else {
            $result = $this->gameHandler->getMessage('TRUM02');
          }
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
