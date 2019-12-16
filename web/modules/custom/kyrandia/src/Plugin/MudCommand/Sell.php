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
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
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
        $item = $this->gameHandler->playerHasItem($actingPlayer, $target, FALSE);
        if ($item) {
          // Player has item.
          if (array_key_exists($item->getTitle(), $values)) {
            // Gem is on the menu.
            $price = $values[$item->getTitle()];
            $result = sprintf($this->gameHandler->getMessage('TRDM00'), $price);
            $profile->field_kyrandia_gold->value += $price;
            $profile->save();
            $this->gameHandler->takeItemFromPlayer($actingPlayer, $item->getTitle());
          }
          elseif ($item->getTitle() == 'kyragem') {
            $result = $this->gameHandler->getMessage('TRDM02');
            $this->gameHandler->takeItemFromPlayer($actingPlayer, $item->getTitle());
            $this->gameHandler->giveItemToPlayer($actingPlayer, 'soulstone');
          }
          else {
            // Doesn't want it.
            $result = $this->gameHandler->getMessage('TRDM04');
          }
        }
        else {
          $result = $this->gameHandler->getMessage('TRDM05');
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
