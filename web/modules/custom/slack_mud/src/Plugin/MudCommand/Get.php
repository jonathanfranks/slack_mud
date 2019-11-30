<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\LookAtPlayerEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Get command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "get",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Get extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = '';
    // Now remove the GET and we'll see who or what they're taking.
    $target = str_replace('get', '', $commandText);
    $target = trim($target);

    $foundSomething = FALSE;
    $loc = $actingPlayer->field_location->entity;

    foreach ($loc->field_visible_items as $delta => $item) {
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (strpos($itemName, $target) === 0) {
        // Item's name starts with the string the user typed.
        // If the item is gettable, add item to player's inventory.
        if ($item->entity->field_can_pick_up->value) {
          $actingPlayer->field_inventory[] = ['target_id' => $item->entity->id()];
          $actingPlayer->save();

          // Remove item from location.
          unset($loc->field_visible_items[$delta]);
          $loc->save();

          $result = 'You picked up the ' . $itemName;
        }
        else {
          // Can't pick up - show its deny get message.
          $result = $item->entity->field_deny_get_message->value;
        }
        $foundSomething = TRUE;
        break;
      }
    }

    if (!$foundSomething) {
      $where = $loc->field_object_location->value;
      $result = t("Sorry, there is no :target :where.",
        [
          ':target' => $target,
          ':where' => $where,
        ]
      );
    }
    return $result;
  }

}
