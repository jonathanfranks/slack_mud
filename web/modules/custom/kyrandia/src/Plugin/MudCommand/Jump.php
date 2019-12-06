<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Jump command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_jump",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Jump extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 282') {
      $result = $this->chasm($commandText, $actingPlayer);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Jumping across the chasm.
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
  protected function chasm($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    // We're looking for "jump chasm" or "jump across chasm".
    $words = explode(' ', $commandText);
    if (in_array('chasm', $words)) {
      $profile = $this->getKyrandiaProfile($actingPlayer);
      $results = [];
      if ($profile->field_kyrandia_level->entity->getName() == '12') {
        // If user is protected.
        $userIsProtected = $profile->field_kyrandia_protection_other->value;
        if ($userIsProtected) {
          if ($this->advanceLevel($profile, 13)) {
            $results[] = $this->getMessage('BODM01');
          }
          if (!$this->giveItemToPlayer($actingPlayer, 'broach')) {
            // Can't give the item to the player - max item limit.
            $results[] = $this->getMessage('BODM03');
            $this->removeFirstItem($actingPlayer);
            // Then give the broach again.
            $this->giveItemToPlayer($actingPlayer, 'broach');
          }
        }
        else {
          // User isn't protected. Death.
          $results[] = $this->getMessage('BODM04');
          $this->damagePlayer($actingPlayer, 100);
        }
      }
      $result = implode("\n", $results);
    }
    return $result;
  }

}
