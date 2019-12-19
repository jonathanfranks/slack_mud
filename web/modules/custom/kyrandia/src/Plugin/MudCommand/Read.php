<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Read command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_read",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Read extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

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
