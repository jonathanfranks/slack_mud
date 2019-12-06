<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Drop command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "drop",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Drop extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Now remove the DROP and we'll see who or what they're taking.
    $target = str_replace('drop', '', $commandText);
    $target = trim($target);

    $loc = $actingPlayer->field_location->entity;

    $item = $this->playerHasItem($actingPlayer, $target, TRUE);
    if ($item) {
      // Player has the item.
      $this->placeItemInLocation($loc, $item->getTitle());
      $result = t('You dropped the :item.', [':item' => $item->getTitle()]);
    }
    else {
      $result = t("You don't have a :target.", [':target' => $target]);
    }
    return $result;
  }

}
