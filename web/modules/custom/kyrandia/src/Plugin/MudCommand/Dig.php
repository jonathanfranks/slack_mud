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
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can dig in the brook to randomly find gold.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 12') {
      $result = $this->brook($commandText, $profile);
    }
    elseif ($loc->getTitle() == 'Location 189') {
      $result = $this->sand($commandText, $profile);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Handles digging for gold in the brook.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $profile
   *   The player profile.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function brook($commandText, NodeInterface $profile) {
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
      $digGold = rand(2, 102);
      $playerGold = $profile->field_kyrandia_gold->value;
      if ($digGold < 10 && $playerGold < 101) {
        $result = sprintf($this->gameHandler->getMessage('FNDGOL'), $digGold);
        $profile->field_kyrandia_gold->value += $digGold;
        $profile->save();
      }
      else {
        $result = $this->gameHandler->getMessage('NOFNDG');
      }
    }
    return $result;
  }

  /**
   * Handles digging for gold in the sand.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $profile
   *   The player profile.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function sand($commandText, NodeInterface $profile) {
    $words = explode(' ', $commandText);
    $synonyms = [
      'sand',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      // Player has to have less than 101 gold and random number has to be
      // less than 10.
      $digGold = rand(0, 100);
      $playerGold = $profile->field_kyrandia_gold->value;
      if ($digGold < 10) {
        $result = $this->gameHandler->getMessage('SANM00');
        $profile->field_kyrandia_gold->value += 1;
        $profile->save();
      }
      else {
        $result = $this->gameHandler->getMessage('SANM02');
      }
    }
    return $result;
  }

}
