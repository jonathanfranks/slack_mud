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
    // Players can dig in the brook to randomly find gold.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 285') {
      $result = $this->time($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 302') {
      $profile = $this->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '24') {
        if ($commandText == 'answer cast the spells and cross the seas, heart, soul, mind, and body are the keys') {
          $dragonHere = $this->isDragonHere($loc);
          if ($dragonHere) {
            $this->advanceLevel($profile, 25);
            $result = $this->getMessage('YOUWIN');
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
      $profile = $this->getKyrandiaProfile($actingPlayer);
      $results = [];
      if ($profile->field_kyrandia_level->entity->getName() == '13') {
        if ($this->advanceLevel($profile, 14)) {
          $results[] = $this->getMessage('MINM01');
          if (!$this->giveItemToPlayer($actingPlayer, 'pendant')) {
            $results[] = $this->getMessage('MIM03');
            // Not enough room? Item limits?
            // Take the first item away to make room.
            $this->removeFirstItem($actingPlayer);
            // And give the broach again.
            $this->giveItemToPlayer($actingPlayer, 'pendant');
          }
        }
      }
      $result = implode("\n", $results);
    }
    return $result;
  }

}
