<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Believe command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_believe",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Believe extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can dig in the brook to randomly find gold.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 257') {
      if ($commandText == 'believe in magic') {
        $profile = $this->getKyrandiaProfile($actingPlayer);
        if ($profile->field_kyrandia_level->entity->getName() == '20') {
          if ($this->advanceLevel($profile, 21)) {
            $result = $this->getMessage('LEVL21');
          }
        }
      }
    }
    elseif ($loc->getTitle() == 'Location 293') {
      if ($commandText == 'believe in fantasy') {
        $profile = $this->getKyrandiaProfile($actingPlayer);
        if ($profile->field_kyrandia_level->entity->getName() == '23') {
          if ($this->advanceLevel($profile, 24)) {
            $result = $this->getMessage('LEVL24');
          }
        }
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
