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
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = [];

    $words = explode(' ', $commandText);
    if (count($words) == 1) {
      $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('COUNTR1');
    }
    elseif ($words[1] == 'gold') {
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('kyrandia_gold');
      $result = $plugin->perform($commandText, $actingPlayer);
    }
    else {
      $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('COUNTR2');
    }
    return $result;
  }

}
