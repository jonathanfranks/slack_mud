<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Cast command plugin implementation.
 *
 * @MudCommandPlugin(
 * id = "kyrandia_cast",
 * module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class CastCommand extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * List of charms for protection.
   *
   * @var array
   */
  protected $protections = [
    'LIGPRO' => 'field_kyrandia_prot_lightning',
    'OBJPRO' => 'field_kyrandia_protection_other',
    'FIRPRO' => 'field_kyrandia_protection_fire',
  ];

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players say a command at the temple to get to level 3.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);

    $words = explode(' ', $commandText);
    $spellName = '';
    $target = '';
    if (count($words) == 1) {
      // Player only typed "cast".
      $result = $this->gameHandler->getMessage('OBJM07');
      return $result;
    }
    if (count($words) > 1) {
      // Spell is the second word. "cast zennyra".
      $spellName = $words[1];
    }
    if (count($words) > 2) {
      // Target is the last word. "cast zelastone at player".
      $target = end($words);
    }

    // Handle exceptions first.
    if ($spellName == 'zennyra' && $loc->getTitle() == 'Location 213') {
      // Casting zennyra (not a real spell) at the altar of sunshine gives a
      // message.
      $result = $this->gameHandler->getMessage('SUNM02');
    }

    // Cast a spell.
    if ($spell = $this->gameHandler->playerMemorizedSpell($actingPlayer, $spellName)) {
      $spellLevel = intval($spell->field_kyrandia_minimum_level->value);
      if ($spellLevel > intval($profile->field_kyrandia_level->entity->getName())) {
        $result = $this->gameHandler->getMessage('KSPM10');
      }
      elseif ($spellLevel > $profile->field_kyrandia_spell_points->value) {
        $result = $this->gameHandler->getMessage('KSPM10');
      }
      else {
        // Player can cast spell.
        switch ($spellName) {
          case 'zapher':
            if ($loc->getTitle() == 'Location 213' && $target == 'tulip' && $this->gameHandler->playerHasItem($actingPlayer, 'tulip', TRUE)) {
              // Casting zapher at the tulip at the altar of sunshine gives
              // player a wand.
              if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'wand')) {
                $result = $this->gameHandler->getMessage('SUNM00');
              }
            }
            break;
        }
        // Do spell.
        $result = $this->spellHandler($commandText, $actingPlayer);
        // Remove spell points.
        $this->gameHandler->playerMemorizedSpell($actingPlayer, $spellName, TRUE);
      }
    }
    if (!$result) {
      $result = $this->gameHandler->getMessage('NOTMEM');
    }
    return $result;
  }

  /**
   * Handler for regular spellcasting.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player casting the spell.
   */
  protected function spellHandler($commandText, NodeInterface $actingPlayer) {
    // @TODO Be more efficient with this. We're doing the same things over and
    // over again and we could probably be much more efficient with this.
    $words = explode(' ', $commandText);
    $castingSpellName = $words[1];
    // Array from original source goes like this:
    // [0] = spell name
    // [1] = routine
    // [2] = power/strength
    // [3] = bit flag
    // [4] = level
    // [5] = description/comment
    $spells = [
      ["abbracada", 'spl001', 2, 'SBD001', 10, "other pro II(scry,tel,etc)"],
      ["allbettoo", 'spl002', 2, 'SBD002', 17, "ultimate heal"],
      ["blowitawa", 'spl003', 3, 'SBD003', 5, "destroy one item"],
      ["blowoutma", 'spl004', 3, 'SBD004', 12, "destroy all items"],
      ["bookworm", 'spl005', 3, 'SBD005', 21, "zap other's spell book"],
      ["burnup", 'spl006', 1, 'SBD006', 6, "fireball I"],
      ["cadabra", 'spl007', 2, 'SBD007', 4, "see invisibility I"],
      ["cantcmeha", 'spl008', 2, 'SBD008', 7, "invisibility I"],
      ["canthur", 'spl009', 2, 'SBD009', 16, "ultimate protection I"],
      ["chillou", 'spl010', 1, 'SBD010', 20, "ice storm II"],
      ["clutzopho", 'spl011', 3, 'SBD011', 5, "make player drop all items"],
      ["cuseme", 'spl012', 3, 'SBD012', 3, "detect power (spell pts)"],
      ["dumdum", 'spl013', 3, 'SBD013', 17, "forget all spells"],
      ["feeluck", 'spl014', 3, 'SBD014', 10, "teleport random"],
      ["firstai", 'spl015', 2, 'SBD015', 10, "heal III"],
      ["flyaway", 'spl016', 3, 'SBD016', 10, "transform into pegasus"],
      ["fpandl", 'spl017', 1, 'SBD017', 2, "firebolt I"],
      ["freezuu", 'spl018', 1, 'SBD018', 14, "ice ball II"],
      ["frostie", 'spl019', 1, 'SBD019', 8, "cone of cold II"],
      ["frozenu", 'spl020', 1, 'SBD020', 7, "ice ball I"],
      ["frythes", 'spl021', 1, 'SBD021', 13, "firebolt III"],
      ["gotcha", 'spl022', 1, 'SBD022', 9, "lightning bolt II"],
      ["goto", 'spl023', 3, 'SBD023', 13, "teleport specific"],
      ["gringri", 'spl024', 3, 'SBD024', 12, "transform into psuedo drag"],
      ["handsof", 'spl025', 2, 'SBD025', 3, "object protection I"],
      ["heater", 'spl026', 2, 'SBD026', 7, "ice protection II"],
      ["hehhehh", 'spl027', 1, 'SBD027', 22, "lightning storm"],
      ["hocus", 'spl028', 3, 'SBD028', 18, "dispel magic"],
      ["holyshe", 'spl029', 1, 'SBD029', 14, "lightning bolt III"],
      ["hotflas", 'spl030', 1, 'SBD030', 8, "lightning ball"],
      ["hotfoot", 'spl031', 1, 'SBD031', 12, "fireball II"],
      ["hotkiss", 'spl032', 1, 'SBD032', 5, "firebolt II"],
      ["hotseat", 'spl033', 2, 'SBD033', 3, "ice protection I"],
      ["howru", 'spl034', 3, 'SBD034', 2, "detect health (hit points)"],
      ["hydrant", 'spl035', 2, 'SBD035', 6, "fire protection II"],
      ["ibebad", 'spl036', 2, 'SBD036', 24, "ultimate protection II"],
      ["icedtea", 'spl037', 1, 'SBD037', 15, "ice storm I"],
      ["icutwo", 'spl038', 3, 'SBD038', 16, "see invisibility III"],
      ["iseeyou", 'spl039', 3, 'SBD039', 3, "see invisibility II"],
      ["koolit", 'spl040', 1, 'SBD040', 3, "cone of cold I"],
      ["makemyd", 'spl041', 2, 'SBD041', 8, "object protection II"],
      ["mower", 'spl042', 3, 'SBD042', 7, "destroy things on ground"],
      ["noouch", 'spl043', 2, 'SBD043', 1, "heal I"],
      ["nosey", 'spl044', 3, 'SBD044', 5, "read other's memorized spls"],
      ["peekabo", 'spl045', 2, 'SBD045', 15, "invisibility II"],
      ["peepint", 'spl046', 3, 'SBD046', 7, "scry someone"],
      ["pickpoc", 'spl047', 3, 'SBD047', 8, "steal a player's item"],
      ["pocus", 'spl048', 1, 'SBD048', 1, "magic missile"],
      ["polarba", 'spl049', 2, 'SBD049', 13, "ice protection III"],
      ["sapspel", 'spl050', 1, 'SBD050', 11, "sap spell points II"],
      ["saywhat", 'spl051', 3, 'SBD051', 6, "forget one spell"],
      ["screwem", 'spl052', 1, 'SBD052', 16, "fire storm"],
      ["smokey", 'spl053', 2, 'SBD053', 2, "fire protection I"],
      ["snowjob", 'spl054', 1, 'SBD054', 13, "cone of cold III"],
      ["sunglass", 'spl055', 2, 'SBD055', 3, "lightning protection I"],
      ["surgless", 'spl056', 2, 'SBD056', 12, "lightning protection III"],
      ["takethat", 'spl057', 1, 'SBD057', 4, "sap spell points I"],
      ["thedoc", 'spl058', 2, 'SBD058', 5, "heal II"],
      ["tiltowait", 'spl059', 1, 'SBD059', 24, "earthquake"],
      ["tinting", 'spl060', 2, 'SBD060', 8, "lightning protection II"],
      ["toastem", 'spl061', 1, 'SBD061', 18, "fireball III"],
      ["weewillo", 'spl062', 3, 'SBD062', 7, "transform into willowisp"],
      ["whereami", 'spl063', 3, 'SBD063', 6, "location finder"],
      ["whopper", 'spl064', 2, 'SBD064', 12, "fire protection III"],
      ["whoub", 'spl065', 3, 'SBD065', 3, "detect true idenity"],
      ["zapher", 'spl066', 1, 'SBD066', 4, "lightning bolt I"],
      ["zelastone", 'spl067', 1, 'SBD067', 10, "ariel servant"],
    ];
    foreach ($spells as $spell) {
      if ($spell[0] == $castingSpellName) {
        // The original game keys off of the 'splXXX' but we can check on the
        // spell name.
        if (count($words) > 2) {
          $target = $words[2];
        }
        else {
          $target = NULL;
        }
        $actingProfile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
        $playerLevel = intval($actingProfile->field_kyrandia_level->entity->getName());
        $loc = $actingPlayer->field_location->entity;
        switch ($castingSpellName) {
          case 'abbracada':
            $this->charm($actingPlayer, 'OBJPRO', 8);
            $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('SPM000');
            $othersMessage = sprintf($this->gameHandler->getMessage('SPM001'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
            return $result;

          case 'allbettoo':
            $this->gameHandler->healPlayer($actingPlayer, 4 * $playerLevel);
            $result = $this->gameHandler->getMessage('SPM002');
            return $result;

          case 'blowitawa':
            $loc = $actingPlayer->field_location->entity;
            if ($targetPlayer = $this->gameHandler->locationHasPlayer($target, $loc, TRUE, $actingPlayer)) {
              $targetPlayerName = $targetPlayer->field_display_name->value;
              $targetProfile = $this->gameHandler->getKyrandiaProfile($targetPlayer);
              $protectionFieldName = array_key_exists('OBJPRO', $this->protections) ? $this->protections['OBJPRO'] : NULL;
              if ($protectionFieldName && $targetProfile->{$protectionFieldName}->value ||
                count($targetPlayer->field_inventory) == 0) {
                // Charmed or not carrying anything.
                $result = $this->gameHandler->getMessage('SNW000');
                return $result;
              }
              else {
                // Vaporize target's first held object.
                $item0Name = $targetPlayer->field_inventory[0]->entity->getTitle();
                $this->gameHandler->takeItemFromPlayer($targetPlayer, $item0Name);
                $result = sprintf($this->gameHandler->getMessage('SPM004'), $targetPlayerName, $item0Name);
                return $result;
              }
            }
            return NULL;

          case 'blowoutma':
            $loc = $actingPlayer->field_location->entity;
            if ($targetPlayer = $this->gameHandler->locationHasPlayer($target, $loc, TRUE, $actingPlayer)) {
              $targetPlayerName = $targetPlayer->field_display_name->value;
              $targetProfile = $this->gameHandler->getKyrandiaProfile($targetPlayer);
              $protectionFieldName = array_key_exists('OBJPRO', $this->protections) ? $this->protections['OBJPRO'] : NULL;
              if ($protectionFieldName && $targetProfile->{$protectionFieldName}->value ||
                count($targetPlayer->field_inventory) == 0) {
                // Charmed or not carrying anything.
                $result = $this->gameHandler->getMessage('SNW000');
                return $result;
              }
              else {
                // Vaporize target's entire inventory.
                $targetPlayer->field_inventory = NULL;
                $targetPlayer->save();
                $hisHer = $targetProfile->field_kyrandia_is_female->value ? 'her' : 'his';
                $result = sprintf($this->gameHandler->getMessage('SPM007'), $targetPlayerName, $hisHer);
                return $result;
              }
            }
            return NULL;

          case 'bookworm':
            $loc = $actingPlayer->field_location->entity;
            if ($targetPlayer = $this->gameHandler->locationHasPlayer($target, $loc, TRUE, $actingPlayer)) {
              $targetPlayerName = $targetPlayer->field_display_name->value;
              $targetProfile = $this->gameHandler->getKyrandiaProfile($targetPlayer);
              $protectionFieldName = array_key_exists('OBJPRO', $this->protections) ? $this->protections['OBJPRO'] : NULL;
              if ($protectionFieldName && $targetProfile->{$protectionFieldName}->value) {
                $result = $this->gameHandler->getMessage('S05M00');
                return $result;
              }
              else {
                // Player needs a moonstone.
                if ($this->gameHandler->playerHasItem($actingPlayer, 'moonstone', TRUE)) {
                  $targetProfile->field_kyrandia_memorized_spells = NULL;
                  $targetProfile->field_kyrandia_spellbook = NULL;
                  $targetProfile->save();
                  $result = sprintf($this->gameHandler->getMessage('S05M03'), $targetPlayerName, $targetPlayerName);
                  return $result;
                }
                else {
                  $result = $this->gameHandler->getMessage('MISS00');
                  return $result;
                }
              }
            }
          case 'burnup':
            $result = $this->gameHandler->getMessage('S06M00');
            $loc = $actingPlayer->field_location->entity;
            $slackUsername = $actingPlayer->field_slack_user_name->value;
            $result = $this->masshitr($actingPlayer, 10, 'FIRPRO', 1, 'S66M0', 'MERCYU');
            //S06M02,S06M03,S06M04,0,1)
            //            $otherPlayers = $this->otherPlayersInLocation($slackUsername, $loc);
            //            foreach ($otherPlayers as $otherPlayer) {
            //              $target = $otherPlayer->field_display_name->value;
            //            }
            return $result;

          case 'zapher':
            $result = $this->striker($actingPlayer, $target, 8, 'LIGPRO', 1, 'S66M0', 'MERCYA');
            return $result;
        }
      }
    }
    return NULL;
  }

  /**
   * Damage a player with a spell.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player casting the spell.
   * @param string $targetPlayerName
   *   The name of the player the caster is targeting.
   * @param int $damage
   *   The amount of damage the spell does.
   * @param string $protectionType
   *   The type of protection that opposes the spell.
   * @param int $mercyLevel
   *   The level the target has to be to be affected by the spell at all
   *   (mercy rule).
   * @param string $msg
   *   The message base for the effects of the spell.
   *   The actual messages are 0-5 from the base. For example, zapher is S66M0,
   *   so it's S66M00 through S66M05.
   *     0 - The message the acting player sees if the target is protected.
   *     1 - The message the target sees if they are protected.
   *     2 - The message other players see if the target is protected.
   *     3 - The message the acting player sees if target is damaged.
   *     4 - The message the target sees if they are damaged.
   *     5 - The message other players see if the target is damaged.
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function striker(NodeInterface $actingPlayer, $targetPlayerName, $damage, $protectionType, $mercyLevel, $msg) {
    // Target must be present.
    $loc = $actingPlayer->field_location->entity;
    if ($targetPlayer = $this->gameHandler->locationHasPlayer($targetPlayerName, $loc, TRUE, $actingPlayer)) {
      // Get the target player's real display name with capitalization and
      // everything.
      $targetPlayerName = $targetPlayer->field_display_name->value;
      $targetProfile = $this->gameHandler->getKyrandiaProfile($targetPlayer);

      // Check if player is charmed with protection.
      $protectionFieldName = array_key_exists($protectionType, $this->protections) ? $this->protections[$protectionType] : NULL;
      if ($protectionFieldName && $targetProfile->{$protectionFieldName}->value) {
        $message = $msg . '0';
        $result = sprintf($this->gameHandler->getMessage($message), $targetPlayerName);
      }
      elseif (intval($targetProfile->field_kyrandia_level->entity->getName()) <= $mercyLevel) {
        $result = sprintf($this->gameHandler->getMessage('MERCYA'), $targetPlayerName);
      }
      else {
        // Target isn't protected or too low level. Damage!
        $message = $msg . '3';
        $results = [];
        $results[] = sprintf($this->gameHandler->getMessage($message), $targetPlayerName);
        $damageResult = $this->gameHandler->damagePlayer($targetPlayer, $damage, $result);
        $result = implode("\n", $results);
      }
    }
    else {
      $result = "...Something is missing and the spell fails!";
    }
    return $result;
  }

  /**
   * Adds protection charm to player.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player casting the charm spell.
   * @param string $protectionType
   *   The type of charm.
   * @param int $protection
   *   The amount of protection given by the charm.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function charm(NodeInterface $actingPlayer, $protectionType, $protection) {
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $protectionFieldName = array_key_exists($protectionType, $this->protections) ? $this->protections[$protectionType] : NULL;
    if ($protectionFieldName) {
      $profile->{$protectionFieldName}->value = $protection;
      $profile->save();
    }
  }

  /**
   * Mass hit users.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player casting the spell.
   * @param int $damage
   *   The amount of damage.
   * @param string $protectionType
   *   The type of protection from this spell.
   * @param string $hitMessage
   *   The message for hit.
   * @param string $otherMessage
   *   The message for other players.
   * @param string $protectedMessage
   *   The message for protected players.
   * @param bool $hitsSelf
   *   TRUE if this spell damages the caster.
   * @param int $mercyLevel
   *   The level the target has to be to be affected by the spell at all
   *   (mercy rule).
   *
   */
  private function masshitr(NodeInterface $actingPlayer, $damage, $protectionType, $hitMessage, $otherMessage, $protectedMessage, $hitsSelf, $mercyLevel) {

  }

}
