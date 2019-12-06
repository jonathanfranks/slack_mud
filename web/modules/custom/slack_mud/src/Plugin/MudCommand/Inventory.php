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
  public function perform($commandText, NodeInterface $actingPlayer) {
    if (count($actingPlayer->field_inventory)) {
      $results = $this->playerInventoryString($actingPlayer);
      $result = t('You have :results.', [':results' => $results]);
    }
    else {
      $result = t('You are not carrying anything.');
    }
    return $result;
  }

}
