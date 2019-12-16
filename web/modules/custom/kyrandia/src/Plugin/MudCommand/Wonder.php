<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Wonder command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_wonder",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Wonder extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 264') {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '22') {
        if ($this->gameHandler->advanceLevel($profile, 23)) {
          $result = $this->gameHandler->getMessage('LEVL23');
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
