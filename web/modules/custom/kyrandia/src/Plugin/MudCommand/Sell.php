<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Sell command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_sell",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Sell extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 8') {
      // Selling gems at the gem store.
      // @TODO Maybe make this configurable?
      $values = [
        "ruby" => 22,
        "emerald" => 25,
        "garnet" => 2,
        "pearl" => 6,
        "aquamarine" => 9,
        "moonstone" => 32,
        "sapphire" => 16,
        "diamond" => 30,
        "amethyst" => 10,
        "onyx" => 28,
        "opal" => 12,
        "bloodstone" => 20,
      ];

      $words = explode(' ', $commandText);
      // Assume the pattern is "sell [item]...".
      // We don't care about anything after word 1.
      if (count($words) > 1) {
        $target = $words[1];
        $invDelta = $this->playerHasItem($actingPlayer, $target);
        if ($invDelta !== FALSE) {
          // Player has item.
          $item = $actingPlayer->field_inventory[$invDelta]->entity;
          if (array_key_exists($item->getTitle(), $values)) {
            // Gem is on the menu.
            $price = $values[$item->getTitle()];
            $result = t("The gem cutter considers for a moment, takes your gem, and then gives you :gold pieces of gold.", [':gold' => $price]);
            $profile->field_kyrandia_gold->value += $price;
            $profile->save();

            // Remove item from inventory.
            unset($actingPlayer->field_inventory[$invDelta]);
            $actingPlayer->save();
          }
          elseif ($item->getTitle() == 'kyragem') {
            $result = 'The gem cutter looks sharply at you, and then suddenly smiles. He leans closer and says, "I bid you good luck, brave seeker of legends". He takes the kyragem and hands you a soulstone.';
            // Remove item from inventory.
            unset($actingPlayer->field_inventory[$invDelta]);
            $actingPlayer->save();
            $this->giveItemToPlayer($actingPlayer, 'soulstone');
          }
          else {
            // Doesn't want it.
            $result = 'The gem cutter says to you, "Thanks, but no thanks."';
          }
        }
        else {
          $result = "Unfortunately, you don't have that at the moment.";
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
