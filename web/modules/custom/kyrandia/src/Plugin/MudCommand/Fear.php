<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Fear command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_fear",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Fear extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // "Fear no evil" advances to level 5 at the dead wooded glade.
    // Players say a command at the temple to get to level 3.
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($commandText == 'fear no evil' && $loc->getTitle() == 'Location 16') {
      $level = $profile->field_kyrandia_level->entity;
      if ($level->getName() == '4') {
        $this->gameHandler->advanceLevel($profile, 5);
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('FEAR01');
        $othersMessage = sprintf($this->gameHandler->getMessage('FEAR02'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
