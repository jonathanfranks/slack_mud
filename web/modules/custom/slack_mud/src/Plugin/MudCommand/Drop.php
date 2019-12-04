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

    $foundSomething = FALSE;
    $loc = $actingPlayer->field_location->entity;

    foreach ($actingPlayer->field_inventory as $delta => $item) {
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (strpos($itemName, $target) === 0) {
        // Item's name starts with the string the user typed.
        // Add item to location.
        $loc->field_visible_items[] = ['target_id' => $item->entity->id()];
        $loc->save();

        // Remove item from inventory.
        unset($actingPlayer->field_inventory[$delta]);
        $actingPlayer->save();

        $result = 'You dropped the ' . $itemName;
        $foundSomething = TRUE;
        break;
      }
    }
    if (!$foundSomething) {
      $result = t("You don't have a :target.", [':target' => $target]);
    }
    return $result;
  }

}
