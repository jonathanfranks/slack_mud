<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Dig command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_dig",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Dig extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Players can dig in the brook to randomly find gold.
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 12') {
      $results[$actingPlayer->id()][] = $this->brook($commandText, $actingPlayer, $profile, $results);
    }
    elseif ($loc->getTitle() == 'Location 189') {
      $results[$actingPlayer->id()][] = $this->sand($commandText, $actingPlayer, $profile, $results);
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

  /**
   * Handles digging for gold in the brook.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The player profile.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function brook($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    $words = explode(' ', $commandText);
    $synonyms = [
      'gold',
      'brook',
      'water',
      'stream',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      // Player has to have less than 101 gold and random number has to be
      // less than 10.
      $game = $actingPlayer->field_game->entity;
      $digGold = $this->generateRandomNumber($game, 2, 102);
      $playerGold = $profile->field_kyrandia_gold->value;
      if ($digGold < 10 && $playerGold < 101) {
        $results[$actingPlayer->id()][] = sprintf($this->gameHandler->getMessage('FNDGOL'), $digGold);
        $profile->field_kyrandia_gold->value += $digGold;
        $profile->save();
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('NOFNDG');
      }
      $this->sndutl($actingPlayer, 'searching the brook for something.', $results);
    }
  }

  /**
   * Handles digging for gold in the sand.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The player profile.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function sand($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    $words = explode(' ', $commandText);
    $synonyms = [
      'sand',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      $game = $actingPlayer->field_game->entity;
      $digGold = $this->generateRandomNumber($game, 0, 100);
      $playerGold = $profile->field_kyrandia_gold->value;
      if ($digGold < 10) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SANM00');
        $profile->field_kyrandia_gold->value += 1;
        $profile->save();
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SANM02');
      }
      $loc = $actingPlayer->field_location->entity;
      $othersMessage = sprintf($this->gameHandler->getMessage('SANM01'), $actingPlayer->field_display_name->value);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
    }
  }

}
