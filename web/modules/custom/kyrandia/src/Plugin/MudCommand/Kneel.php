<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kneel command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_kneel",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Kneel extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can kneel at the willow tree at location 0 to go from level 1 to
    // level 2.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 0') {
      // Player is at the willow tree.
      $level = $profile->field_kyrandia_level->entity;
      if ($level->getName() == '1') {
        $this->gameHandler->advanceLevel($profile, 2);
        $result[$actingPlayer->id()][] = $this->gameHandler->getMessage("LVL200");
        $othersMessage = sprintf($this->gameHandler->getMessage('GETLVL'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        $this->gameHandler->giveSpellToPlayer($actingPlayer, 'smokey');
      }
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = 'You kneel.';
    }
    return $result;
  }

}
