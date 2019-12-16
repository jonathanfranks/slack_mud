<?php

namespace Drupal\kyrandia\Service;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\Service\MudGameHandlerService;
use Drupal\taxonomy\Entity\Term;

/**
 * Service that handles the game for the command plugins for Kyrandia.
 *
 * @package Drupal\kyrandia\Service
 */
class KyrandiaGameHandlerService extends MudGameHandlerService implements KyrandiaGameHandlerServiceInterface {

  /**
   * {@inheritdoc}
   */
  public function getKyrandiaProfile(NodeInterface $targetPlayer) {
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
   * {@inheritdoc}
   */
  public function getMessage($messageId) {
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
   * {@inheritdoc}
   */
  public function advanceLevel(NodeInterface $profile, $level) {
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
   * {@inheritdoc}
   */
  public function giveSpellToPlayer(NodeInterface $player, string $spellName) {
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
   * {@inheritdoc}
   */
  public function playerHasSpell(NodeInterface $player, $spellName) {
    $profile = $this->getKyrandiaProfile($player);
    foreach ($profile->field_kyrandia_spellbook as $spell) {
      if ($spell->entity->getName() == $spellName) {
        return $spell->entity;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function playerMemorizedSpell(NodeInterface $player, $spellName, $removeSpell = FALSE) {
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
   * {@inheritdoc}
   */
  public function damagePlayer(NodeInterface $player, $damage, array &$result) {
    $profile = $this->getKyrandiaProfile($player);
    $currentHits = $profile->field_kyrandia_hit_points->value;
    $loc = $player->field_location->entity;
    if ($currentHits - $damage <= 0) {
      // Kills player.
      $this->killPlayer($player, $result);
      return FALSE;
    }
    else {
      $currentHits -= $damage;
    }
    $profile->field_kyrandia_hit_points = $currentHits;
    $profile->save();
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function killPlayer(NodeInterface $player, array &$result) {
    $loc = $player->field_location->entity;
    $profile = $this->getKyrandiaProfile($player);
    $profile->delete();
    _kyrandia_create_new_profile($player);
    $result[$player->id()][] = $this->getMessage('DIEMSG');
    $othersMessage = sprintf($this->getMessage('KILLED'), $player->field_display_name->value);
    $this->sendMessageToOthersInLocation($player, $loc, $othersMessage, $result);
    $this->movePlayer($player, 'Location 0');
    $newLoc = $player->field_location->entity;
    $msg = t('appeared in a holy light');
    $entranceMessage = sprintf("***\n%s has just %s!\n", $player->field_display_name->value, $msg);
    $this->sendMessageToOthersInLocation($player, $newLoc, $entranceMessage, $result);
  }

  /**
   * {@inheritdoc}
   */
  public function healPlayer(NodeInterface $player, $heal) {
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
   * {@inheritdoc}
   */
  public function getInstanceSetting(NodeInterface $game, $setting, $defaultValue) {
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
   * {@inheritdoc}
   */
  public function saveInstanceSetting(NodeInterface $game, $setting, $value) {
    $instanceSettingsText = $game->field_instance_settings->value;
    $settings = json_decode($instanceSettingsText, TRUE);
    $settings[$setting] = $value;
    $game->field_instance_settings = json_encode($settings);
    $game->save();
  }

  /**
   * {@inheritdoc}
   */
  public function removeFirstItem(NodeInterface $actingPlayer) {
    if (count($actingPlayer->field_inventory)) {
      // Remove the player's first item.
      $firstItemName = $actingPlayer->field_inventory[0]->entity->getTitle();
      $this->takeItemFromPlayer($actingPlayer, $firstItemName);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isDragonHere(NodeInterface $location) {
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
   * {@inheritdoc}
   */
  public function moveDragon(NodeInterface $location) {
    // First remove the dragon from wherever it is.
    $query = \Drupal::entityQuery('node')
      ->condition('field_game.entity.title', 'kyrandia')
      ->condition('type', 'item')
      ->condition('title', 'dragon');
    $ids = $query->execute();
    if ($ids) {
      $dragon_item_id = reset($ids);
    }

    $query = \Drupal::entityQuery('node')
      ->condition('field_game.entity.title', 'kyrandia')
      ->condition('type', 'location')
      ->condition('field_visible_items.target_id', $dragon_item_id);
    $ids = $query->execute();
    $locationNodes = Node::loadMultiple($ids);
    // Now for each instance of a dragon in all locations, remove them.
    foreach ($locationNodes as $locationNode) {
      do {
        $dragon = $this->locationHasItem($locationNode, 'remove dragon', TRUE);
      } while ($dragon);
    }
    // Then put it where it needs to be.
    if ($location) {
      $this->placeItemInLocation($location, 'dragon');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function sendMessageToOthersInLocation(NodeInterface $actingPlayer, NodeInterface $loc, string $othersMessage, array &$result, array $exceptPlayers = []) {
    $otherPlayers = $this->otherPlayersInLocation($loc, $actingPlayer);
    $noMessagePlayerIds = [];
    foreach ($exceptPlayers as $exceptPlayer) {
      $noMessagePlayerIds[] = $exceptPlayer->id();
    }
    foreach ($otherPlayers as $otherPlayer) {
      if (!in_array($otherPlayer->id(), $noMessagePlayerIds)) {
        $result[$otherPlayer->id()][] = $othersMessage;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function targetNonExistantItem(NodeInterface $actingPlayer, array &$result) {
    $result[$actingPlayer->id()][] = $this->getMessage('OBJM09');
    $location = $actingPlayer->field_location->entity;
    $othersMessage = t(':actor is having wild dreams.', [
      ':actor' => $actingPlayer->field_display_name->value,
    ]);
    $this->sendMessageToOthersInLocation($actingPlayer, $location, $othersMessage, $result);
  }

}
