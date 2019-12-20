<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Offer command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_offer",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Offer extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Players offer their birthstones, in order, at the silver altar to reach
    // level 4. Item is removed from player's inventory whether correct or not.
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if ($loc->getTitle() == 'Location 24' && $profile->field_kyrandia_level->entity->getName() == '3') {
      // Player is at the silver altar.
      $this->silverAltar($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 101' && $profile->field_kyrandia_level->entity->getName() == '6') {
      // Player is at the hidden shrine.
      $this->hiddenShrine($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 7') {
      // Player is in the temple. We handle offering gold here.
      $this->temple($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 10') {
      // Offering at the healer shop.
      $this->healer($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 38') {
      // Blessing at the fountain.
      $this->fountain($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 213') {
      $this->sunshineKyragem($commandText, $actingPlayer, $results);
    }
    elseif ($loc->getTitle() == 'Location 255') {
      $this->hallOfHearts($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 288') {
      $this->chamberOfTheHeart($commandText, $actingPlayer, $profile, $results);
    }
  }

  /**
   * Offering at silver altar.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player acting.
   * @param \Drupal\node\NodeInterface $profile
   *   The player's Kyrandia profile.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function silverAltar($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if (count($profile->field_kyrandia_birth_stones)) {
      // The next birthstone will always be in slot 0, since we remove them
      // when a successful offering is made.
      $firstBirthstone = $profile->field_kyrandia_birth_stones[0]->entity;
      $firstBirthstoneName = $firstBirthstone->getTitle();
      $lastBirthstone = count($profile->field_kyrandia_birth_stones) == 1;

      // Now remove the OFFER and we'll see what they're offering.
      $target = str_replace('offer', '', $commandText);
      $target = trim($target);

      // Take the item the player is offering.
      if ($item = $this->gameHandler->playerHasItem($actingPlayer, $target, TRUE)) {
        if ($item->getTitle() == $firstBirthstoneName) {
          if ($lastBirthstone) {
            // Last one! Player gets a level and a spell.
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SILVM0');
            $othersMessage = sprintf($this->gameHandler->getMessage('SILVM1'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
            $this->gameHandler->advanceLevel($profile, 4);
            $this->gameHandler->giveSpellToPlayer($actingPlayer, 'hotseat');
          }
          else {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SILVM2');
            $othersMessage = sprintf($this->gameHandler->getMessage('SILVM3'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
            // Remove birthstone from player's profile.
            $profile->field_kyrandia_birth_stones[0] = NULL;
            $profile->save();
          }
        }
        else {
          // Not the next birthstone.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SILVM4');
          $othersMessage = sprintf($this->gameHandler->getMessage('SILVM3'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
      else {
        // Player doesn't actually have it.
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('TRDM05');
        $othersMessage = sprintf($this->gameHandler->getMessage('SILVM5'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
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
  protected function getTarget($commandText) {
    $target = str_replace('to', '', $commandText);
    $target = str_replace('and', '', $target);
    // Now remove the OFFER and we'll see what they're offering.
    $target = str_replace('offer', '', $target);
    $target = trim($target);
    return $target;
  }

  /**
   * Offer at hidden shrine (101).
   *
   * @param string $commandText
   *   Command text the user entered.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The user's Kyrandia profile.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function hiddenShrine($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    // We're looking for:
    // "offer heart and soul to tashanna".
    // We really just want heart, then soul, then tashanna.
    $words = explode(' ', $commandText);
    $indexHeart = array_search('heart', $words);
    $indexSoul = array_search('soul', $words);
    $indexTashanna = array_search('tashanna', $words);
    if ($indexHeart < $indexSoul && $indexSoul < $indexTashanna) {
      $loc = $actingPlayer->field_location->entity;
      $this->gameHandler->advanceLevel($profile, 7);
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HNSYOU');
      $othersMessage = sprintf($this->gameHandler->getMessage('HNSOTH'), $actingPlayer->field_display_name->value);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      $this->gameHandler->giveSpellToPlayer($actingPlayer, 'weewillo');
    }
  }

  /**
   * Offering gold in the temple.
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
    if (strpos($commandText, 'gold') !== FALSE) {
      $loc = $actingPlayer->field_location->entity;
      // Assume this is offer # gold.
      $words = explode(' ', $commandText);
      if (count($words) > 2) {
        if (is_numeric($words[1])) {
          $offeringGold = $words[1];
          if ($offeringGold > 0) {
            $playerGold = $profile->field_kyrandia_gold->value;
            if ($playerGold >= $offeringGold) {
              $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('NOGTCR');
              $othersMessage = sprintf($this->gameHandler->getMessage('OGETCR'), $actingPlayer->field_display_name->value);
              $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
              $playerGold = $playerGold - $offeringGold;
              $profile->field_kyrandia_gold = $playerGold;
              $profile->save();
            }
            else {
              // Player doesn't have enough.
              $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('CHEAPO');
              $this->sndutl($actingPlayer, 'looking somewhat cheap.', $results);
            }
          }
        }
      }
    }
  }

  /**
   * Offering at the healer.
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
  protected function healer($commandText, NodeInterface $actingPlayer, NodeInterface $profile) {
    // We only care about 'offer rose...'. So we'll check word 1.
    $words = explode(' ', $commandText);
    if (count($words) > 1) {
      if ($words[1] == 'rose') {
        $item = $this->gameHandler->playerHasItem($actingPlayer, $words[1], TRUE);
        if ($item) {
          $result = $this->gameHandler->getMessage('TAKROS');
          // Healer adds 10 HP.
          $this->gameHandler->healPlayer($actingPlayer, 10);
        }
        else {
          // Player doesn't have a rose.
          $result = $this->gameHandler->getMessage('NOHAVE');
        }
      }
      else {
        // Player offered something else.
        $result = $this->gameHandler->getMessage('NOGOOD');
      }
    }
    return $result;
  }

  /**
   * Handles offering the kyragem at the alter of eternal sunshine.
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
  protected function sunshineKyragem(string $commandText, NodeInterface $actingPlayer, array &$results) {
    $words = explode(' ', $commandText);
    $itemPos = array_search('kyragem', $words);
    if ($itemPos !== FALSE) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '11' && $this->gameHandler->playerHasItem($actingPlayer, 'kyragem')) {
        if ($this->gameHandler->advanceLevel($profile, 12)) {
          $this->msgutl2($actingPlayer, 'SUNM03', 'SUNM04', $results);
        }
      }
    }
  }

  /**
   * Get blessing at the fountain.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The profile.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function fountain($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    // We're looking for 'offer true love to tashanna', but the 'to' is
    // stripped out.
    if ($commandText == 'offer true love tashanna') {
      $profile->field_kyrandia_blessed = TRUE;
      $profile->save();
      $results[$actingPlayer->id()][] = 'The Goddess blesses you.';
    }
  }

  /**
   * Offer heart to spouse in chamber of the heart for level 15.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The Kyrandia profile of the actor.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function chamberOfTheHeart($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    if ($profile->field_kyrandia_level->entity->getName() == 14) {
      $words = explode(' ', $commandText);
      if ($profile->field_kyrandia_married_to->entity) {
        // We're looking for "offer heart to [spouse]".
        $spouse = $profile->field_kyrandia_married_to->entity;
        $spouseName = strtolower($spouse->field_display_name->value);
        $heartPos = array_search('heart', $words);
        $spousePos = array_search($spouseName, $words);
        if ($heartPos !== FALSE && $spousePos !== FALSE && $heartPos < $spousePos) {
          if ($this->gameHandler->advanceLevel($profile, 15)) {
            $this->msgutl2($actingPlayer, 'HEAR01', 'HEAR02', $results);
            if (!$this->gameHandler->giveItemToPlayer($actingPlayer, 'locket')) {
              $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HEAR03');
              // Couldn't give - item limits?
              $this->gameHandler->removeFirstItem($actingPlayer);
              // Remove first item and give again.
              $this->gameHandler->giveItemToPlayer($actingPlayer, 'locket');
            }
          }
        }
      }
    }
  }

  /**
   * Offer love in hall of hearts for level 22.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The actor's Kyrandia profile.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function hallOfHearts($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    $words = explode(' ', $commandText);
    if (in_array('love', $words)) {
      if ($profile->field_kyrandia_level->entity->getName() == '21') {
        if ($this->gameHandler->advanceLevel($profile, 22)) {
          $this->msgutl2($actingPlayer, 'LEVL22', 'LVL9M1', $results);
        }
      }
    }
  }

}
