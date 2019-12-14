<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Drupal\node\Entity\Node;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @Given :type :label content is updated to:
   */
  public function contentIsUpdatedTo($type, $label, TableNode $table) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', $type)
      ->condition('title', $label);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $node = Node::load($id);
      foreach ($table->getHash() as $nodeHash) {
        foreach ($nodeHash as $column => $value) {
          $node->{$column} = $value;
        }
      }
      $node->save();
    }
    else {
      throw new \Exception(sprintf('No node with title of %s exists.', $label));
    }
  }

  /**
   * @Given the :type :title content is deleted
   */
  public function theContentIsDeleted($type, $title) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', $type)
      ->condition('title', $title);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $node = Node::load($id);
      $node->delete();
    }
    else {
      throw new \Exception(sprintf('No node with title of %s exists.', $title));
    }
  }

}
