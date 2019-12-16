<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Forget command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_forget",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Forget extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 253') {
      // @TODO Handle verb synonyms.
      if ($profile->field_kyrandia_level->entity->getName() == '19') {
        $this->gameHandler->advanceLevel($actingPlayer, 20);
        $result = $this->gameHandler->getMessage('LEVL20');
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
