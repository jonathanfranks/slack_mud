<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Gold command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_gold",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Gold extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      $result = t('You have :gold gold piece:plural.', [
        ':gold' => $profile->field_kyrandia_gold->value,
        ':plural' => $profile->field_kyrandia_gold->value == 1 ? '' : 's',
      ]);
    }
    return $result;
  }

}
