<?php

namespace Drupal\kyrandia\Service;

use Drupal\node\NodeInterface;

/**
 * Service that handles the game for the command plugins for Kyrandia.
 *
 * @package Drupal\kyrandia\Service
 */
interface KyrandiaGameHandlerServiceInterface {

  /**
   * Gets the Kyrandia profile node for the given player node.
   *
   * @param \Drupal\node\NodeInterface $targetPlayer
   *   The player.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The player's Kyrandia profile node.
   */
  public function getKyrandiaProfile(NodeInterface $targetPlayer);

  /**
   * Gets a Kyrandia message.
   *
   * @param string $messageId
   *   The message ID.
   *
   * @return |null
   *   The message text.
   */
  public function getMessage($messageId);

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
  public function advanceLevel(NodeInterface $profile, $level);

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
  public function giveSpellToPlayer(NodeInterface $player, string $spellName);

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
  public function playerHasSpell(NodeInterface $player, $spellName);

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
  public function playerMemorizedSpell(NodeInterface $player, $spellName, $removeSpell = FALSE);

  /**
   * Applies damage to the target player.
   *
   * @param \Drupal\node\NodeInterface $player
   *   Player to damage.
   * @param int $damage
   *   Amount of damage to apply.
   * @param array $result
   *   Existing results to add to or alter.
   *
   * @return bool
   *   TRUE if the player is still alive, FALSE if the player is dead.
   */
  public function damagePlayer(NodeInterface $player, $damage, array &$result);

  /**
   * Kills and reincarnates the target player.
   *
   * @param \Drupal\node\NodeInterface $player
   *   Player to damage.
   * @param array $result
   *   Existing results to add to or alter.
   *
   * @return bool
   *   TRUE if the player is still alive, FALSE if the player is dead.
   */
  public function killPlayer(NodeInterface $player, array &$result);

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
  public function healPlayer(NodeInterface $player, $heal);

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
  public function getInstanceSetting(NodeInterface $game, $setting, $defaultValue);

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
  public function saveInstanceSetting(NodeInterface $game, $setting, $value);

  /**
   * Removes the first item from the player's inventory.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function removeFirstItem(NodeInterface $actingPlayer);

  /**
   * Is the dragon in this location?
   *
   * @param \Drupal\node\NodeInterface $location
   *   The location.
   *
   * @return bool
   *   TRUE if the dragon is there.
   */
  public function isDragonHere(NodeInterface $location);

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
   * @param array $exceptPlayers
   *   Players in the location not to send the message to.
   */
  public function sendMessageToOthersInLocation(NodeInterface $actingPlayer, NodeInterface $loc, string $othersMessage, array &$result, array $exceptPlayers = []);

  /**
   * Handles player doing something to a non-existent item.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $result
   *   The result array.
   */
  public function targetNonExistantItem(NodeInterface $actingPlayer, array &$result);

}
