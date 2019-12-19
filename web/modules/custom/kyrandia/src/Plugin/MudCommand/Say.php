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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 183' && $commandText == 'say legends pass and time goes by but true love will never die') {
      if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'key')) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PANM00');
        $othersMessage = sprintf($this->gameHandler->getMessage('PANM01'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PANM02');
        $othersMessage = sprintf($this->gameHandler->getMessage('PANM01'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
    else {
      // Not a special say. Handle this like a regular say.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('say');
      $plugin->perform($commandText, $actingPlayer, $results);
    }
  }

}
