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

              $result = "
As you offer your fourth birthstone to the Goddess Tashanna, you feel a powerful surge of magical energy course through your body!\n
***\n
You are now at level 4!\n
***\n
A spell has been added to your spellbook!";
            }
            else {
              $result = "The Goddess Tashanna accepts the offer of your birthstone! You feel the urge to complete the offering with the rest of your birthstones.";
            }
          }
          else {
            $result = "The Goddess accepts your offer, but in your soul you realize that your offering was not one of your birthstones, or was out of sequence.";
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
      $result = "As you offer your heart and soul, the most precious jewels of your life, you feel the hand of the Goddess Tashanna bless you with power.\n
***\n
You are now at level 7!\n
***\n
A spell has been added to your spellbook!";
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
  protected function temple($commandText, NodeInterface $profile): string {
    if (strpos($commandText, 'gold') !== FALSE) {
      // Assume this is offer # gold.
      $words = explode(' ', $commandText);
      if (count($words) > 2) {
        if (is_numeric($words[1])) {
          $offeringGold = $words[1];
          if ($offeringGold > 0) {
            $playerGold = $profile->field_kyrandia_gold->value;
            if ($playerGold >= $offeringGold) {
              $result = "As you make you offering, a flash of lightning streaks from the sky and strikes your offered gold, disintegrating it!\n
***\n
The Goddess Tashanna appears to you and graciously thanks you for your wonderful sacrifice.";
              $playerGold = $playerGold - $offeringGold;
              $profile->field_kyrandia_gold = $playerGold;
              $profile->save();
            }
            else {
              // Player doesn't have enough.
              $result = "Unfortunately, you do not have that in gold. Best beware of making false offers to the gods!";
            }
          }
        }
      }
    }
    return $result;
  }

  /**
   * @param $commandText
   * @param \Drupal\node\NodeInterface $actingPlayer
   * @param \Drupal\node\NodeInterface $profile
   *
   * @return string
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function healer($commandText, NodeInterface $actingPlayer, NodeInterface $profile): string {
    // We only care about 'offer rose...'. So we'll check word 1.
    $words = explode(' ', $commandText);
    if (count($words) > 1) {
      if ($words[1] == 'rose') {
        $invDelta = $this->playerHasItem($actingPlayer, $words[1]);
        if ($invDelta !== FALSE) {
          $result = "The healer smiles at you and humbly accepts you beautiful offer. She then touches her palm to your forehead and utters a strange incantation.";
          // Remove item from inventory.
          unset($actingPlayer->field_inventory[$invDelta]);
          $actingPlayer->save();
          // Healer adds 10 HP.
          $healedHp = $profile->field_kyrandia_hit_points->value + 10;
          if ($healedHp > $profile->field_kyrandia_max_hit_points->value) {
            $healedHp = $profile->field_kyrandia_max_hit_points->value;
          }
          $profile->field_kyrandia_hit_points = $healedHp;
          $profile->save();
        }
        else {
          // Player doesn't have a rose.
          $result = "Unfortunately, you don't have a rose.  The healer smiles at you, but does nothing.";
        }
      }
      else {
        // Player offered something else.
        $result = "The healer denies your offer and tells you to be less materialistic; to appreciate and respect more of the beauty of nature.";
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
