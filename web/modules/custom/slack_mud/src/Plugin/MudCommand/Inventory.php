<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Inventory command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "inventory",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Inventory extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    if (count($actingPlayer->field_inventory)) {
      $inv = $this->gameHandler->playerInventoryString($actingPlayer);
      $results[$actingPlayer->id()][] = t('You have :results.', [':results' => $results]);
    }
    else {
      $results[$actingPlayer->id()][] = t('You are not carrying anything.');
    }
  }

}
