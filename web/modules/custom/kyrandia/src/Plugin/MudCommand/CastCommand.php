<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Cast command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_cast",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class CastCommand extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players say a command at the temple to get to level 3.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);

    $words = explode(' ', $commandText);
    $spell = '';
    $target = '';
    if (count($words) > 1) {
      // Spell is the second word. "cast zennyra".
      $spell = $words[1];
    }
    if (count($words) > 2) {
      // Target is the last word. "cast zelastone at player".
      $target = end($words);
    }

    // Handle exceptions first.
    if ($spell == 'zennyra' && $loc->getTitle() == 'Location 213') {
      // Casting zennyra (not a real spell) at the altar of sunshine gives a
      // message.
      $result = $this->getMessage('SUNM02');
    }

    // Cast a spell.
    if ($this->playerHasSpell($actingPlayer, $spell)) {
      switch ($spell) {
        case 'zapher':
          if ($loc->getTitle() == 'Location 213' && $target == 'tulip' && $this->playerHasItem($actingPlayer, 'tulip', TRUE)) {
            // Casting zapher at the tulip at the altar of sunshine gives player
            // a wand.
            if ($this->giveItemToPlayer($actingPlayer, 'wand')) {
              $result = $this->getMessage('SUNM00');
            }
          }
          break;
      }
    }
    if (!$result) {
      $result = 'You don\'t have that spell.';
    }
    return $result;
  }

}
