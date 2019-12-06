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
    $profile = $this->getKyrandiaProfile($actingPlayer);
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
        $result = t("You find :found pieces of gold in the brook!", [':found' => $digGold]);
        $profile->field_kyrandia_gold->value += $digGold;
        $profile->save();
      }
      else {
        $result = "You search the brook, but don't find anything this time.";
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
        $result = t("You dig through the sand and happen to find a piece of gold!");
        $profile->field_kyrandia_gold->value += 1;
        $profile->save();
      }
      else {
        $result = "You dig through the sand, but you find nothing of interest!";
      }
    }
    return $result;
  }

}
