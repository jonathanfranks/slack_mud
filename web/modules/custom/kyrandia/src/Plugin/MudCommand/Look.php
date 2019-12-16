<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kyrandia-specific Look command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_look",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Look extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players drop things into the stump at Loc 18 to reach level 6.
    $result = [];
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if (strpos($commandText, 'statue') !== FALSE && $loc->getTitle() == 'Location 181') {
      $result[$actingPlayer->id()][] = $this->statue();
    }
    elseif (strpos($commandText, 'pool') !== FALSE && $loc->getTitle() == 'Location 182') {
      $result[$actingPlayer->id()][] = $this->reflectingPool();
    }
    elseif (strpos($commandText, 'symbols') !== FALSE && $loc->getTitle() == 'Location 183') {
      $result[$actingPlayer->id()][] = $this->pantheonSymbols();
    }
    elseif (strpos($commandText, 'pillars') !== FALSE && $loc->getTitle() == 'Location 183') {
      $result[$actingPlayer->id()][] = $this->pantheonSymbols();
    }
    else {
      // Not a special look. Handle this like a regular look.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('look');
      $result = $plugin->perform($commandText, $actingPlayer);

      $words = explode(' ', $commandText);
      if (count($words) > 1) {
        // Assume word 1 is the target.
        $target = $words[1];
        if ($target == 'brief') {
          $briefDesc = $loc->field_brief_description->value;
          $result[$actingPlayer->id()][0] = sprintf($this->getMessage('LOOKER5'), $briefDesc);
          $othersMessage = t(':actor is glancing around briefly!', [
            ':actor' => $actingPlayer->field_display_name->value,
          ]);
          $this->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
        elseif ($target == 'spellbook') {
          /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
          $pluginManager = \Drupal::service('plugin.manager.mud_command');
          /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
          $plugin = $pluginManager->createInstance('kyrandia_spellbook');
          $result = $plugin->perform($commandText, $actingPlayer);
        }
        elseif ($targetPlayer = $this->locationHasPlayer($target, $loc, FALSE)) {
          $profile = $this->getKyrandiaProfile($targetPlayer);
          // Override the look description if the target player is in a
          // different form.
          if ($profile->field_kyrandia_invisible->value) {
            $result[$actingPlayer->id()][0] = $this->getMessage('INVDES');
          }
          elseif ($profile->field_kyrandia_willowisp->value) {
            $result[$actingPlayer->id()][0] = $this->getMessage('WILDES');
          }
          elseif ($profile->field_kyrandia_pegasus->value) {
            $result[$actingPlayer->id()][0] = $this->getMessage('PEGDES');
          }
          elseif ($profile->field_kyrandia_pseudodragon->value) {
            $result[$actingPlayer->id()][0] = $this->getMessage('PDRDES');
          }
          $result[$targetPlayer->id()][] = sprintf($this->getMessage('LOOKER3'), $actingPlayer->field_display_name->value);
          $othersMessage = sprintf($this->getMessage('LOOKER4'), $actingPlayer->field_display_name->value, $targetPlayer->field_display_name->value);
          $exceptPlayers = [$targetPlayer];
          $this->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result, $exceptPlayers);
        }
        elseif ($item = $this->locationHasItem($loc, $commandText, FALSE)) {
          $othersMessage = sprintf($this->getMessage('LOOKER1'), $actingPlayer->field_display_name->value, $target, $loc->field_object_location->value);
          $this->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
        elseif ($item = $this->playerHasItem($actingPlayer, $target, FALSE)) {
          $othersMessage = sprintf($this->getMessage('LOOKER2'), $actingPlayer->field_display_name->value, $profile->field_kyrandia_is_female->value ? 'her' : 'his', $target);
          $this->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
      }
      else {
        // Tell the other players in the room that actor is looking around.
        // This is in source in looker(), there's no message for this.
        $othersMessage = t(':actor is carefully inspecting the surroundings.', [
          ':actor' => $actingPlayer->field_display_name->value,
        ]);
        $this->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
      }
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Handles looking at the statue.
   *
   * @return string
   *   The result.
   */
  protected function statue() {
    return "The statue is quite a spectacular work of art, especially for its large proportions.  Despite the wonderful artwork, your attention is grasped by an inscription near the base which reads: \"id- upi- ozs-omsy-pm -p p-ysom y-r fs--rt\".";
  }

  /**
   * Handles looking at the statue.
   *
   * @return string
   *   The result.
   */
  protected function reflectingPool() {
    return "As you look into the pool, you see a mirror image of yourself.\n
***\n
Suddenly, the words \"feoamut wotaquop jagarooni pistobba\" appear in the pool, and then mysteriously vanish!";
  }

  /**
   * Handles looking at the symbols.
   *
   * @return string
   *   The result.
   */
  protected function pantheonSymbols() {
    return "As you examine the ancient runes embedded into the marble pillars, you are only able to recognize a few of the symbols, although you cannot instantly translate their true meaning. You read: \"ha-tc en-esa-eop ore-ef gusi- xx-he xxx-e-\".";
  }

}
