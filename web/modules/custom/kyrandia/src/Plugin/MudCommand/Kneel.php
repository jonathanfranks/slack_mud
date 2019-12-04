<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Kneel command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_kneel",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Kneel extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can kneel at the willow tree at location 0 to go from level 1 to
    // level 2.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 0') {
      // Player is at the willow tree.
      $level = $profile->field_kyrandia_level->entity;
      if ($level->getName() == '1') {
        // Set the player's level to 2.
        // Get the Level 2 term.
        $query = \Drupal::entityQuery('taxonomy_term')
          ->condition('vid', 'kyrandia_level')
          ->condition('name', '2');
        $level_ids = $query->execute();
        $level_id = $level_ids ? reset($level_ids) : NULL;

        $profile->field_kyrandia_level->target_id = $level_id;
        $profile->save();

        $result = "
As you kneel, a vision of the Goddess Tashanna materializes before you.\n
She lays her hand gently upon your shoulder and says to you, \"Rise, rise, Magic-user!  Your first advancement has begun.\"\n
She then vanishes, and you feel yourself grow in power!\n
***\n
You are now at level 2!\n
***\n
A spell has been added to your spellbook!";
      }
    }
    if (!$result) {
      $result = 'You kneel.';
    }
    return $result;
  }

}
