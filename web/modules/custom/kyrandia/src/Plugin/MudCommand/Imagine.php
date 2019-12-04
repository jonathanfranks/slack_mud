<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    $profile = $this->getKyrandiaProfile($actingPlayer);
    $commandText = str_replace('imagine', '', $commandText);
    $commandText = trim($commandText);
    if ($commandText == 'dagger' && $loc->getTitle() == 'Location 181') {
      // Imagining a dagger at the statue gives the player a dagger.
      $itemName = 'dagger';
      if ($this->giveItemToPlayer($actingPlayer, $itemName)) {
        $result = "As you concentrate upon your wish, the Goddess Tashanna intervenes with her magic, and a dagger appears in your hands!";
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
