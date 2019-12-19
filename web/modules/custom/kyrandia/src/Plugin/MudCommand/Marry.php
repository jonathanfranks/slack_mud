<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Marry command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_marry",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Marry extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);

    $words = explode(' ', $commandText);
    // Word 0 has to be marry, otherwise we wouldn't be here.
    // We'll assume word 1 is the target player.
    if ($loc->getTitle() == 'Location 7' && count($words) > 1) {
      $target = $words[1];
      if ($otherPlayer = $this->gameHandler->locationHasPlayer($target, $loc, FALSE, $actingPlayer)) {
        if ($otherPlayer->id() == $actingPlayer->id()) {
          // Can't marry self.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MARRY2');
          $othersMessage = sprintf($this->gameHandler->getMessage('MARRY3'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
        else {
          if ($profile->field_kyrandia_married_to->target_id) {
            // Already married.
            $spouse = $profile->field_kyrandia_married_to->entity->field_display_name->value;
            $results[$actingPlayer->id()][] = sprintf($this->gameHandler->getMessage('MARRY0'), $spouse);
            $othersMessage = sprintf($this->gameHandler->getMessage('MARRY1'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
          else {
            // Set acting spouse.
            $profile->field_kyrandia_married_to = $otherPlayer;
            $profile->save();
            // Set target spouse.
            $otherProfile = $this->gameHandler->getKyrandiaProfile($otherPlayer);
            $otherProfile->field_kyrandia_married_to = $actingPlayer;
            $otherProfile->save();
            $results[$actingPlayer->id()][] = sprintf($this->gameHandler->getMessage('MARRY4'), $otherPlayer->field_display_name->value);
            $results[$otherPlayer->id()][] = sprintf($this->gameHandler->getMessage('MARRY5'), $actingPlayer->field_display_name->value, $this->gameHandler->hisHer($profile));
            $othersMessage = sprintf($this->gameHandler->getMessage('MARRY6'), $actingPlayer->field_display_name->value, $this->gameHandler->hisHer($profile), $otherPlayer->field_display_name->value);
            $exclude = [$otherPlayer];
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results, $exclude);
          }
        }
      }
      else {
        // Player isn't here.
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MARRY7');
        $othersMessage = sprintf($this->gameHandler->getMessage('MARRY8'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
  }

}
