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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      $results[$actingPlayer->id()][] = t('You are currently level :level, :title.',
        [
          ':level' => $profile->field_kyrandia_level->entity->getName(),
          ':title' => $profile->field_kyrandia_level->entity->description->value,
        ]);
    }
  }

}
