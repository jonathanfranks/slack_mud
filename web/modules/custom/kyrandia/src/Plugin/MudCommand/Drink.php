<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Drink command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_drink",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Drink extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $drinkableLocations = [
      'Location 12',
      'Location 32',
      'Location 35',
      'Location 36',
      'Location 38',
    ];
    if (in_array($loc->getTitle(), $drinkableLocations)) {
      $words = explode(' ', $commandText);
      $synonyms = [
        'water',
      ];
      $synonymMatch = array_intersect($synonyms, $words);
      if ($synonymMatch) {
        $result = $this->gameHandler->getMessage('DRINK0');
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
