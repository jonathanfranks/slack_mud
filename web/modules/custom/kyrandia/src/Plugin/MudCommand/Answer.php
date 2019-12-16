<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Answer command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_answer",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Answer extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 285') {
      $result = $this->time($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 302') {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '24') {
        if ($commandText == 'answer cast the spells and cross the seas, heart, soul, mind, and body are the keys') {
          $dragonHere = $this->gameHandler->isDragonHere($loc);
          if ($dragonHere) {
            $this->gameHandler->advanceLevel($profile, 25);
            $result = $this->gameHandler->getMessage('YOUWIN');
          }
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Answer time.
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
  protected function time($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    // We're looking for "answer time".
    $words = explode(' ', $commandText);
    if (in_array('time', $words)) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      $results = [];
      if ($profile->field_kyrandia_level->entity->getName() == '13') {
        if ($this->gameHandler->advanceLevel($profile, 14)) {
          $results[] = $this->gameHandler->getMessage('MINM01');
          if (!$this->gameHandler->giveItemToPlayer($actingPlayer, 'pendant')) {
            $results[] = $this->gameHandler->getMessage('MIM03');
            // Not enough room? Item limits?
            // Take the first item away to make room.
            $this->gameHandler->removeFirstItem($actingPlayer);
            // And give the broach again.
            $this->gameHandler->giveItemToPlayer($actingPlayer, 'pendant');
          }
        }
      }
      $result = implode("\n", $results);
    }
    return $result;
  }

}
