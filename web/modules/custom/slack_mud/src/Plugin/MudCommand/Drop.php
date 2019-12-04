<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    $result = '';
    // Now remove the DROP and we'll see who or what they're taking.
    $target = str_replace('drop', '', $commandText);
    $target = trim($target);

    $loc = $actingPlayer->field_location->entity;

    $invDelta = $this->playerHasItem($actingPlayer, $target);
    if ($invDelta !== FALSE) {
      // Player has the item.
      $item = $actingPlayer->field_inventory[$invDelta]->entity;
      // Now handle the dropping of it.
      // Add item to location.
      $loc->field_visible_items[] = ['target_id' => $item->id()];
      $loc->save();

      // Remove item from inventory.
      unset($actingPlayer->field_inventory[$invDelta]);
      $actingPlayer->save();

      $result = t('You dropped the :item.', [':item' => $item->getTitle()]);
    }
    else {
      $result = t("You don't have a :target.", [':target' => $target]);
    }
    return $result;
  }

}
