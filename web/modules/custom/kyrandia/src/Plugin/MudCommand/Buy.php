<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
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
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 9') {
      // Selling gems at the gem store.
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
            $result = $this->getMessage('BUYM00');
          }
          else {
            // Add spell.
            if ($this->giveSpellToPlayer($actingPlayer, $target)) {
              $result = $this->getMessage('BUYM02');
              // Subtract gold.
              $playerGold -= $price;
              $profile->field_kyrandia_gold = $playerGold;
              $profile->save();
            }
            else {
              $result = "The shop keeper shakes his head and says, \"You already have that spell.\"";
            }
          }
        }
        else {
          // Doesn't have it.
          $result = $this->getMessage('BUYM04');
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
