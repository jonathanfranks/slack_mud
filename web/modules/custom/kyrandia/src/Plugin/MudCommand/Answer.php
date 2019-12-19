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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 285') {
      $this->time($commandText, $actingPlayer, $results);
    }
    elseif ($loc->getTitle() == 'Location 302') {
      $this->winGame($commandText, $actingPlayer, $loc, $results);
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

  /**
   * Answer time.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $result
   *   The result array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function time($commandText, NodeInterface $actingPlayer, array &$result) {
    // We're looking for "answer time".
    $words = explode(' ', $commandText);
    if (in_array('time', $words)) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '13') {
        if ($this->gameHandler->advanceLevel($profile, 14)) {
          $loc = $actingPlayer->field_location->entity;
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('MINM01');
          $othersMessage = sprintf($this->gameHandler->getMessage('MINM02'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
          if (!$this->gameHandler->giveItemToPlayer($actingPlayer, 'pendant')) {
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('MINM03');
            // Not enough room? Item limits?
            // Take the first item away to make room.
            $this->gameHandler->removeFirstItem($actingPlayer);
            // And give the broach again.
            $this->gameHandler->giveItemToPlayer($actingPlayer, 'pendant');
          }
        }
      }
    }
  }

  /**
   * Win the game with this command.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $loc
   *   The location.
   * @param array $result
   *   The result array.
   */
  protected function winGame($commandText, NodeInterface $actingPlayer, NodeInterface $loc, array &$result) {
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($profile->field_kyrandia_level->entity->getName() == '24') {
      if ($commandText == 'answer cast the spells and cross the seas, heart, soul, mind, and body are the keys') {
        $dragonHere = $this->gameHandler->isDragonHere($loc);
        if ($dragonHere) {
          $this->gameHandler->advanceLevel($profile, 25);
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('YOUWIN');
          $othersMessage = sprintf($this->gameHandler->getMessage('SHEWON'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
      }
    }
  }

}
