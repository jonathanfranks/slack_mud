<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Hits command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_hits",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Hits extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $loc = $actingPlayer->field_location->entity;
    if ($profile) {
      $currentHits = $profile->field_kyrandia_hit_points->value;
      $maxHits = $profile->field_kyrandia_max_hit_points->value;
      $results[$actingPlayer->id()][] = sprintf($this->gameHandler->getMessage('HITCTR'), $currentHits, $maxHits);
      $this->sndutl($actingPlayer, 'is checking %s health.', $results);
    }
  }

}
