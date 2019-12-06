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
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    $roseLocations = [
      'Location 12',
      'Location 32',
      'Location 36',
    ];
    if (in_array($loc->getTitle(), $roseLocations)) {
      $result = $this->rose($commandText, $actingPlayer);
    }
    elseif ($loc->getTitle() == 'Location 14') {
      // @TODO Handle verb synonyms.
      $result = $this->pines($commandText, $actingPlayer);
    }
    elseif ($loc->gettitle() == 'Location 20') {
      $result = $this->rubies($commandText, $actingPlayer);
    }
    elseif ($loc->gettitle() == 'Location 199') {
      $result = $this->tulips($commandText, $actingPlayer);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Getting a rose from the brook.
   *
   * @param string $commandText
   *   Command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function rose($commandText, NodeInterface $actingPlayer) {
    $result = '';
    // We're looking for 'pick rose'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'rose',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      if ($this->giveItemToPlayer($actingPlayer, 'rose')) {
        $result = $this->getMessage('GROSE1');
      }
      else {
        $result = $this->getMessage('GROSE3');
      }
    }
    return $result;
  }

  /**
   * Picking pinecones.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function pines($commandText, NodeInterface $actingPlayer) {
    $result = '';
    // We're looking for 'pick pinecone'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'pinecone',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      // Random chance to grab one.
      $chance = rand(0, 100);
      if ($chance < 40) {
        if ($this->giveItemToPlayer($actingPlayer, 'pinecone')) {
          $result = $this->getMessage('PINEC0');
        }
        else {
          $result = $this->getMessage('PINEC2');
        }
      }
      else {
        $result = $this->getMessage('PINEC2');
      }
    }
    return $result;
  }

  /**
   * Picking rubies.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function rubies($commandText, NodeInterface $actingPlayer) {
    $result = '';
    // We're looking for 'pick ruby'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'ruby',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      // Random chance to grab one.
      $chance = rand(0, 100);
      if ($chance < 20) {
        if ($this->giveItemToPlayer($actingPlayer, 'ruby')) {
          $result = $this->getMessage('RUBY00');
        }
      }
      if (!$result) {
        $result = $this->damagePlayer($actingPlayer, 8);
        if (!$result) {
          // Damage didn't kill the player.
          $result = $this->getMessage('RUBY02');
        }
      }
    }
    return $result;
  }

  /**
   * Picking tulips.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function tulips($commandText, NodeInterface $actingPlayer) {
    $result = '';
    // We're looking for 'pick tulip'.
    $words = explode(' ', $commandText);
    $synonyms = [
      'tulip',
    ];
    $synonymMatch = array_intersect($synonyms, $words);
    if ($synonymMatch) {
      if ($this->giveItemToPlayer($actingPlayer, 'tulip')) {
        $result = $this->getMessage('TULM00');
      }
      else {
        $result = $this->getMessage('TULM02');
      }
    }
    return $result;
  }

}
