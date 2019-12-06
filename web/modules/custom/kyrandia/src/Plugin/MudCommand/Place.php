<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Place command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_place",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Place extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);

    if ($loc->getTitle() == 'Location 7') {
      $result = $this->temple($commandText, $actingPlayer, $profile);
    }
    elseif ($loc->getTitle() == 'Location 27') {
      $result = $this->strangeRock($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 185') {
      $result = $this->alcove($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 186') {
      $result = $this->slotMachine($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 188') {
      $result = $this->mistyRuins($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 218') {
      $result = $this->demonGate($commandText, $actingPlayer);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Placing items on the altar at the temple.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The player's Kyrandia profile.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function temple($commandText, NodeInterface $actingPlayer, NodeInterface $profile) {
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    $game = $actingPlayer->field_game->entity;
    $instanceSettingsText = $game->field_instance_settings->value;
    $settings = json_decode($instanceSettingsText, TRUE);
    $currentTempleChantCount = 0;
    if ($settings === NULL) {
      $settings = [];
    }
    if ($settings && array_key_exists('currentTempleChantCount', $settings)) {
      $currentTempleChantCount = $settings['currentTempleChantCount'];
    }
    if ($currentTempleChantCount >= 5) {
      $charmPos = array_search('charm', $words);
      $tiaraPos = array_search('tiara', $words);
      $altarPos = array_search('altar', $words);
      if ($charmPos !== FALSE && $charmPos < $altarPos && $profile->field_kyrandia_level->entity->getName() == 8) {
        $charmDelta = $this->playerHasItem($actingPlayer, 'charm');
        if ($charmDelta !== FALSE) {
          $result = "Tashanna accepts your offer!\n
***\n
You are now at level 9!";
          $this->advanceLevel($profile, 9);
          // Remove item from inventory.
          unset($actingPlayer->field_inventory[$charmDelta]);
          $actingPlayer->save();
        }
      }
      elseif ($tiaraPos !== FALSE && $tiaraPos < $altarPos && $profile->field_kyrandia_level->entity->getName() == 9) {
        $tiaraDelta = $this->playerHasItem($actingPlayer, 'tiara');
        if ($tiaraDelta !== FALSE) {
          $result = "Tashanna graciously accepts your gift.\n
***\n
You are now at level 10!";
          $this->advanceLevel($profile, 10);
          // Remove item from inventory.
          unset($actingPlayer->field_inventory[$tiaraDelta]);
          $actingPlayer->save();
        }
      }
      elseif ($altarPos >= 2) {
        // Player placed something else on the altar.
        $commandText = str_replace([
          'place',
          'the',
          'on',
          'altar',
        ], [
          '',
          '',
          '',
        ], $commandText);
        $commandText = trim($commandText);
        $itemDelta = $this->playerHasItem($actingPlayer, $commandText);
        if ($itemDelta !== FALSE) {
          $result = "The Goddess Tashanna accepts your humble offer and gives you her eternal blessings!";
          // Remove item from inventory.
          unset($actingPlayer->field_inventory[$itemDelta]);
          $actingPlayer->save();
        }
      }
    }
    return $result;
  }

  /**
   * Handles placing the sword in the rock.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function strangeRock($commandText, NodeInterface $actingPlayer): string {
    // At the strange rock.
    // It has to have been prayed at.
    $game = $actingPlayer->field_game->entity;
    $rockPrayCount = $this->getInstanceSetting($game, 'currentRockPrayCount', 0);
    if ($rockPrayCount) {
      $words = explode(' ', $commandText);
      // Word 0 has to be 'place', otherwise we wouldn't be here.
      // We're looking for 'place sword in rock'.
      $swordPos = array_search('sword', $words);
      $rockPos = array_search('rock', $words);
      if ($swordPos !== FALSE && $rockPos !== FALSE && $swordPos < $rockPos) {
        if ($this->takeItemFromPlayer($actingPlayer, 'sword')) {
          if ($this->giveItemToPlayer($actingPlayer, 'tiara')) {
            $result = "The rock glows a bright purple in acceptance of your offering, and the sword vanishes!\n
***\n
A tiara suddenly appears in your hands!";
          }
        }
        else {
          $result = "Hmmmmm, that's an interesting concept, but unfortunately, not an acceptable one.";
        }
      }
    }
    return $result;
  }

  /**
   * Handles placing the key in the crevice.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function alcove($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    // At the alcove.
    // Player needs to chant opensesame to use it.
    $profile = $this->getKyrandiaProfile($actingPlayer);
    $openSesame = $profile->field_kyrandia_open_sesame->value;

    if ($openSesame) {
      $words = explode(' ', $commandText);
      // Word 0 has to be 'place', otherwise we wouldn't be here.
      // We're looking for 'place key in crevice'.
      $swordPos = array_search('key', $words);
      $rockPos = array_search('crevice', $words);
      if ($swordPos !== FALSE && $rockPos !== FALSE && $swordPos < $rockPos) {
        if ($this->takeItemFromPlayer($actingPlayer, 'key')) {
          if ($this->movePlayer($actingPlayer, 'Location 186')) {
            $result = "As you drop the key into the crevice, a flash of golden light engulfs you, and you feel yourself being magically transported through space...\n";
          }
          // The result is LOOKing at the new location.
          $mudEvent = new CommandEvent($actingPlayer, 'look');
          $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
          $result .= $mudEvent->getResponse();
        }
      }
    }
    if (!$result) {
      $result = "For some reason, nothing happens at all!";
    }
    return $result;
  }

  /**
   * Handles placing the garnet in the slot machine.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function slotMachine($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    // We're looking for 'place garnet in slot'.
    $itemPos = array_search('garnet', $words);
    $slotPos = array_search('slot', $words);
    if ($slotPos !== FALSE) {
      if ($itemPos !== FALSE && $slotPos !== FALSE && $itemPos < $slotPos) {
        if ($this->takeItemFromPlayer($actingPlayer, 'garnet')) {
          // Random result.
          $rand = rand(1, 11);
          if ($rand < 3) {
            $prizes = [
              "ruby",
              "emerald",
              "garnet",
              "pearl",
              "aquamarine",
              "moonstone",
              "sapphire",
              "diamond",
              "amethyst",
              "onyx",
              "opal",
              "bloodstone",
            ];
            $prize = $prizes[array_rand($prizes)];
            $this->giveItemToPlayer($actingPlayer, $prize);
            $result = t("You drop the garnet in the slot, and the machine starts to hum and spin...\n***\nSuddenly a voice booms: \"WE HAVE A WIIIIIIINNNNNNERRRR!\"\nSuddenly, :article :prize appears in your hands!", [
              ':article' => $this->wordGrammar->getIndefiniteArticle($prize),
              ':prize' => $prize,
            ]);
          }
          else {
            $result = "You drop the garnet in the slot, and the machine starts to hum and spin...\n***\nSuddenly a voice booms:  \"WE HAVE A LOOOOOSSSSEEEEERRRRR!\"";
          }
        }
      }
    }
    if (!$result) {
      $result = "For some reason, nothing happens at all!";
    }
    return $result;
  }

  /**
   * Handles placing the dagger in the orb.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function mistyRuins(string $commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    // We're looking for 'place garnet in slot'.
    $itemPos = array_search('dagger', $words);
    $targetPos = array_search('orb', $words);
    if ($itemPos !== FALSE && $targetPos !== FALSE && $itemPos < $targetPos) {
      $profile = $this->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName == '7' && $this->takeItemFromPlayer($actingPlayer, 'dagger')) {
        if ($this->advanceLevel($profile, '8')) {
          $result = "The orb accepts your offer, and glows brightly for a moment!\n***\nYou are now at level 8!";
        }
      }
    }
    if (!$result) {
      $result = "For some reason, nothing happens at all!";
    }
    return $result;
  }

  /**
   * Handles placing the soulstone in the niche.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function demonGate(string $commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    // We're looking for 'place garnet in slot'.
    $itemPos = array_search('soulstone', $words);
    $targetPos = array_search('niche', $words);
    if ($itemPos !== FALSE && $targetPos !== FALSE && $itemPos < $targetPos) {
      $profile = $this->getKyrandiaProfile($actingPlayer);
      if ($this->playerHasItem($actingPlayer, 'soulstone')) {
        $result = $this->getMessage('SOUKEY');
        $this->movePlayer($actingPlayer, 'Location 219');
        // The result is LOOKing at the new location.
        $mudEvent = new CommandEvent($actingPlayer, 'look');
        $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
        $result .= $mudEvent->getResponse();
      }
    }
    if (!$result) {
      $result = "For some reason, nothing happens at all!";
    }
    return $result;
  }

}
