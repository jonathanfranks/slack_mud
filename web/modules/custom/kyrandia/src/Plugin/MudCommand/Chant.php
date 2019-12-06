<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Chant command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_chant",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Chant extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($commandText == 'chant tashanna' && $loc->getTitle() == 'Location 7') {
      // Chant Tashanna at the temple makes the altar glow in 5 stages.
      $game = $actingPlayer->field_game->entity;
      $currentTempleChantCount = $this->getInstanceSetting($game, 'currentTempleChantCount', 0);
      if ($currentTempleChantCount == 0) {
        // First time chanting.
        $result = 'The altar begins to glow dimly.';
      }
      else {
        $result = 'The altar begins to glow even brighter!';
      }
      if ($currentTempleChantCount < 5) {
        $currentTempleChantCount++;
      }
      $this->saveInstanceSetting($game, 'currentTempleChantCount', $currentTempleChantCount);
    }
    elseif ($commandText == 'chant opensesame' && $loc->getTitle() == 'Location 185') {
      $result = $this->getMessage('WALM03');
      $profile->field_kyrandia_open_sesame = TRUE;
      $profile->save();
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
