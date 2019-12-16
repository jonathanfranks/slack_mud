<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kyrandia-specific Say command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_say",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Say extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 183' && $commandText == 'say legends pass and time goes by but true love will never die') {
      if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'key')) {
        $result = $this->gameHandler->getMessage('PANM00');
      }
      else {
        $result = $this->gameHandler->getMessage('PANM02');
      }
    }
    else {
      // Not a special say. Handle this like a regular say.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('say');
      $result = $plugin->perform($commandText, $actingPlayer);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
