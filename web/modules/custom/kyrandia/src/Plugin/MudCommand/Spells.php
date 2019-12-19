<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Spells command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_spells",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Spells extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $result = NULL;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      $spellList = '';
      if (count($profile->field_kyrandia_memorized_spells) == 0) {
        $spellList = 'no spells';
      }
      else {
        $spells = [];
        foreach ($profile->field_kyrandia_memorized_spells as $spell) {
          $spells[] = $spell->entity->getName();
        }
        $spellList = $this->wordGrammar->getWordList($spells);
      }
      $spellPoints = $profile->field_kyrandia_spell_points->value;
      $levelTerm = $profile->field_kyrandia_level->entity;
      $level = $levelTerm->getName();
      $levelName = $levelTerm->getDescription();
      $result = t("You currently have :spellList memorized, and :spellPoints spell points of energy. You are at level :level, titled \":levelName\".", [
        ':spellList' => $spellList,
        ':spellPoints' => $spellPoints,
        ':level' => $level,
        ':levelName' => $levelName,
      ]);
    }
    return $result;
  }

}
