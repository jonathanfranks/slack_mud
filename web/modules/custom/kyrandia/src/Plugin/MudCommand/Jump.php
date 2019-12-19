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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 282') {
      $this->chasm($commandText, $actingPlayer, $results);
    }
  }

  /**
   * Jumping across the chasm.
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
  protected function chasm($commandText, NodeInterface $actingPlayer, array &$results) {
    // We're looking for "jump chasm" or "jump across chasm".
    $words = explode(' ', $commandText);
    $loc = $actingPlayer->field_location->entity;
    if (in_array('chasm', $words)) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '12') {
        // If user is protected.
        $userIsProtected = $profile->field_kyrandia_protection_other->value;
        if ($userIsProtected) {
          if ($this->gameHandler->advanceLevel($profile, 13)) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('BODM01');
            $othersMessage = sprintf($this->gameHandler->getMessage('BODM02'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
          if (!$this->gameHandler->giveItemToPlayer($actingPlayer, 'broach')) {
            // Can't give the item to the player - max item limit.
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('BODM03');
            $this->gameHandler->removeFirstItem($actingPlayer);
            // Then give the broach again.
            $this->gameHandler->giveItemToPlayer($actingPlayer, 'broach');
          }
        }
        else {
          // User isn't protected. Death.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('BODM04');
          $othersMessage = sprintf($this->gameHandler->getMessage('BODM05'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          $this->gameHandler->damagePlayer($actingPlayer, 100, $results);
        }
      }
    }
  }

}
