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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $game = $actingPlayer->field_game->entity;
    if ($commandText == 'chant tashanna' && $loc->getTitle() == 'Location 7') {
      // Chant Tashanna at the temple makes the altar glow in 5 stages.
      $currentTempleChantCount = $this->gameHandler->getInstanceSetting($game, 'currentTempleChantCount', 0);
      if ($currentTempleChantCount == 0) {
        // First time chanting.
        $results[$actingPlayer->id()][] = t('The altar begins to glow dimly.');
        $othersMessage = t('The altar begins to glow dimly.');
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $results[$actingPlayer->id()][] = t('The altar begins to glow even brighter!');
        $othersMessage = t('The altar begins to glow even brighter!');
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      if ($currentTempleChantCount < 5) {
        $currentTempleChantCount++;
      }
      $this->gameHandler->saveInstanceSetting($game, 'currentTempleChantCount', $currentTempleChantCount);
    }
    elseif ($commandText == 'chant opensesame' && $loc->getTitle() == 'Location 185') {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('WALM03');
      $othersMessage = $this->gameHandler->getMessage('WALM04');
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      $this->gameHandler->saveInstanceSetting($game, 'opensesame', TRUE);
      $profile->field_kyrandia_open_sesame = TRUE;
      $profile->save();
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
