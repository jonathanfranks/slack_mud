<?php

namespace Drupal\word_grammar\Service;

/**
 * WordGrammar service interface.
 */
interface WordGrammarInterface {

  /**
   * Gets the indefinite article (a, an) of the given word.
   *
   * @param string $word
   *   The word to check.
   *
   * @return string
   *   The indefinite article (a or an).
   */
  public function getIndefiniteArticle($word);

  /**
   * Joins words together in a list with or without an Oxford comma.
   *
   * @param array $words
   *   List of words to combine.
   * @param bool $includeOxfordComma
   *   TRUE if list should include an Oxford comma for the last item.
   *   This should always be true. :)
   *
   * @return string
   *   The combined words.
   */
  public function getWordList(array $words, $includeOxfordComma = TRUE);

}
