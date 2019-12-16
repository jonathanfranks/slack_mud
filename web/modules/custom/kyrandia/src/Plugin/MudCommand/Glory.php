<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Glory command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_glory",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Glory extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players say a command at the temple to get to level 3.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if ($commandText == 'glory be tashanna' && $loc->getTitle() == 'Location 7') {
      // Player is at the temple.
      $level = $profile->field_kyrandia_level->entity;
      if ($level->getName() == '2') {
        $this->gameHandler->advanceLevel($profile, 3);
        $result[$actingPlayer->id()][] = $this->gameHandler->getMessage("LVL300");
        $othersMessage = sprintf($this->gameHandler->getMessage('GETLVL'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
