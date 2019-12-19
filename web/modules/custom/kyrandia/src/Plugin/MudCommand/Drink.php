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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
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
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('DRINK0');
        $othersMessage = sprintf($this->gameHandler->getMessage('DRINK1'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
