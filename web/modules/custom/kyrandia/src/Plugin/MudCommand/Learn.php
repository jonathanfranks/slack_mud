<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Learn command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_learn",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Learn extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;

    // @TODO Configurationize this?
    $maxSpells = 3;

    $words = explode(' ', $commandText);
    // Syntax is "learn [spell]", like "learn weewillo".
    if (count($words) > 1) {
      $spellName = $words[1];
      if ($spell = $this->gameHandler->playerHasSpell($actingPlayer, $spellName)) {
        $results = [];
        $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
        if (count($profile->field_kyrandia_memorized_spells) >= $maxSpells) {
          $forgotSpell = $profile->field_kyrandia_memorized_spells[0];
          $results[] = sprintf($this->gameHandler->getMessage('LOSSPL'), $spellName, $forgotSpell->entity->getName());
          unset($profile->field_kyrandia_memorized_spells[0]);
        }
        else {
          $results[] = sprintf($this->gameHandler->getMessage('GAISPL'), $spellName);
        }
        $profile->field_kyrandia_memorized_spells[] = $spell;
        $profile->save();
        $result = implode("\n", $results);
      }
      else {
        $result = $this->gameHandler->getMessage('KSPM09');
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
