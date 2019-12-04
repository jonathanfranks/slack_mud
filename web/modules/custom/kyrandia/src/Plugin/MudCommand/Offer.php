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
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

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
                $query = \Drupal::entityQuery('taxonomy_term')
                  ->condition('vid', 'kyrandia_level')
                  ->condition('name', '4');
                $level_ids = $query->execute();
                $level_id = $level_ids ? reset($level_ids) : NULL;
                $profile->field_kyrandia_level->target_id = $level_id;
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
              $profile->save();
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
    }
    if (!$result) {
      $result = "You can't do that here.";
    }
    return $result;
  }

}
