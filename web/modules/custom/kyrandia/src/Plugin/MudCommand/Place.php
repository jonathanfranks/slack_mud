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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);

    if ($loc->getTitle() == 'Location 7') {
      $this->temple($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 27') {
      $this->strangeRock($commandText, $actingPlayer, $results);
    }
    elseif ($loc->getTitle() == 'Location 185') {
      $this->alcove($commandText, $actingPlayer, $results);
    }
    elseif ($loc->getTitle() == 'Location 186') {
      $this->slotMachine($commandText, $actingPlayer, $results);
    }
    elseif ($loc->getTitle() == 'Location 188') {
      $this->mistyRuins($commandText, $actingPlayer, $results);
    }
    elseif ($loc->getTitle() == 'Location 218') {
      $this->demonGate($commandText, $actingPlayer, $results);
    }
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
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function temple($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    $game = $actingPlayer->field_game->entity;
    $loc = $actingPlayer->field_location->entity;
    $currentTempleChantCount = $this->gameHandler->getInstanceSetting($game, 'currentTempleChantCount', 0);
    if ($currentTempleChantCount >= 5) {
      $charmPos = array_search('charm', $words);
      $tiaraPos = array_search('tiara', $words);
      $altarPos = array_search('altar', $words);
      if ($charmPos !== FALSE && $charmPos < $altarPos && $profile->field_kyrandia_level->entity->getName() == 8) {
        $charm = $this->gameHandler->playerHasItem($actingPlayer, 'charm', TRUE);
        if ($charm) {
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('LVL9M0');
          $othersMessage = sprintf($this->gameHandler->getMessage('LVL9M1'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          $this->gameHandler->advanceLevel($profile, 9);
        }
      }
      elseif ($tiaraPos !== FALSE && $tiaraPos < $altarPos && $profile->field_kyrandia_level->entity->getName() == 9) {
        $tiara = $this->gameHandler->playerHasItem($actingPlayer, 'tiara', TRUE);
        if ($tiara) {
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('LV10M0');
          $othersMessage = sprintf($this->gameHandler->getMessage('LVL9M1'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          $this->gameHandler->advanceLevel($profile, 10);
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
        $itemDelta = $this->gameHandler->playerHasItem($actingPlayer, $commandText, TRUE);
        if ($itemDelta !== FALSE) {
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('OFFER0');
        }
      }
    }
  }

  /**
   * Handles placing the sword in the rock.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function strangeRock($commandText, NodeInterface $actingPlayer, array &$results) {
    // At the strange rock.
    // It has to have been prayed at.
    $game = $actingPlayer->field_game->entity;
    $rockPrayCount = $this->gameHandler->getInstanceSetting($game, 'currentRockPrayCount', 0);
    $loc = $actingPlayer->field_location->entity;
    if ($rockPrayCount) {
      $words = explode(' ', $commandText);
      // Word 0 has to be 'place', otherwise we wouldn't be here.
      // We're looking for 'place sword in rock'.
      $swordPos = array_search('sword', $words);
      $rockPos = array_search('rock', $words);
      if ($swordPos !== FALSE && $rockPos !== FALSE && $swordPos < $rockPos) {
        if ($this->gameHandler->takeItemFromPlayer($actingPlayer, 'sword')) {
          if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'tiara')) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('ROCK00');
            $othersMessage = sprintf($this->gameHandler->getMessage('ROCK01'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
        }
        else {
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('ROCK02');
          $othersMessage = sprintf($this->gameHandler->getMessage('ROCK01'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
    }
  }

  /**
   * Handles placing the key in the crevice.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function alcove($commandText, NodeInterface $actingPlayer, array &$results) {
    // At the alcove.
    // Player needs to chant opensesame to use it.
    $loc = $actingPlayer->field_location->entity;
    $game = $actingPlayer->field_game->entity;
    $openSesame = $this->gameHandler->getInstanceSetting($game, 'opensesame', 0);
    if ($openSesame) {
      $words = explode(' ', $commandText);
      // Word 0 has to be 'place', otherwise we wouldn't be here.
      // We're looking for 'place key in crevice'.
      $swordPos = array_search('key', $words);
      $rockPos = array_search('crevice', $words);
      if ($swordPos !== FALSE && $rockPos !== FALSE && $swordPos < $rockPos) {
        if ($this->gameHandler->takeItemFromPlayer($actingPlayer, 'key')) {
          if ($this->gameHandler->movePlayer($actingPlayer, 'Location 186', $results, 'vanished in a golden flash of light', 'appeared in a golden flash of light')) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('WALM00');
          }
          // The result is LOOKing at the new location.
          $mudEvent = new CommandEvent($actingPlayer, 'look', $results);
          $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
          $results = $mudEvent->getResponse();
        }
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('WALM01');
      $othersMessage = sprintf($this->gameHandler->getMessage('WALM02'), $actingPlayer->field_display_name->value);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
    }
  }

  /**
   * Handles placing the garnet in the slot machine.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   *
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function slotMachine($commandText, NodeInterface $actingPlayer, array &$results) {
    $result = NULL;
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    // We're looking for 'place garnet in slot'.
    $itemPos = array_search('garnet', $words);
    $slotPos = array_search('slot', $words);
    if ($slotPos !== FALSE) {
      if ($itemPos !== FALSE && $slotPos !== FALSE && $itemPos < $slotPos) {
        if ($this->gameHandler->takeItemFromPlayer($actingPlayer, 'garnet')) {
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
            $this->gameHandler->giveItemToPlayer($actingPlayer, $prize);
            $result = $this->gameHandler->getMessage('SLOT00') . "\n" . sprintf($this->gameHandler->getMessage('SLOT02'), $this->wordGrammar->getIndefiniteArticle($prize) . ' ' . $prize);
          }
          else {
            $result = $this->gameHandler->getMessage('SLOT03');
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
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function mistyRuins(string $commandText, NodeInterface $actingPlayer, array &$results) {
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    // We're looking for 'place garnet in slot'.
    $itemPos = array_search('dagger', $words);
    $targetPos = array_search('orb', $words);
    if ($itemPos !== FALSE && $targetPos !== FALSE && $itemPos < $targetPos) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '7' && $this->gameHandler->takeItemFromPlayer($actingPlayer, 'dagger')) {
        if ($this->gameHandler->advanceLevel($profile, 8)) {
          $this->msgutl2($actingPlayer, 'MISM04', 'MISM05', $results);
        }
      }
    }
  }

  /**
   * Handles placing the soulstone in the niche.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function demonGate(string $commandText, NodeInterface $actingPlayer, array &$results) {
    $result = NULL;
    $words = explode(' ', $commandText);
    // Word 0 has to be 'place', otherwise we wouldn't be here.
    // We're looking for 'place garnet in slot'.
    $itemPos = array_search('soulstone', $words);
    $targetPos = array_search('niche', $words);
    if ($itemPos !== FALSE && $targetPos !== FALSE && $itemPos < $targetPos) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($this->gameHandler->playerHasItem($actingPlayer, 'soulstone')) {
        $result = $this->gameHandler->getMessage('SOUKEY');
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
