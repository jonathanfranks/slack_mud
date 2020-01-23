<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Seek command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_joingamefirsttimeintrodisplay",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class JoinGameFirstTimeIntroDisplay extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('INTROA');
    $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('INTROB');
    $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('INTROC');
    $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('INTROD');
  }

}
