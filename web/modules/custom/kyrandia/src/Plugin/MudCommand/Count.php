<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Count command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_count",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Count extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $words = explode(' ', $commandText);
    if (count($words) == 1) {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('COUNTR1');
    }
    elseif ($words[1] == 'gold') {
      $this->performAnotherAction('gold', $actingPlayer, $results);
    }
    else {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('COUNTR2');
    }
  }

}
