<?php

namespace Drupal\convert_media_tags_to_markup\traits;

/**
 * Implements the Singleton design pattern.
 */
trait Singleton {

  /**
   * Interal instance variable used with the instance() method.
   *
   * @var object|null
   */
  static private $instance;

  /**
   * Implements the Singleton design pattern.
   *
   * Only one instance certain classes should exist per execution.
   *
   * @return mixed
   *   The single instance of the singleton class.
   */
  public static function instance() {
    // See http://stackoverflow.com/questions/15443458
    $class = get_called_class();

    // Not sure why the linter tells me $instance is not used.
    // @codingStandardsIgnoreStart
    if (!$class::$instance) {
    // @codingStandardsIgnoreEnd
      $class::$instance = new $class();
    }
    return $class::$instance;
  }

}
