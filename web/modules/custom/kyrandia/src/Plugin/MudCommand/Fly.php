<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Fly command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_fly",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Fly extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($profile->field_kyrandia_willow->value) {
      if ($loc->getTitle() == 'Location 179') {
        $this->willof($actingPlayer, 180, $results);
      }
      elseif ($loc->getTitle() == 'Location 180') {
        $this->willof($actingPlayer, 179, $results);
      }
      else {
        $this->msgutl2($actingPlayer, 'UNOFLY', 'ATFLY1', $results);
      }
    }
    elseif ($profile->field_kyrandia_pegasu->value) {
      if ($loc->getTitle() == 'Location 22') {
        $this->pegasf($actingPlayer, 189, $results);
      }
      elseif ($loc->getTitle() == 'Location 189') {
        $this->pegasf($actingPlayer, 22, $results);
      }
      else {
        $this->msgutl2($actingPlayer, 'UNOFLY', 'ATFLY1', $results);
      }
    }
    else {
      $this->msgutl2($actingPlayer, 'HUNFLY', 'ATFLY1', $results);
    }
  }

  /**
   * Willowisp flying routine.
   *
   * This routine is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param string $targetDestination
   *   The new location name.
   * @param array $results
   *   The results array.
   */
  protected function willof(NodeInterface $actingPlayer, $targetDestination, array &$results) {
    $this->prfmsg($actingPlayer, 'WILFLY', $results);
    $newLocationTitle = 'Location ' . $targetDestination;
    $this->gameHandler->movePlayer($actingPlayer, $newLocationTitle, $results, "gracefully flown across the chasm", "gracefully flown from across the chasm");
  }

  /**
   * Willowisp flying routine.
   *
   * This routine is ported by name from original source code.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param string $targetDestination
   *   The new location name.
   * @param array $results
   *   The results array.
   */
  protected function pegasf(NodeInterface $actingPlayer, $targetDestination, array &$results) {
    $this->prfmsg($actingPlayer, 'PEGFLY', $results);
    $newLocationTitle = 'Location ' . $targetDestination;
    $this->gameHandler->movePlayer($actingPlayer, $newLocationTitle, $results, "majestically flown across the sea", "majestically flown from across the sea");
  }

}
