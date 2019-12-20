<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Touch command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_touch",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Touch extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 188') {
      $this->mistyRuins($actingPlayer, $commandText, $results);
    }
    elseif ($loc->getTitle() == 'Location 34') {
      // Druid's circle.
      $words = explode(' ', $commandText);
      // We're looking for 'touch orb with sceptre'.
      $orbPosition = array_search('orb', $words);
      $sceptrePosition = array_search('sceptre', $words);
      if ($orbPosition !== FALSE && $sceptrePosition !== FALSE && $orbPosition < $sceptrePosition) {
        if ($this->gameHandler->takeItemFromPlayer($actingPlayer, 'sceptre')) {
          // Give the player a random spell from this list.
          $spells = [
            'chillou',
            'freezuu',
            'frostie',
            'frythes',
            'hotflas',
          ];
          $game = $actingPlayer->field_game->entity;
          $randomSpellKey = $this->generateRandomNumber($game, 0, count($spells) - 1);
          $spell = $spells[$randomSpellKey];
          $this->gameHandler->giveSpellToPlayer($actingPlayer, $spell);
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('DRUID0');
          $othersMessage = sprintf($this->gameHandler->getMessage('DRUID1'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
        else {
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('DRUID2');
          $othersMessage = sprintf($this->gameHandler->getMessage('DRUID1'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
    }
  }

  /**
   * Touching the orb at the misty ruins.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The acting player.
   * @param string $commandText
   *   The command text.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function mistyRuins(NodeInterface $actingPlayer, $commandText, array &$results) {
    // Touch orb in misty ruins (188) teleports to druid's circle (34).
    // We're looking for 'touch orb'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'orb',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MISM00');
      $this->gameHandler->movePlayer($actingPlayer, 'Location 34', $results, 'vanished in a bright blue flash', 'appeared in a bright blue flash');

      // The result is LOOKing at the new location.
      /** @var \Drupal\slack_mud\Event\CommandEvent $mudEvent */
      $mudEvent = new CommandEvent($actingPlayer, 'look', $results);
      $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
      $results = $mudEvent->getResponse();
    }
  }

}
