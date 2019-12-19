<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Pick command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_pick",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Pick extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $roseLocations = [
      'Location 12',
      'Location 32',
      'Location 36',
    ];
    if (in_array($loc->getTitle(), $roseLocations)) {
      $this->rose($commandText, $actingPlayer, $results);
    }
    elseif ($loc->getTitle() == 'Location 14') {
      // @TODO Handle verb synonyms.
      $this->pines($commandText, $actingPlayer, $results);
    }
    elseif ($loc->gettitle() == 'Location 20') {
      $this->rubies($commandText, $actingPlayer, $results);
    }
    elseif ($loc->gettitle() == 'Location 199') {
      $this->tulips($commandText, $actingPlayer, $results);
    }
  }

  /**
   * Getting a rose from the brook.
   *
   * @param string $commandText
   *   Command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function rose($commandText, NodeInterface $actingPlayer, array &$results) {
    // We're looking for 'pick rose'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'rose',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    $loc = $actingPlayer->field_location->entity;
    if ($synonymMatch) {
      if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'rose')) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('GROSE1');
        $othersMessage = sprintf($this->gameHandler->getMessage('GROSE2'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('GROSE3');
        $othersMessage = sprintf($this->gameHandler->getMessage('GROSE4'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
  }

  /**
   * Picking pinecones.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function pines($commandText, NodeInterface $actingPlayer, array &$results) {
    // We're looking for 'pick pinecone'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'pinecone',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      // Random chance to grab one.
      $game = $actingPlayer->field_game->entity;
      $loc = $actingPlayer->field_location->entity;
      $chance = $this->generateRandomNumber($game, 0, 100);
      if ($chance < 40 && $this->gameHandler->giveItemToPlayer($actingPlayer, 'pinecone')) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PINEC0');
        $othersMessage = sprintf($this->gameHandler->getMessage('PINEC1'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PINEC2');
        $othersMessage = sprintf($this->gameHandler->getMessage('PINEC3'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
  }

  /**
   * Picking rubies.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function rubies($commandText, NodeInterface $actingPlayer, array &$results) {
    // We're looking for 'pick ruby'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'ruby',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    $loc = $actingPlayer->field_location->entity;
    if ($synonymMatch) {
      // Random chance to grab one.
      $game = $actingPlayer->field_game->entity;
      $chance = $this->generateRandomNumber($game, 0, 100);
      if ($chance < 20 && $this->gameHandler->giveItemToPlayer($actingPlayer, 'ruby')) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('RUBY00');
        $othersMessage = sprintf($this->gameHandler->getMessage('RUBY01'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('RUBY02');
        $othersMessage = sprintf($this->gameHandler->getMessage('RUBY03'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        $this->gameHandler->damagePlayer($actingPlayer, 8, $results);
      }
    }
  }

  /**
   * Picking tulips.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function tulips($commandText, NodeInterface $actingPlayer, array &$results) {
    // We're looking for 'pick tulip'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'tulip',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      $loc = $actingPlayer->field_location->entity;
      if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'tulip')) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('TULM00');
        $othersMessage = sprintf($this->gameHandler->getMessage('TULM01'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('TULM02');
        $othersMessage = sprintf($this->gameHandler->getMessage('TULM01'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
  }

}
