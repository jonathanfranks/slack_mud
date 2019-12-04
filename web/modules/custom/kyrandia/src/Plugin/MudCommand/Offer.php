<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    if (!$result) {
      $result = "You can't do that here.";
    }
    return $result;
  }

  /**
   * @param $commandText
   * @param \Drupal\node\NodeInterface $actingPlayer
   * @param \Drupal\node\NodeInterface $profile
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
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

}
