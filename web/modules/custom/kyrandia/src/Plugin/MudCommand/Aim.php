<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Aim command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_aim",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Aim extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 201') {
      $this->crystalTree($commandText, $actingPlayer, $results);
    }
    if (!$results) {
      $words = explode(' ', $commandText);
      if (count($words) == 1) {
        // Just typed "aim".
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('OBJM03');
        $othersMessage = t(':actor is pointing wildly.', [
          ':actor' => $actingPlayer->field_display_name->value,
        ]);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $target = $words[1];
        $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
        if ($this->gameHandler->playerHasItem($actingPlayer, $target)) {
          // The "at" is removed, so "aim wand at jeff" turns into
          // "aim wand jeff".
          if (count($words) < 3) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('OBJM05');
            $othersMessage = t(':actor is waving :possessive arms.', [
              ':actor' => $actingPlayer->field_display_name->value,
              ':possessive' => $profile->field_kyrandia_is_female->value ? 'her' : 'his',
            ]);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
          else {
            $targetPlayerName = $words[2];
            if ($targetPlayer = $this->gameHandler->locationHasPlayer($targetPlayerName, $loc, TRUE, $actingPlayer)) {
              // Handle aimable flag.
              $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('OBJM04');
              $othersMessage = t(':actor is waving obscenely!', [
                ':actor' => $actingPlayer->field_display_name->value,
              ]);
              $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
            }
            else {
              // Aiming at a player who isn't there.
              $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('OBJM06');
              $othersMessage = t(':actor is seeing ghosts!', [
                ':actor' => $actingPlayer->field_display_name->value,
              ]);
              $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
            }
          }
        }
        else {
          // Doesn't have whatever they're aiming.
          // @TODO Profanity handler.
          $profane = TRUE;
          if ($profane) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('OBJM01');
            $othersMessage = t(':actor is playing with :possessive body parts!', [
              ':actor' => $actingPlayer->field_display_name->value,
              ':possessive' => $profile->field_kyrandia_is_female->value ? 'her' : 'his',
            ]);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
          else {
            $this->gameHandler->targetNonExistantItem($actingPlayer, $results);
          }
        }
      }
    }
  }

  /**
   * Handles aiming the wand at the tree.
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
  protected function crystalTree(string $commandText, NodeInterface $actingPlayer, array &$results) {
    $words = explode(' ', $commandText);
    // Word 0 has to be 'aim', otherwise we wouldn't be here.
    // We're looking for 'aim wand at tree'.
    $itemPos = array_search('wand', $words);
    $targetPos = array_search('tree', $words);
    if ($itemPos !== FALSE && $targetPos !== FALSE && $itemPos < $targetPos) {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '10' && $this->gameHandler->playerHasItem($actingPlayer, 'wand')) {
        if ($this->gameHandler->advanceLevel($profile, 11)) {
          $loc = $actingPlayer->field_location->entity;
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('CTREM0');
          $othersMessage = sprintf($this->gameHandler->getMessage('CTREM1'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('WALM01');
    }
  }

}
