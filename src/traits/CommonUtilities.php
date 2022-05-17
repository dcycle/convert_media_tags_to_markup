<?php

namespace Drupal\convert_media_tags_to_markup\traits;

use Drupal\convert_media_tags_to_markup\ConvertMediaTagsToMarkup\Entity;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Utility\Error;
use Drupal\file\Entity\File;

/**
 * General utilities trait.
 *
 * If your class needs to use any of these, add "use CommonUtilities" your class
 * and these methods will be available and mockable in tests.
 */
trait CommonUtilities {

  /**
   * Mockable wrapper around decodeEntities().
   */
  public function decodeEntities($text) {
    return Html::decodeEntities($text);
  }

  /**
   * Mockable wrapper around Json::decode().
   */
  public function drupalJsonDecode($tag) {
    return Json::decode($tag);
  }

  /**
   * Mockable wrapper around File::load(). Also throws an exception.
   */
  public function fileLoad($fid) {
    $file = File::load($fid);

    if (!$file) {
      throw new \Exception('Could not load media object');
    }

    return $file;
  }

  /**
   * Get all entities of a specific type and bundle.
   *
   * @param string $type
   *   A type such as node.
   * @param string $bundle
   *   A bundle such as article.
   *
   * @return array
   *   Array of
   *   \Drupal\convert_media_tags_to_markup\ConvertMediaTagsToMarkup\Entity
   *   objects.
   */
  protected function getAllEntities(string $type, string $bundle) : array {
    $values = [
      'type' => $bundle,
    ];
    $nodes = \Drupal::entityTypeManager()
      ->getListBuilder($type)
      ->getStorage()
      ->loadByProperties($values);
    $return = [];
    foreach ($nodes as $node) {
      $return[] = new Entity($node);
    }
    return $return;
  }

  /**
   * Log a string to the watchdog.
   *
   * @param string $string
   *   String to be logged.
   */
  public function watchdog(string $string) {
    \Drupal::logger('steward_common')->notice($string);
  }

  /**
   * Log an error to the watchdog.
   *
   * @param string $string
   *   String to be logged.
   */
  public function watchdogError(string $string) {
    \Drupal::logger('steward_common')->error($string);
  }

  /**
   * Log a \Throwable to the watchdog.
   *
   * @param \Throwable $t
   *   A \throwable.
   * @param mixed $message
   *   The message to store in the log. If empty, a text that contains all
   *   useful information about the passed-in exception is used.
   * @param mixed $variables
   *   Array of variables to replace in the message on display or NULL if
   *   message is already translated or not possible to translate.
   * @param mixed $severity
   *   The severity of the message, as per RFC 3164.
   * @param mixed $link
   *   A link to associate with the message.
   */
  public function watchdogThrowable(\Throwable $t, $message = NULL, $variables = [], $severity = RfcLogLevel::ERROR, $link = NULL) {

    // Use a default value if $message is not set.
    if (empty($message)) {
      $message = '%type: @message in %function (line %line of %file).';
    }

    if ($link) {
      $variables['link'] = $link;
    }

    $variables += Error::decodeException($t);

    \Drupal::logger('steward_common')->log($severity, $message, $variables);
  }

}
