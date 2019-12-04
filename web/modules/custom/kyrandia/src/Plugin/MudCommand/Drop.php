<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kyrandia-specific Drop command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_drop",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Drop extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players drop things into the stump at Loc 18 to reach level 6.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if (strpos($commandText, 'stump') !== FALSE && $loc->getTitle() == 'Location 18') {
      $currentStumpStone = intval($profile->field_kyrandia_stump_gem->value);
      $stumpStones = [
        'ruby',
        'emerald',
        'garnet',
        'pearl',
        'aquamarine',
        'moonstone',
        'sapphire',
        'diamond',
        'amethyst',
        'onyx',
        'opal',
        'bloodstone',
      ];

      // Player is dropping something in or into stump.
      $target = $this->getTargetItem($commandText);
      $invDelta = $this->playerHasItem($actingPlayer, $target);
      if ($invDelta === FALSE) {
        $result = t("But you don't have one, you hallucinating fool!");
      }
      else {
        $item = $actingPlayer->field_inventory[$invDelta]->entity;
        $itemTitle = $item->getTitle();
        $stumpStoneIndex = array_search($itemTitle, $stumpStones);
        if ($stumpStoneIndex === $currentStumpStone) {
          // Match! This is the last one, so advance to level 6 if the user is
          // level 5!
          if ($currentStumpStone == count($stumpStones) - 1 && $profile->field_kyrandia_level->entity->getName() == '5') {
            $this->advanceLevel($profile, 6);
            $result = "As you drop the gem into the stump, a powerful surge of magical energy rushes through your entire body!
***\n
You are now at level 6!\n
***\n
A spell has been added to your spellbook!";
          }
          else {
            // Match! Set the stone index to the next one.
            $result = "The gem drops smoothly into the endless depths of the stump. You feel a mysterious tingle down your spine, as though you have begun to unleash a powerful source of magic.";
            if ($currentStumpStone < count($stumpStones) - 1) {
              // If it's the last one but the user isn't level 5, just keep the
              // index where it is.
              $nextStumpStone = $currentStumpStone + 1;
              $profile->field_kyrandia_stump_gem = $nextStumpStone;
              $profile->save();
            }
          }
        }
        else {
          $result = "It drops into the endless depths of the stump, but nothing seems to happen.";
        }
        // Remove item from inventory whether it matches or not.
        unset($actingPlayer->field_inventory[$invDelta]);
        $actingPlayer->save();
      }
    }
    else {
      // Not a stump drop. Handle this like a regular drop.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('drop');
      $result = $plugin->perform($commandText, $actingPlayer);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Gets the item targeted by the command.
   *
   * @param string $commandText
   *   The command text the user entered.
   *
   * @return mixed|string
   *   The object the user typed.
   */
  protected function getTargetItem($commandText) {
    $target = str_replace('stump', '', $commandText);
    $target = str_replace('into', '', $target);
    $target = str_replace('in', '', $target);
    // Now remove the DROP and we'll see what they're dropping.
    $target = str_replace('drop', '', $target);
    $target = trim($target);
    return $target;
  }

}
