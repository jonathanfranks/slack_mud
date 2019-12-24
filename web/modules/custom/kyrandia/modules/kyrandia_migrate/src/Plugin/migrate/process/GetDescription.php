<?php

namespace Drupal\kyrandia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Smalot\PdfParser\Parser;

/**
 * Extracts location descriptions from MSG file.
 *
 * @MigrateProcessPlugin(
 *   id = "kyrandia_get_description"
 * )
 *
 * @code
 *   pdf_text:
 *     plugin: kyrandia_get_description
 *     source: description id
 * @endcode
 */
class GetDescription extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $contents = file_get_contents($this->configuration['message_file_path']);
    $contents = str_replace("\r\n\r\n\r\n", "\n\n\n", $contents);
    $raw_rows = explode("\n\n\n", $contents);
    $rows = [];
    foreach ($raw_rows as $raw_row) {
      if (strpos($raw_row, $this->configuration['trailing_text']) !== FALSE) {
        // Needs to end with that in order to be a valid location text.
        // There's at least one row with a {.. so check for which to do.
        $separator = "{";
        $split_row = explode($separator, $raw_row);
        $id = trim($split_row[0]);
        $text = $split_row[1];
        // Remove \r.
        $text = str_replace("\r", '', $text);
        // Convert \n to space.
        $text = str_replace("\n", ' ', $text);
        // Remove multiple spaces (twice?, figure this out later).
        $text = str_replace("  ", ' ', $text);
        $text = str_replace("  ", ' ', $text);
        // Remove trailing token.
        $text = str_replace($this->configuration['trailing_text'], '', $text);
        $text = trim($text);
        $rows[$id] = $text;
      }
    }
    $text = array_key_exists($value, $rows) ? $rows[$value] : '';
    return $text;
  }

}
