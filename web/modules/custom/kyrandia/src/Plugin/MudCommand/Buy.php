<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Buy command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_buy",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Buy extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 9') {
      // Buying spells at the spell shop.
      // @TODO Maybe make this configurable?
      $values = [
        'burnup' => 80,
        'cantcmeha' => 100,
        'clutzopho' => 50,
        'cuseme' => 40,
        'fpandl' => 60,
        'hocus' => 400,
        'howru' => 25,
        'koolit' => 60,
        'noouch' => 30,
        'pocus' => 45,
        'sunglass' => 35,
        'thedoc' => 75,
        'tiltowait' => 1000,
        'weewillo' => 120,
        'whereami' => 100,
        'zapher' => 50,
      ];

      $words = explode(' ', $commandText);
      // Assume the pattern is "buy [item]...".
      // We don't care about anything after word 1.
      if (count($words) > 1) {
        $target = $words[1];
        if (array_key_exists($target, $values)) {
          // Gem is on the menu.
          $price = $values[$target];
          $playerGold = $profile->field_kyrandia_gold->value;
          if ($price > $playerGold) {
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('BUYM00');
            $othersMessage = sprintf($this->gameHandler->getMessage('BUYM01'), $actingPlayer->field_display_name->value, $profile->field_kyrandia_is_female->value ? 'her' : 'him');
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
          }
          else {
            // Add spell.
            $this->gameHandler->giveSpellToPlayer($actingPlayer, $target);
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('BUYM02');
            // Subtract gold.
            $playerGold -= $price;
            $profile->field_kyrandia_gold = $playerGold;
            $profile->save();
            $othersMessage = sprintf($this->gameHandler->getMessage('BUYM03'), $actingPlayer->field_display_name->value, $profile->field_kyrandia_is_female->value ? 'her' : 'his', $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
          }
        }
        else {
          // Doesn't have it.
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('BUYM04');
          $othersMessage = sprintf($this->gameHandler->getMessage('BUYM01'), $actingPlayer->field_display_name->value, $profile->field_kyrandia_is_female->value ? 'her' : 'him');
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
