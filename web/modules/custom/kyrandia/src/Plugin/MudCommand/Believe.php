<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Believe command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_believe",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Believe extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 257') {
      if ($commandText == 'believe in magic') {
        $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
        if ($profile->field_kyrandia_level->entity->getName() == '20') {
          if ($this->gameHandler->advanceLevel($profile, 21)) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('LEVL21');
            $othersMessage = sprintf($this->gameHandler->getMessage('LVL9M1'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
        }
      }
    }
    elseif ($loc->getTitle() == 'Location 293') {
      if ($commandText == 'believe in fantasy') {
        $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
        if ($profile->field_kyrandia_level->entity->getName() == '23') {
          if ($this->gameHandler->advanceLevel($profile, 24)) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('LEVL24');
            $othersMessage = sprintf($this->gameHandler->getMessage('LVL9M1'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
        }
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
