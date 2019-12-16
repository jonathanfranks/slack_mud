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
        $charm = $this->playerHasItem($actingPlayer, 'charm', TRUE);
        if ($charm) {
          $result = $this->getMessage('LVL9M0');
          $this->advanceLevel($profile, 9);
        }
      }
      elseif ($tiaraPos !== FALSE && $tiaraPos < $altarPos && $profile->field_kyrandia_level->entity->getName() == 9) {
        $tiara = $this->playerHasItem($actingPlayer, 'tiara', TRUE);
        if ($tiara) {
          $result = $this->getMessage('LV10M0');
          $this->advanceLevel($profile, 10);
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
        $itemDelta = $this->playerHasItem($actingPlayer, $commandText, TRUE);
        if ($itemDelta !== FALSE) {
          $result = $this->getMessage('OFFER0');
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
  protected function strangeRock($commandText, NodeInterface $actingPlayer) {
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
            $result = $this->getMessage('ROCK00');
          }
        }
        else {
          $result = $this->getMessage('ROCK02');
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
            $result = $this->getMessage('WALM00');
          }
          // The result is LOOKing at the new location.
          $mudEvent = new CommandEvent($actingPlayer, 'look');
          $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
          $result .= $mudEvent->getResponse();
        }
      }
    }
    if (!$result) {
      $result = $this->getMessage('WALM01');
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
            $result = $this->getMessage('SLOT00') . "\n" . sprintf($this->getMessage('SLOT02'), $this->wordGrammar->getIndefiniteArticle($prize) . ' ' . $prize);
          }
          else {
            $result = $this->getMessage('SLOT03');
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
        if ($this->advanceLevel($profile, 8)) {
          $result = $this->getMessage('MISM04');
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
