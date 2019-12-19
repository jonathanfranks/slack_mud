<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Enter command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_enter",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Enter extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $loc = $actingPlayer->field_location->entity;
    $game = $actingPlayer->field_game->entity;
    if ($commandText == 'enter portal' && $loc->getTitle() == 'Location 184') {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PORTAL');
      $random = $this->generateRandomNumber($game, 1, 9);
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PORTAL' . $random);
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('ENDPOR');

      $othersMessage = sprintf($this->gameHandler->getMessage('OEPORT'), $actingPlayer->field_display_name->value, $this->gameHandler->heShe($profile));
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
