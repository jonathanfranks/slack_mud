<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Drupal\slack_mud\Plugin\MudCommand\MudCommandPluginBase;

/**
 * Defines a base MudCommand plugin implementation.
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
abstract class KyrandiaCommandPluginBase extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * Gets the Kyrandia profile node for the given player node.
   *
   * @param \Drupal\node\NodeInterface $targetPlayer
   *   The player.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The player's Kyrandia profile node.
   */
  protected function getKyrandiaProfile(NodeInterface $targetPlayer) {
    // @TODO: Service-ize this.
    $kyrandiaProfile = NULL;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'kyrandia_profile')
      ->condition('field_player.target_id', $targetPlayer->id());
    $kyrandiaProfileNids = $query->execute();
    if ($kyrandiaProfileNids) {
      $kyrandiaProfileNid = reset($kyrandiaProfileNids);
      $kyrandiaProfile = Node::load($kyrandiaProfileNid);
    }
    return $kyrandiaProfile;
  }

  /**
   * Advance the profile to the specified level.
   *
   * @param \Drupal\node\NodeInterface $profile
   *   Acting player.
   * @param int $level
   *   Level to advance to.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function advanceLevel(NodeInterface $profile, $level) {
    // Set the player's level to 6.
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'kyrandia_level')
      ->condition('name', '6');
    $level_ids = $query->execute();
    $level_id = $level_ids ? reset($level_ids) : NULL;

    $profile->field_kyrandia_level->target_id = $level_id;
    $profile->save();
  }

}
