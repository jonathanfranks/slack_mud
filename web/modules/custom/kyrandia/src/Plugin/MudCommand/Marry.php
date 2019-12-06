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
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);

    $words = explode(' ', $commandText);
    // Word 0 has to be marry, otherwise we wouldn't be here.
    // We'll assume word 1 is the target player.
    if ($loc->getTitle() == 'Location 7' && count($words) > 1) {
      $target = $words[1];
      if ($otherPlayer = $this->locationHasPlayer($target, $loc, FALSE, $actingPlayer)) {
        if ($otherPlayer->id() == $actingPlayer->id()) {
          // Can't marry self.
          $result = $this->getMessage('MARRY2');
        }
        else {
          if ($profile->field_kyrandia_married_to->target_id) {
            // Already married.
            $spouse = $profile->field_kyrandia_married_to->entity->field_display_name->value;
            $result = sprintf($this->getMessage('MARRY0'), $spouse);
          }
          else {
            // Set acting spouse.
            $profile->field_kyrandia_married_to = $otherPlayer;
            $profile->save();
            // Set target spouse.
            $otherProfile = $this->getKyrandiaProfile($otherPlayer);
            $otherProfile->field_kyrandia_married_to = $actingPlayer;
            $otherProfile->save();
            $result = sprintf($this->getMessage('MARRY4'), $otherPlayer->field_display_name->value);
          }
        }
      }
      else {
        // Player isn't here.
        $result = $this->getMessage('MARRY7');
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
