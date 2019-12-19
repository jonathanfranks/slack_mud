<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Push command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_push",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Push extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

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
