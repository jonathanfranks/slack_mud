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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 291' && $commandText == 'ignore time') {
      if ($this->gameHandler->advanceLevel($profile, 16)) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SOUL01');
        $othersMessage = sprintf($this->gameHandler->getMessage('SOUL02'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        if (!$this->gameHandler->giveItemToPlayer($actingPlayer, 'ring')) {
          // Can't give item - max items?
          // Remove first item and give it again.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SOUL03');
          $this->gameHandler->removeFirstItem($actingPlayer);
          $this->gameHandler->giveItemToPlayer($actingPlayer, 'ring');
        }
      }
    }
    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
