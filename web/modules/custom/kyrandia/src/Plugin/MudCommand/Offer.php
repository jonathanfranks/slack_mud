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
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players offer their birthstones, in order, at the silver altar to reach
    // level 4. Item is removed from player's inventory whether correct or not.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if ($loc->getTitle() == 'Location 24' && $profile->field_kyrandia_level->entity->getName() == '3') {
      // Player is at the silver altar.
      $result = $this->silverAltar($commandText, $actingPlayer, $profile);
    }
    elseif ($loc->getTitle() == 'Location 101' && $profile->field_kyrandia_level->entity->getName() == '6') {
      // Player is at the hidden shrine.
      $result = $this->hiddenShrine($commandText, $actingPlayer, $profile);
    }
    elseif ($loc->getTitle() == 'Location 7') {
      // Player is in the temple. We handle offering gold here.
      $result = $this->temple($commandText, $profile);
    }
    elseif ($loc->getTitle() == 'Location 10') {
      // Offering at the healer shop.
      $result = $this->healer($commandText, $actingPlayer, $profile);
    }
    elseif ($loc->getTitle() == 'Location 38') {
      // Blessing at the fountain.
      // We're looking for 'offer true love to tashanna', but the 'to' is
      // stripped out.
      if ($commandText == 'offer true love tashanna') {
        $profile->field_kyrandia_blessed = TRUE;
        $profile->save();
        $result = 'The Goddess blesses you.';
      }
    }
    elseif ($loc->getTitle() == 'Location 213') {
      $result = $this->sunshineKyragem($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 255') {
      $words = explode(' ', $commandText);
      if (in_array('love', $words)) {
        if ($profile->field_kyrandia_level->entity->getName() == '21') {
          if ($this->gameHandler->advanceLevel($profile, 22)) {
            $result = $this->gameHandler->getMessage('LEVL22');
          }
        }
      }
    }
    elseif ($loc->getTitle() == 'Location 288') {
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
              $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HEAR01');
              $othersMessage = sprintf($this->gameHandler->getMessage('HEAR02'), $actingPlayer->field_display_name->value);
              $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
              if (!$this->gameHandler->giveItemToPlayer($actingPlayer, 'locket')) {
                $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HEAR03');
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
    if (!$result) {
      $result[$actingPlayer->id()][] = "You can't do that here.";
    }
    return $result;
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
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function silverAltar($commandText, NodeInterface $actingPlayer, NodeInterface $profile) {
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
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('SILVM0');
            $othersMessage = sprintf($this->gameHandler->getMessage('SILVM1'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
            $this->gameHandler->advanceLevel($profile, 4);
            $this->gameHandler->giveSpellToPlayer($actingPlayer, 'hotseat');
          }
          else {
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('SILVM2');
            $othersMessage = sprintf($this->gameHandler->getMessage('SILVM3'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
            // Remove birthstone from player's profile.
            $profile->field_kyrandia_birth_stones[0] = NULL;
            $profile->save();
          }
        }
        else {
          // Not the next birthstone.
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('SILVM4');
          $othersMessage = sprintf($this->gameHandler->getMessage('SILVM3'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
      }
      else {
        // Player doesn't actually have it.
        $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('TRDM05');
        $othersMessage = sprintf($this->gameHandler->getMessage('SILVM5'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
      }
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
   *
   * @return string|null
   *   The result of this action.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function hiddenShrine($commandText, NodeInterface $actingPlayer, NodeInterface $profile) {
    $result = [];
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
      $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HNSYOU');
      $othersMessage = sprintf($this->gameHandler->getMessage('HNSOTH'), $actingPlayer->field_display_name->value);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
      $this->gameHandler->giveSpellToPlayer($actingPlayer, 'weewillo');
    }
    return $result;
  }

  /**
   * Offering gold in the temple.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $profile
   *   The player's Kyrandia profile.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function temple($commandText, NodeInterface $profile) {
    if (strpos($commandText, 'gold') !== FALSE) {
      // Assume this is offer # gold.
      $words = explode(' ', $commandText);
      if (count($words) > 2) {
        if (is_numeric($words[1])) {
          $offeringGold = $words[1];
          if ($offeringGold > 0) {
            $playerGold = $profile->field_kyrandia_gold->value;
            if ($playerGold >= $offeringGold) {
              $result = $this->gameHandler->getMessage('NOGTCR');
              $playerGold = $playerGold - $offeringGold;
              $profile->field_kyrandia_gold = $playerGold;
              $profile->save();
            }
            else {
              // Player doesn't have enough.
              $result = $this->gameHandler->getMessage('CHEAPO');
            }
          }
        }
      }
    }
    return $result;
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
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function sunshineKyragem(string $commandText, NodeInterface $actingPlayer) {
    $result = [];
    $words = explode(' ', $commandText);
    $itemPos = array_search('kyragem', $words);
    if ($itemPos !== FALSE) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '11' && $this->gameHandler->playerHasItem($actingPlayer, 'kyragem')) {
        if ($this->gameHandler->advanceLevel($profile, 12)) {
          $loc = $actingPlayer->field_location->entity;
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('SUNM03');
          $othersMessage = sprintf($this->gameHandler->getMessage('SUNM04'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
      }
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = "For some reason, nothing happens at all!";
    }
    return $result;
  }

}
