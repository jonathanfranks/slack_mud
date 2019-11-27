<?php

namespace Drupal\kyrandia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Smalot\PdfParser\Parser;

/**
 * Extracts location descriptions from MSG file.
 *
 * @MigrateProcessPlugin(
 *   id = "kyrandia_location_get_description"
 * )
 *
 * @code
 *   pdf_text:
 *     plugin: kyrandia_location_get_description
 *     source: filename
 * @endcode
 */
class LocationGetDescription extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $text = '';

    $id_value = 'KRD' . str_pad($value, 3, '0', STR_PAD_LEFT);

    $ids = [];
    $id_contents = file_get_contents($this->configuration['index_file_path']);
    $raw_ids = explode("\n", $id_contents);
    foreach ($raw_ids as $raw_id) {
      $raw_id = trim($raw_id);
      if ($raw_id) {
        $pos_begin = strpos($raw_id, '{');
        $pos_end = strpos($raw_id, '}');
        $id_string = substr($raw_id, $pos_begin + 1, $pos_end - $pos_begin);
        $pos_comma = strpos($id_string, ',');
        $id = substr($id_string, 0, $pos_comma);
        $ids[] = $id;
      }
    }


    $contents = file_get_contents($this->configuration['message_file_path']);
    $contents = str_replace("\r\n\r\n\r\n", "\n\n\n", $contents);
    $raw_rows = explode("\n\n\n", $contents);
    $rows = [];
    foreach ($raw_rows as $raw_row) {
      $row = [];
      if (strpos($raw_row, '} T Kyrandia locations text') !== FALSE) {
        // Needs to end with that in order to be a valid location text.
        // There's at least one row with a {.. so check for which to do.
        $separator = "";
        if (strpos($raw_row, " {...") !== FALSE) {
          $separator = " {...";
        }
        elseif (strpos($raw_row, " {..") !== FALSE) {
          $separator = " {..";
        }
        $split_row = explode($separator, $raw_row);
        $id = $split_row[0];
        $text = $split_row[1];
        // Remove \r.
        $text = str_replace("\r", '', $text);
        // Convert \n to space.
        $text = str_replace("\n", ' ', $text);
        // Remove multiple spaces (twice?, figure this out later).
        $text = str_replace("  ", ' ', $text);
        $text = str_replace("  ", ' ', $text);
        // Remove trailing token.
        $text = str_replace('} T Kyrandia locations text', '', $text);
        $text = trim($text);
        $rows[$id] = $text;
      }
    }
    $text = array_key_exists($id_value, $rows) ? $rows[$id_value] : '';
    return $text;
  }

}
