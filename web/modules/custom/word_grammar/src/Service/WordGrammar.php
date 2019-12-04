<?php

namespace Drupal\word_grammar\Service;

use UseAllFive\AvsAnBundle\AvsAn;
use UseAllFive\AvsAnBundle\Dictionary\Dictionary;

/**
 * Send messages to Slack.
 */
class WordGrammar implements WordGrammarInterface {

  /**
   * {@inheritdoc}
   */
  public function getIndefiniteArticle($word) {
    $dictionary = new Dictionary();
    $aOrAn = new AvsAn($dictionary);
    $article = $aOrAn->query($word)['article'];
    return $article;
  }

  /**
   * {@inheritdoc}
   */
  public function getWordList(array $words, $includeOxfordComma = TRUE) {
    // Even if we're including the comma, we don't use one if there are only
    // two words.
    $oxfordComma = $includeOxfordComma && count($words) > 2 ? ',' : '';
    if (count($words) > 1) {
      $lastWord = array_pop($words);
      $results = implode(', ', $words) . $oxfordComma . ' and ' . $lastWord;
    }
    elseif (count($words) == 1) {
      $results = reset($words);
    }
    else {
      $results = $words;
    }
    return $results;
  }

}
