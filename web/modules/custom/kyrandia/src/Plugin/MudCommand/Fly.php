<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Fly command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_fly",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Fly extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $result = NULL;
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
