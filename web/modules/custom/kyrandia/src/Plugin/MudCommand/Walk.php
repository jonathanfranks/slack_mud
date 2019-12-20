<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Walk command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_walk",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Walk extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 19') {
      $words = explode(' ', $commandText);
      $synonyms = [
        'thicket',
      ];
      $synonymMatch = array_intersect($synonyms, $words);
      if ($synonymMatch) {
        // If player walks through thicket, they are damaged for 10 hp.
        $this->gameHandler->damagePlayer($actingPlayer, 10, $results);
        $results[$actingPlayer->id()][] = t("...Ouch!\n");
        $this->sndutl($actingPlayer, "burning in the flaming thicket!", $results);
      }
    }
  }

}
