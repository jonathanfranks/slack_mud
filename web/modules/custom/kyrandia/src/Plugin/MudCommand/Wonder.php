<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Wonder command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_wonder",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Wonder extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $result = [];
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 264') {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '22') {
        if ($this->gameHandler->advanceLevel($profile, 23)) {
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('LEVL23');
          $othersMessage = sprintf($this->gameHandler->getMessage('LVL9M1'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);

        }
      }
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = 'Nothing happens.';
    }
    return $result;
  }

}
