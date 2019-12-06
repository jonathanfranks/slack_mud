<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Level command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_level",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Level extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can kneel at the willow tree at location 0 to go from level 1 to
    // level 2.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      $result = t('You are currently level :level, :title.',
        [
          ':level' => $profile->field_kyrandia_level->entity->getName(),
          ':title' => $profile->field_kyrandia_level->entity->description->value,
        ]);
    }
    return $result;
  }

}
