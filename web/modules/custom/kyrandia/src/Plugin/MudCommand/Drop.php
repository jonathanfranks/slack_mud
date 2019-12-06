<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kyrandia-specific Drop command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_drop",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Drop extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players drop things into the stump at Loc 18 to reach level 6.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if (strpos($commandText, 'stump') !== FALSE && $loc->getTitle() == 'Location 18') {
      $result = $this->stump($commandText, $actingPlayer, $profile);
    }
    elseif (strpos($commandText, 'fountain') !== FALSE && $loc->getTitle() == 'Location 38') {
      $result = $this->fountain($commandText, $actingPlayer, $profile);
    }
    elseif (strpos($commandText, 'pool') !== FALSE && $loc->getTitle() == 'Location 182') {
      $result = $this->reflectingPool($commandText, $actingPlayer, $profile);
    }
    else {
      // Not a special drop. Handle this like a regular drop.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('drop');
      $result = $plugin->perform($commandText, $actingPlayer);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Gets the item targeted by the command.
   *
   * @param string $commandText
   *   The command text the user entered.
   *
   * @return mixed|string
   *   The object the user typed.
   */
  protected function getTargetItem($commandText) {
    $target = str_replace('stump', '', $commandText);
    $target = str_replace('into', '', $target);
    $target = str_replace('in', '', $target);
    // Now remove the DROP and we'll see what they're dropping.
    $target = str_replace('drop', '', $target);
    $target = trim($target);
    return $target;
  }

  /**
   * Handles dropping things into the stump.
   *
   * This can advance a player from level 5 to 6 when done in the right order.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The player's profile.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function stump($commandText, NodeInterface $actingPlayer, NodeInterface $profile) {
    $currentStumpStone = intval($profile->field_kyrandia_stump_gem->value);
    $stumpStones = [
      'ruby',
      'emerald',
      'garnet',
      'pearl',
      'aquamarine',
      'moonstone',
      'sapphire',
      'diamond',
      'amethyst',
      'onyx',
      'opal',
      'bloodstone',
    ];

    // Player is dropping something in or into stump.
    $target = $this->getTargetItem($commandText);
    $invDelta = $this->playerHasItem($actingPlayer, $target);
    if ($invDelta === FALSE) {
      $result = t("But you don't have one, you hallucinating fool!");
    }
    else {
      $item = $actingPlayer->field_inventory[$invDelta]->entity;
      $itemTitle = $item->getTitle();
      $stumpStoneIndex = array_search($itemTitle, $stumpStones);
      if ($stumpStoneIndex === $currentStumpStone) {
        // Match! This is the last one, so advance to level 6 if the user is
        // level 5!
        if ($currentStumpStone == count($stumpStones) - 1 && $profile->field_kyrandia_level->entity->getName() == '5') {
          $this->advanceLevel($profile, 6);
          $result = "As you drop the gem into the stump, a powerful surge of magical energy rushes through your entire body!
***\n
You are now at level 6!\n
***\n
A spell has been added to your spellbook!";
        }
        else {
          // Match! Set the stone index to the next one.
          $result = "The gem drops smoothly into the endless depths of the stump. You feel a mysterious tingle down your spine, as though you have begun to unleash a powerful source of magic.";
          if ($currentStumpStone < count($stumpStones) - 1) {
            // If it's the last one but the user isn't level 5, just keep the
            // index where it is.
            $nextStumpStone = $currentStumpStone + 1;
            $profile->field_kyrandia_stump_gem = $nextStumpStone;
            $profile->save();
          }
        }
      }
      else {
        $result = "It drops into the endless depths of the stump, but nothing seems to happen.";
      }
      // Remove item from inventory whether it matches or not.
      unset($actingPlayer->field_inventory[$invDelta]);
      $actingPlayer->save();
    }
    return $result;
  }

  /**
   * Handles dropping pinecones into the fountain.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string|null
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function pinecone(NodeInterface $actingPlayer) {
    $result = NULL;
    // We're looking for something like 'drop pinecone in fountain'.
    $game = $actingPlayer->field_game->entity;
    $fountainPineconeCount = $this->getInstanceSetting($game, 'fountainPineconeCount', 0);
    $fountainPineconeCount++;
    if ($fountainPineconeCount >= 3) {
      $fountainPineconeCount = 0;
      // A scroll is randomly dropped in one of the mainland
      // locations, from 0-168.
      $locId = rand(0, 168);
      $locName = 'Location ' . $locId;
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'location')
        ->condition('field_game.entity.title', 'kyrandia')
        ->condition('title', $locName);
      $ids = $query->execute();
      if ($ids) {
        $id = reset($ids);
        $randomLocation = Node::load($id);
        if ($randomLocation) {
          $this->placeItemInLocation($randomLocation, 'scroll');
          $result = "As you toss the pinecone into the fountain, a genie suddenly appears above the fountain and states: \"Thanks for you donation; a scroll has been delivered somewhere within the forest of Kyrandia as a sign of our thanks.\"\n
***\n
The genie then vanishes!";
        }
      }
    }
    else {
      $result = "As you toss the pinecone into the fountain, a voice echoes all around, whispering in the wind: \"The fountain needs more to work its magic!\"";
    }
    $this->saveInstanceSetting($game, 'fountainPineconeCount', $fountainPineconeCount);
    return $result;
  }

  /**
   * Handles dropping shards into the fountain.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   *
   * @return string|null
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function shard(NodeInterface $actingPlayer) {
    $result = NULL;
    // We're looking for something like 'drop shard in fountain'.
    $profile = $this->getKyrandiaProfile($actingPlayer);
    $shardCount = $profile->field_kyrandia_shard_count->value;
    $shardCount++;
    if ($shardCount >= 6) {
      $shardCount = 0;
      // A shard is given to the acting player.
      if ($this->giveItemToPlayer($actingPlayer, 'amulet')) {
        $result = "As you toss the shard into the fountain, a genie magically appears for a moment, hands you an amulet, and then vanishes!";
      }
    }
    else {
      $result = "As you toss the shard into the fountain, a voice echoes all around, whispering in the wind: \"The fountain needs more to work its magic!\"";
    }
    $profile->field_kyrandia_shard_count = $shardCount;
    $profile->save();
    return $result;
  }

  /**
   * Handles dropping things into the fountain.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The player's Kyrandia profile.
   *
   * @return string|null
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function fountain($commandText, NodeInterface $actingPlayer, NodeInterface $profile) {
    // Remove the item.
    $words = explode(' ', $commandText);
    // Assume the second word is the target.
    if (count($words) > 1) {
      $target = $words[1];
      if ($this->takeItemFromPlayer($actingPlayer, $target)) {
        // Player is blessed, so they can create scrolls.
        if (strpos($commandText, 'pinecone') !== FALSE && $profile->field_kyrandia_blessed->value) {
          $result = $this->pinecone($actingPlayer);
        }
        elseif (strpos($commandText, 'shard') !== FALSE) {
          $result = $this->shard($actingPlayer);
        }
        else {
          // Wasn't a pinecone or shard.
          $result = "The fountain sparkles magically in acceptance of your gift!";
        }
      }
    }
    return $result;
  }

  /**
   * Handles dropping things into the reflecting pool.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param \Drupal\node\NodeInterface $profile
   *   The player's Kyrandia profile.
   *
   * @return string|null
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function reflectingPool(string $commandText, NodeInterface $actingPlayer, NodeInterface $profile) {
    $result = NULL;
    $words = explode(' ', $commandText);
    // Assume the second word is the target.
    if (count($words) > 1) {
      $target = $words[1];
      if (strpos($commandText, 'dagger') !== FALSE) {
        if ($this->takeItemFromPlayer($actingPlayer, $target)) {
          if ($this->giveItemToPlayer($actingPlayer, 'sword')) {
            $result = "As you toss the dagger into the pool, it vanishes in circles of ripples.\n
***\n
Suddenly, a beautiful sword rises from the water and levitates into your hands!\n";
          }
        }
        else {
          // Player doesn't have a dagger.
          $result = "Oh, surely thou jest!";
        }
      }
    }
    return $result;
  }

}
