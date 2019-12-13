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
      $words = explode(' ', $commandText);
      if (count($words) > 1) {
        // Player needs to drop something.
        // Not a special drop. Handle this like a regular drop.
        /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
        $pluginManager = \Drupal::service('plugin.manager.mud_command');
        /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
        $plugin = $pluginManager->createInstance('drop');
        $result = $plugin->perform($commandText, $actingPlayer);
        // @TODO: Check max object locations for DROPIT1.
        if (array_key_exists(':item', $result[$actingPlayer->id()][0]->getArguments())) {
          // Player successfully dropped an item.
          $droppedObjectArticle = $this->wordGrammar->getIndefiniteArticle($result[$actingPlayer->id()][0]->getArguments()[':item']);
          $droppedObject = $droppedObjectArticle . ' ' . $result[$actingPlayer->id()][0]->getArguments()[':item'];
          $dropLocation = $loc->field_object_location->value;
          $othersMessage = sprintf($this->getMessage('DROPIT3'), $actingPlayer->field_display_name->value, $droppedObject, $dropLocation);
        }
        else {
          // Player did not successfully drop an item.
          // Here we override the stock drop message.
          $result[$actingPlayer->id()][0] = $this->getMessage('DROPIT4');
          $othersMessage = t(':actor is acting very oddly.', [
            ':actor' => $actingPlayer->field_display_name->value,
          ]);
        }
        $this->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
      }
    }
    if (!$result) {
      // Usually this is because the player just typed "drop".
      $result[$actingPlayer->id()][] = $this->getMessage('DROPIT5');
      $othersMessage = t(':actor is looking a little queer!', [
        ':actor' => $actingPlayer->field_display_name->value,
      ]);
      $this->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
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
    $item = $this->playerHasItem($actingPlayer, $target, TRUE);
    if ($item === FALSE) {
      $result = t("But you don't have one, you hallucinating fool!");
    }
    else {
      $itemTitle = $item->getTitle();
      $stumpStoneIndex = array_search($itemTitle, $stumpStones);
      if ($stumpStoneIndex === $currentStumpStone) {
        // Match! This is the last one, so advance to level 6 if the user is
        // level 5!
        if ($currentStumpStone == count($stumpStones) - 1 && $profile->field_kyrandia_level->entity->getName() == '5') {
          $this->advanceLevel($profile, 6);
          $result = $this->getMessage('BGEM00');
        }
        else {
          // Match! Set the stone index to the next one.
          $result = $this->getMessage('BGEM02');
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
        $result = $this->getMessage('BGEM04');
      }
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
          $result = $this->getMessage('MAGF00');
        }
      }
    }
    else {
      $result = $this->getMessage('MAGF04');
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
        $result = $this->getMessage('MAGF05');
      }
    }
    else {
      $result = $this->getMessage('MAGF06');
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
          $result = $this->getMessage('MAGF02');
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
            $result = $this->getMessage('REFM00');
          }
        }
        else {
          // Player doesn't have a dagger.
          $result = $this->getMessage('REFM02');
        }
      }
    }
    return $result;
  }

}
