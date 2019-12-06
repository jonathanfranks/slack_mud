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
    $profile = $this->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if ($loc->getTitle() == 'Location 24' && $profile->field_kyrandia_level->entity->getName() == '3') {
      // Player is at the silver altar.
      $result = $this->silverAltar($commandText, $actingPlayer, $profile);
    }
    elseif ($loc->getTitle() == 'Location 101' && $profile->field_kyrandia_level->entity->getName() == '6') {
      // Player is at the hidden shrine.
      $result = $this->hiddenShrine($commandText, $profile);
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
          if ($this->advanceLevel($profile, 22)) {
            $result = $this->getMessage('LEVL22');
          }
        }
      }
    }
    elseif ($loc->getTitle() == 'Location 288') {
      if ($profile->field_kyrandia_level->entity->getName() == 14) {
        $words = explode(' ', $commandText);
        if ($profile->field_kyrandia_married_to->entity) {
          $results = [];
          // We're looking for "offer heart to [spouse]".
          $spouse = $profile->field_kyrandia_married_to->entity;
          $spouseName = strtolower($spouse->field_display_name->value);
          $heartPos = array_search('heart', $words);
          $spousePos = array_search($spouseName, $words);
          if ($heartPos !== FALSE && $spousePos !== FALSE && $heartPos < $spousePos) {
            if ($this->advanceLevel($profile, 15)) {
              $results[] = $this->getMessage('HEAR01');
              if (!$this->giveItemToPlayer($actingPlayer, 'locket')) {
                $results[] = $this->getMessage('HEAR03');
                // Couldn't give - item limits?
                $this->removeFirstItem($actingPlayer);
                // Remove first item and give again.
                $this->giveItemToPlayer($actingPlayer, 'locket');
              }
            }
          }
          $result = implode("\n", $results);
        }
      }
    }
    if (!$result) {
      $result = "You can't do that here.";
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
    if (count($profile->field_kyrandia_birth_stones)) {
      // The next birthstone will always be in slot 0, since we remove them
      // when a successful offering is made.
      $firstBirthstone = $profile->field_kyrandia_birth_stones[0]->entity;
      $firstBirthstoneName = $firstBirthstone->getTitle();
      $lastBirthstone = count($profile->field_kyrandia_birth_stones) == 1;

      // Now remove the OFFER and we'll see what they're offering.
      $target = str_replace('offer', '', $commandText);
      $target = trim($target);

      // Player actually has to have the item they're offering.
      $foundSomething = FALSE;
      foreach ($actingPlayer->field_inventory as $delta => $item) {
        $itemName = strtolower(trim($item->entity->getTitle()));
        if (strpos($itemName, $target) === 0) {
          // Item's name starts with the string the user typed.
          // Remove item from inventory.
          unset($actingPlayer->field_inventory[$delta]);
          $actingPlayer->save();

          if ($itemName == $firstBirthstoneName) {
            // This was the right stone. Remove it from the birthstones array.
            $profile->field_kyrandia_birth_stones[0] = NULL;
            if ($lastBirthstone) {
              // This was the last birthstone! The player advances to level 4.
              $level_ids = $this->advanceLevel($profile, 4);

              $result = $this->getMessage('SILVM0');
            }
            else {
              $result = $this->getMessage('SILVM2');
            }
          }
          else {
            $result = $this->getMessage('SILVM4');
          }
          $foundSomething = TRUE;
          break;
        }
      }
      if (!$foundSomething) {
        $result = t("Unfortunately, you don't have that at the moment.");
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
   * @param \Drupal\node\NodeInterface $profile
   *   The user's Kyrandia profile.
   *
   * @return string|null
   *   The result of this action.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function hiddenShrine($commandText, NodeInterface $profile) {
    $result = NULL;
    // We're looking for:
    // "offer heart and soul to tashanna".
    // We really just want heart, then soul, then tashanna.
    $words = explode(' ', $commandText);
    $indexHeart = array_search('heart', $words);
    $indexSoul = array_search('soul', $words);
    $indexTashanna = array_search('tashanna', $words);
    if ($indexHeart < $indexSoul && $indexSoul < $indexTashanna) {
      $result = $this->getMessage('HNSYOU');
      $this->advanceLevel($profile, 7);
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
              $result = $this->getMessage('NOGTCR');
              $playerGold = $playerGold - $offeringGold;
              $profile->field_kyrandia_gold = $playerGold;
              $profile->save();
            }
            else {
              // Player doesn't have enough.
              $result = $this->getMessage('CHEAPO');
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
        $item = $this->playerHasItem($actingPlayer, $words[1], TRUE);
        if ($item) {
          $result = $this->getMessage('TAKROS');
          // Healer adds 10 HP.
          $this->healPlayer($actingPlayer, 10);
        }
        else {
          // Player doesn't have a rose.
          $result = $this->getMessage('NOHAVE');
        }
      }
      else {
        // Player offered something else.
        $result = $this->getMessage('NOGOOD');
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
    $result = NULL;
    $words = explode(' ', $commandText);
    $itemPos = array_search('kyragem', $words);
    if ($itemPos !== FALSE) {
      $profile = $this->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '11' && $this->playerHasItem($actingPlayer, 'kyragem')) {
        if ($this->advanceLevel($profile, '12')) {
          $result = $this->getMessage('SUNM03');
        }
      }
    }
    if (!$result) {
      $result = "For some reason, nothing happens at all!";
    }
    return $result;
  }

}
