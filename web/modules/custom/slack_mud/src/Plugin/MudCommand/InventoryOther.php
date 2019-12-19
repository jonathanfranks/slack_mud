<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines InventoryOther command plugin implementation.
 *
 * Gets items held by another player.
 *
 * @MudCommandPlugin(
 *   id = "inventory_other",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class InventoryOther extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $result = $this->gameHandler->playerInventoryString($actingPlayer);
    if (!$result) {
      $result = 'nothing';
    }
    $results[$actingPlayer->id()][] = $result;
  }

}
