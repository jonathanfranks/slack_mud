<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Drupal\slack_mud\Plugin\MudCommand\MudCommandPluginBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Defines a base MudCommand plugin implementation.
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
abstract class KyrandiaCommandPluginBase extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * Gets the Kyrandia profile node for the given player node.
   *
   * @param \Drupal\node\NodeInterface $targetPlayer
   *   The player.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The player's Kyrandia profile node.
   */
  protected function getKyrandiaProfile(NodeInterface $targetPlayer) {
    // @TODO: Service-ize this.
    $kyrandiaProfile = NULL;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'kyrandia_profile')
      ->condition('field_player.target_id', $targetPlayer->id());
    $kyrandiaProfileNids = $query->execute();
    if ($kyrandiaProfileNids) {
      $kyrandiaProfileNid = reset($kyrandiaProfileNids);
      $kyrandiaProfile = Node::load($kyrandiaProfileNid);
    }
    return $kyrandiaProfile;
  }

  /**
   * Gets a Kyrandia message.
   *
   * @param string $messageId
   *   The message ID.
   *
   * @return |null
   *   The message text.
   */
  protected function getMessage($messageId) {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'kyrandia_message')
      ->condition('name', $messageId);
    $ids = $query->execute();
    if ($ids) {
      $id = $ids ? reset($ids) : NULL;
      $messageTerm = Term::load($id);
      $message = $messageTerm->getDescription();
      return $message;
    }
    return NULL;
  }

  /**
   * Advance the profile to the specified level.
   *
   * @param \Drupal\node\NodeInterface $profile
   *   Acting player.
   * @param int $level
   *   Level to advance to.
   *
   * @return bool
   *   TRUE if level was advanced.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function advanceLevel(NodeInterface $profile, $level) {
    // Set the player's level to $level.
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'kyrandia_level')
      ->condition('name', $level);
    $level_ids = $query->execute();
    if ($level_ids) {
      $level_id = $level_ids ? reset($level_ids) : NULL;
      $profile->field_kyrandia_level = $level_id;
      $profile->save();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Gives the named spell to the specified player.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player.
   * @param string $spellName
   *   The spell to give.
   *
   * @return bool
   *   TRUE if the spell was given.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function giveSpellToPlayer(NodeInterface $player, string $spellName) {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'kyrandia_spell')
      ->condition('name', $spellName);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $profile = $this->getKyrandiaProfile($player);
      $hasSpell = $this->playerHasSpell($player, $spellName);
      if (!$hasSpell) {
        $profile->field_kyrandia_spellbook[] = $id;
        $profile->save();
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Checks if specified player has specified spell.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player.
   * @param string $spellName
   *   The spell name.
   *
   * @return \Drupal\node\NodeInterface
   *   The spell if the player has the spell in their spellbook or null.
   */
  protected function playerHasSpell(NodeInterface $player, $spellName) {
    $profile = $this->getKyrandiaProfile($player);
    foreach ($profile->field_kyrandia_spellbook as $spell) {
      if ($spell->entity->getName() == $spellName) {
        return $spell->entity;
      }
    }
    return FALSE;
  }

  /**
   * Checks if specified player has specified spell memorized.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player.
   * @param string $spellName
   *   The spell name.
   * @param bool $removeSpell
   *   If TRUE, remove the spell from the player's memorized spells when found.
   *
   * @return \Drupal\node\NodeInterface
   *   The spell if the player has the spell memorized or null.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function playerMemorizedSpell(NodeInterface $player, $spellName, $removeSpell = FALSE) {
    $profile = $this->getKyrandiaProfile($player);
    foreach ($profile->field_kyrandia_memorized_spells as $delta => $spell) {
      if ($spell->entity->getName() == $spellName) {
        if ($removeSpell) {
          unset($profile->field_kyrandia_memorized_spells[$delta]);
          $profile->save();
        }
        return $spell->entity;
      }
    }
    return FALSE;
  }

  /**
   * Applies damage to the target player.
   *
   * @param \Drupal\node\NodeInterface $player
   *   Player to damage.
   * @param int $damage
   *   Amount of damage to apply.
   *
   * @return string|null
   *   NULL if the player is still alive, otherwise the death message.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function damagePlayer(NodeInterface $player, $damage) {
    $profile = $this->getKyrandiaProfile($player);
    $currentHits = $profile->field_kyrandia_hit_points->value;
    if ($currentHits - $damage <= 0) {
      // Kills player.
      // @TODO Handle player death.
      $result = $this->getMessage('DIEMSG');
      return $result;
    }
    else {
      $currentHits -= $damage;
    }
    $profile->field_kyrandia_hit_points = $currentHits;
    $profile->save();
    return NULL;
  }

  /**
   * Heals the target player.
   *
   * @param \Drupal\node\NodeInterface $player
   *   Player to heal.
   * @param int $heal
   *   Amount of health to apply.
   *
   * @return int
   *   The player's current hit points after healing.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function healPlayer(NodeInterface $player, $heal) {
    $profile = $this->getKyrandiaProfile($player);
    $currentHits = $profile->field_kyrandia_hit_points->value;
    $maxHits = $profile->field_kyrandia_max_hit_points->value;
    if ($currentHits + $heal <= $maxHits) {
      $currentHits += $heal;
    }
    else {
      $currentHits = $maxHits;
    }
    $profile->field_kyrandia_hit_points = $currentHits;
    $profile->save();
    return $currentHits;
  }

  /**
   * Gets the value of the specified instance setting.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The game.
   * @param string $setting
   *   The setting.
   * @param mixed $defaultValue
   *   The value to use if the setting hasn't been set.
   *
   * @return mixed|null
   *   The setting value or NULL if it isn't set.
   */
  protected function getInstanceSetting(NodeInterface $game, $setting, $defaultValue) {
    $settingValue = $defaultValue;
    $instanceSettingsText = $game->field_instance_settings->value;
    $settings = json_decode($instanceSettingsText, TRUE);
    if ($settings === NULL) {
      $settings = [];
    }
    if ($settings && array_key_exists($setting, $settings)) {
      $settingValue = $settings[$setting];
    }
    return $settingValue;
  }

  /**
   * Sets an instance value for a game.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The game.
   * @param string $setting
   *   The setting.
   * @param mixed $value
   *   The value.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function saveInstanceSetting(NodeInterface $game, $setting, $value) {
    $instanceSettingsText = $game->field_instance_settings->value;
    $settings = json_decode($instanceSettingsText, TRUE);
    $settings[$setting] = $value;
    $game->field_instance_settings = json_encode($settings);
    $game->save();
  }

  /**
   * Removes the first item from the player's inventory.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function removeFirstItem(NodeInterface $actingPlayer) {
    if (count($actingPlayer->field_inventory)) {
      // Remove the player's first item.
      $firstItemName = $actingPlayer->field_inventory[0]->entity->getTitle();
      $this->takeItemFromPlayer($actingPlayer, $firstItemName);
    }
  }

  /**
   * Is the dragon in this location?
   *
   * @param \Drupal\node\NodeInterface $location
   *   The location.
   *
   * @return bool
   *   TRUE if the dragon is there.
   */
  protected function isDragonHere(NodeInterface $location) {
    $dragonHere = FALSE;
    foreach ($location->field_visible_items as $visible_item) {
      if ($visible_item->entity->getTitle() == 'dragon') {
        $dragonHere = TRUE;
        break;
      }
    }
    return $dragonHere;
  }

  /**
   * Sends the specified message to each other player in the actor's location.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player performing the action.
   * @param \Drupal\node\NodeInterface $loc
   *   The player's current location (usually - this could be a remotely
   *   targeted location).
   * @param string $othersMessage
   *   The message to show the players in the target location.
   * @param array $result
   *   The message results.
   */
  protected function sendMessageToOthersInLocation(NodeInterface $actingPlayer, NodeInterface $loc, string $othersMessage, array &$result) {
    $otherPlayers = $this->otherPlayersInLocation($loc, $actingPlayer);
    foreach ($otherPlayers as $otherPlayer) {
      $result[$otherPlayer->id()][] = $othersMessage;
    }
  }

}
