<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Glory command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_glory",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Glory extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players say a command at the temple to get to level 3.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if ($commandText == 'glory be tashanna' && $loc->getTitle() == 'Location 7') {
      // Player is at the temple.
      $level = $profile->field_kyrandia_level->entity;
      if ($level->getName() == '2') {
        // Set the player's level to 3.
        // Get the Level 3 term.
        $query = \Drupal::entityQuery('taxonomy_term')
          ->condition('vid', 'kyrandia_level')
          ->condition('name', '3');
        $level_ids = $query->execute();
        $level_id = $level_ids ? reset($level_ids) : NULL;

        $profile->field_kyrandia_level->target_id = $level_id;
        $profile->save();

        $result = "
As you praise the Goddess Tashanna, you feel yourself grow in power!\n
***\n
You are now at level 3!";
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
