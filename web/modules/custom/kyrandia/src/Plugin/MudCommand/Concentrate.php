<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Concentrate command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_concentrate",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Concentrate extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $commandText = str_replace('concentrate', '', $commandText);
    $commandText = str_replace(' on ', '', $commandText);
    $commandText = trim($commandText);
    if ($commandText == 'orb' && $loc->getTitle() == 'Location 188') {
      // Concentrating on the orb in the misty ruins gives the player a charm.
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'item')
        ->condition('field_game.entity.title', 'kyrandia')
        ->condition('title', 'charm');
      $ids = $query->execute();
      if ($ids) {
        $id = reset($ids);
        // @TODO Handling max items for player.
        $actingPlayer->field_inventory[] = ['target_id' => $id];
        $actingPlayer->save();
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MISM01');
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
