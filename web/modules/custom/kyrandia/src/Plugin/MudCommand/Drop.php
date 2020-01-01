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
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Players drop things into the stump at Loc 18 to reach level 6.
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if (strpos($commandText, 'stump') !== FALSE && $loc->getTitle() == 'Location 18') {
      $this->stump($commandText, $actingPlayer, $profile, $results);
    }
    elseif (strpos($commandText, 'fountain') !== FALSE && $loc->getTitle() == 'Location 38') {
      $this->fountain($commandText, $actingPlayer, $profile, $results);
    }
    elseif (strpos($commandText, 'pool') !== FALSE && $loc->getTitle() == 'Location 182') {
      $this->reflectingPool($commandText, $actingPlayer, $results);
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
        // Duplicate result array.
        $result = [];
        $plugin->perform($commandText, $actingPlayer, $result);
        // @TODO: Check max object locations for DROPIT1.
        if (array_key_exists(':item', $result[$actingPlayer->id()][0]->getArguments())) {
          // Player successfully dropped an item.
          $droppedObjectArticle = $this->wordGrammar->getIndefiniteArticle($result[$actingPlayer->id()][0]->getArguments()[':item']);
          $droppedObject = $droppedObjectArticle . ' ' . $result[$actingPlayer->id()][0]->getArguments()[':item'];
          $dropLocation = $loc->field_object_location->value;
          $othersMessage = sprintf($this->gameHandler->getMessage('DROPIT3'), $actingPlayer->field_display_name->value, $droppedObject, $dropLocation);
        }
        else {
          // Player did not successfully drop an item.
          // Here we override the stock drop message.
          $result[$actingPlayer->id()][0] = $this->gameHandler->getMessage('DROPIT4');
          $othersMessage = t(':actor is acting very oddly.', [
            ':actor' => $actingPlayer->field_display_name->value,
          ]);
        }
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
      }
    }
    if (!$results) {
      // Usually this is because the player just typed "drop".
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('DROPIT5');
      $othersMessage = t(':actor is looking a little queer!', [
        ':actor' => $actingPlayer->field_display_name->value,
      ]);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
    }

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
    // This usually goes like "drop garnet in stump" so let's assume that the
    // first word is the verb and the second word is the item.
    $words = explode(' ', $commandText);
    if (count($words) > 1) {
      $target = $words[1];
    }
    else {
      $target = NULL;
    }
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
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function stump($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
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
    $item = $this->gameHandler->playerHasItem($actingPlayer, $target, TRUE);
    $loc = $actingPlayer->field_location->entity;
    if ($item === FALSE) {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('BGEM05');
      $othersMessage = sprintf($this->gameHandler->getMessage('BGEM06'), $actingPlayer->field_display_name->value);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
    }
    else {
      $itemTitle = $item->getTitle();
      $stumpStoneIndex = array_search($itemTitle, $stumpStones);
      if ($stumpStoneIndex === $currentStumpStone) {
        // Match! This is the last one, so advance to level 6 if the user is
        // level 5!
        if ($currentStumpStone == count($stumpStones) - 1 && $profile->field_kyrandia_level->entity->getName() == '5') {
          $this->gameHandler->advanceLevel($profile, 6);
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('BGEM00');
          $othersMessage = sprintf($this->gameHandler->getMessage('BGEM01'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          $this->gameHandler->giveSpellToPlayer($actingPlayer, 'hotkiss');
        }
        else {
          // Match! Set the stone index to the next one.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('BGEM02');
          $othersMessage = sprintf($this->gameHandler->getMessage('BGEM03'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
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
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('BGEM04');
        $othersMessage = sprintf($this->gameHandler->getMessage('BGEM03'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
  }

  /**
   * Handles dropping pinecones into the fountain.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function pinecone(NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    // We're looking for something like 'drop pinecone in fountain'.
    $game = $actingPlayer->field_game->entity;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    $fountainPineconeCount = $this->gameHandler->getInstanceSetting($game, 'fountainPineconeCount', 0);
    if ($profile->field_kyrandia_blessed->value) {
      $fountainPineconeCount++;
    }
    if ($fountainPineconeCount >= 3) {
      $fountainPineconeCount = 0;
      // A scroll is randomly dropped in one of the mainland
      // locations, from 0-168.
      $locId = $this->generateRandomNumber($game, 0, 168);
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
          $this->gameHandler->placeItemInLocation($randomLocation, 'scroll');
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MAGF00');
          $othersMessage = sprintf($this->gameHandler->getMessage('MAGF01'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
    }
    else {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MAGF04');
      $othersMessage = sprintf($this->gameHandler->getMessage('MAGF07'), $actingPlayer->field_display_name->value);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
    }
    $this->gameHandler->saveInstanceSetting($game, 'fountainPineconeCount', $fountainPineconeCount);
  }

  /**
   * Handles dropping shards into the fountain.
   *
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function shard(NodeInterface $actingPlayer, array &$results) {
    // We're looking for something like 'drop shard in fountain'.
    $game = $actingPlayer->field_game->entity;
    $loc = $actingPlayer->field_location->entity;
    $shardCount = $this->gameHandler->getInstanceSetting($game, 'fountainShardCount', 0);
    $shardCount++;
    if ($shardCount >= 6) {
      $shardCount = 0;
      // A shard is given to the acting player.
      if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'amulet')) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MAGF05');
        $othersMessage = sprintf($this->gameHandler->getMessage('MAGF03'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
    else {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MAGF06');
      $othersMessage = sprintf($this->gameHandler->getMessage('MAGF03'), $actingPlayer->field_display_name->value);
      $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
    }
    $this->gameHandler->saveInstanceSetting($game, 'fountainShardCount', $shardCount);
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
   * @param array $results
   *   The results array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function fountain($commandText, NodeInterface $actingPlayer, NodeInterface $profile, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    // Remove the item.
    $words = explode(' ', $commandText);
    // Assume the second word is the target.
    if (count($words) > 1) {
      $target = $words[1];
      if ($this->gameHandler->takeItemFromPlayer($actingPlayer, $target)) {
        if (strpos($commandText, 'pinecone') !== FALSE) {
          $this->pinecone($actingPlayer, $results);
        }
        elseif (strpos($commandText, 'shard') !== FALSE) {
          $this->shard($actingPlayer, $results);
        }
        else {
          // Wasn't a pinecone or shard.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('MAGF02');
          $othersMessage = sprintf($this->gameHandler->getMessage('MAGF03'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
    }
  }

  /**
   * Handles dropping things into the reflecting pool.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $results
   *   The results array.
   */
  protected function reflectingPool(string $commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    $words = explode(' ', $commandText);
    // Assume the second word is the target.
    if (count($words) > 1) {
      $target = $words[1];
      if (strpos($commandText, 'dagger') !== FALSE) {
        if ($this->gameHandler->takeItemFromPlayer($actingPlayer, $target)) {
          if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'sword')) {
            $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('REFM00');
            $othersMessage = sprintf($this->gameHandler->getMessage('REFM01'), $actingPlayer->field_display_name->value);
            $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
          }
        }
        else {
          // Player doesn't have a dagger.
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('REFM02');
          $othersMessage = sprintf($this->gameHandler->getMessage('REFM01'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        }
      }
    }
  }

}
