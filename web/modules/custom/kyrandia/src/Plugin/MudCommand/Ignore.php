<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Ignore command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_ignore",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Ignore extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 291' && $commandText == 'ignore time') {
      $results = [];
      $results[] = $this->getMessage('SOUL01');
      if ($this->advanceLevel($profile, 16)) {
        if (!$this->giveItemToPlayer($actingPlayer, 'ring')) {
          // Can't give item - max items?
          // Remove first item and give it again.
          $results[] = $this->getMessage('SOUL03');
          $this->removeFirstItem($actingPlayer);
          $this->giveItemToPlayer($actingPlayer, 'ring');
        }
      }
      $result = implode("\n", $results);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
