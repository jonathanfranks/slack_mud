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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Players drop things into the stump at Loc 18 to reach level 6.
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if (strpos($commandText, 'statue') !== FALSE && $loc->getTitle() == 'Location 181') {
      $results[$actingPlayer->id()][] = $this->statue();
    }
    elseif (strpos($commandText, 'pool') !== FALSE && $loc->getTitle() == 'Location 182') {
      $results[$actingPlayer->id()][] = $this->reflectingPool();
    }
    elseif (strpos($commandText, 'symbols') !== FALSE && $loc->getTitle() == 'Location 183') {
      $results[$actingPlayer->id()][] = $this->pantheonSymbols();
    }
    elseif (strpos($commandText, 'pillars') !== FALSE && $loc->getTitle() == 'Location 183') {
      $results[$actingPlayer->id()][] = $this->pantheonSymbols();
    }
    else {
      // Not a special look. Handle this like a regular look.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('look');
      $plugin->perform($commandText, $actingPlayer, $results);

      $words = explode(' ', $commandText);
      if (count($words) > 1) {
        // Assume word 1 is the target.
        $target = $words[1];
        if ($target == 'brief') {
          $briefDesc = $loc->field_brief_description->value;
          $results[$actingPlayer->id()][0] = sprintf($this->gameHandler->getMessage('LOOKER5'), $briefDesc);
          $othersMessage = t(':actor is glancing around briefly!', [
            ':actor' => $actingPlayer->field_display_name->value,
          ]);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
        elseif ($target == 'spellbook') {
          $this->performAnotherAction('spellbook', $actingPlayer, $results);
        }
        elseif ($targetPlayer = $this->gameHandler->locationHasPlayer($target, $loc, FALSE)) {
          $profile = $this->gameHandler->getKyrandiaProfile($targetPlayer);
          // Override the look description if the target player is in a
          // different form.
          if ($profile->field_kyrandia_invisf->value) {
            $results[$actingPlayer->id()][0] = $this->gameHandler->getMessage('INVDES');
          }
          elseif ($profile->field_kyrandia_willow->value) {
            $results[$actingPlayer->id()][0] = $this->gameHandler->getMessage('WILDES');
          }
          elseif ($profile->field_kyrandia_pegasu->value) {
            $results[$actingPlayer->id()][0] = $this->gameHandler->getMessage('PEGDES');
          }
          elseif ($profile->field_kyrandia_pdragn->value) {
            $results[$actingPlayer->id()][0] = $this->gameHandler->getMessage('PDRDES');
          }
          else {
            $displayName = $targetPlayer->field_display_name->value;
            $isFemale = $profile->field_kyrandia_is_female->value;
            $level = $profile->field_kyrandia_level->entity;
            $genderDescription = $isFemale ? $level->field_female_description->value : $level->field_male_description->value;
            $targetInventory = $this->gameHandler->playerInventoryString($targetPlayer);
            if (!$targetInventory) {
              $targetInventory = t('nothing');
            }
            $desc = sprintf($genderDescription, $displayName) . ' ' . $targetInventory . '.';
            $results[$actingPlayer->id()][0] = $desc;
          }
          $results[$targetPlayer->id()][] = sprintf($this->gameHandler->getMessage('LOOKER3'), $actingPlayer->field_display_name->value);
          $othersMessage = sprintf($this->gameHandler->getMessage('LOOKER4'), $actingPlayer->field_display_name->value, $targetPlayer->field_display_name->value);
          $exceptPlayers = [$targetPlayer];
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results, $exceptPlayers);
        }
        elseif ($item = $this->gameHandler->locationHasItem($loc, $commandText, FALSE)) {
          $othersMessage = sprintf($this->gameHandler->getMessage('LOOKER1'), $actingPlayer->field_display_name->value, $target, $loc->field_object_location->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
        elseif ($item = $this->gameHandler->playerHasItem($actingPlayer, $target, FALSE)) {
          $othersMessage = sprintf($this->gameHandler->getMessage('LOOKER2'), $actingPlayer->field_display_name->value, $profile->field_kyrandia_is_female->value ? 'her' : 'his', $target);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
        else {
          // Looking at a target where the target doesn't exist returns the room
          // description in Kyrandia.
          $results[$actingPlayer->id()][0] = $loc->body->value;
        }
      }
      else {
        // Tell the other players in the room that actor is looking around.
        // This is in source in looker(), there's no message for this.
        $othersMessage = t(':actor is carefully inspecting the surroundings.', [
          ':actor' => $actingPlayer->field_display_name->value,
        ]);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
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
