<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Rub command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_rub",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Rub extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can dig in the brook to randomly find gold.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;


    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
