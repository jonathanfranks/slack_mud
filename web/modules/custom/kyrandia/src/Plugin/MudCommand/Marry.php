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
          $result = "Trying to marry yourself, huh?  Sorry, we don't allow that sort of self pleasure in Kyrandia...";
        }
        else {
          if ($profile->field_kyrandia_married_to->target_id) {
            // Already married.
            $spouse = $profile->field_kyrandia_married_to->entity->field_display_name->value;
            $result = t("What bigamy is this? You've already sworn your life to :spouse! Surely you have more dedication than that!", [':spouse' => $spouse]);
          }
          else {
            // Set acting spouse.
            $profile->field_kyrandia_married_to = $otherPlayer;
            $profile->save();
            // Set target spouse.
            $otherProfile = $this->getKyrandiaProfile($otherPlayer);
            $otherProfile->field_kyrandia_married_to = $actingPlayer;
            $otherProfile->save();
            $result = t('You devote the rest of your mortal life in Kyrandia to :spouse.', [':spouse' => $otherPlayer->field_display_name->value]);
          }
        }
      }
      else {
        // Player isn't here.
        $result = "Feeling somewhat lonely, huh?  Sorry, that person isn't around...";
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
