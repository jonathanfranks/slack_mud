<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Devote command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_devote",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Devote extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can dig in the brook to randomly find gold.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 295') {
      $profile = $this->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '16') {
        // Player needs broach, pendant, locket, and ring.
        $hasBroach = $this->playerHasItem($actingPlayer, 'broach');
        $hasPendant = $this->playerHasItem($actingPlayer, 'pendant');
        $hasLocket = $this->playerHasItem($actingPlayer, 'locket');
        $hasRing = $this->playerHasItem($actingPlayer, 'ring');
        if ($hasBroach && $hasPendant && $hasLocket && $hasRing) {
          $this->advanceLevel($profile, 17);
          $result = $this->getMessage('DEVM01');
          $this->takeItemFromPlayer($actingPlayer, 'broach');
          $this->takeItemFromPlayer($actingPlayer, 'pendant');
          $this->takeItemFromPlayer($actingPlayer, 'locket');
          $this->takeItemFromPlayer($actingPlayer, 'ring');
        }
        else {
          $result = $this->getMessage('DEVM03');
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
