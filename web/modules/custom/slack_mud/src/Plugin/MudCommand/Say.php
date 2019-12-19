<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Say command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "say",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Say extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // @TODO Handle saying for multiplayer.
    $result = t('You said it!');
    return $result;
  }

}
