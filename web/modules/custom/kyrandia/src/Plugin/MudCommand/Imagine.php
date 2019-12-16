<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Imagine command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_imagine",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Imagine extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $commandText = str_replace('imagine', '', $commandText);
    $commandText = trim($commandText);
    if ($commandText == 'dagger' && $loc->getTitle() == 'Location 181') {
      // Imagining a dagger at the statue gives the player a dagger.
      $itemName = 'dagger';
      if ($this->gameHandler->giveItemToPlayer($actingPlayer, $itemName)) {
        $result = $this->gameHandler->getMessage('DAGM00');
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
