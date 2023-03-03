<?php

namespace Drupal\convert_media_tags_to_markup\Plugin\Filter;

use Drupal\convert_media_tags_to_markup\ConvertMediaTagsToMarkup\App;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\convert_media_tags_to_markup\traits\CommonUtilities;

/**
 * Provides a filter for converting legacy media tags to markup.
 *
 * See ./README.md for details.
 * This code is adapted from
 * http://cgit.drupalcode.org/media/tree/modules/media_wysiwyg/includes/media_wysiwyg.filter.inc?h=7.x-3.x.
 *
 * @Filter(
 *   id = "convert_legacy_media_tags_to_markup",
 *   module = "convert_media_tags_to_markup",
 *   title = @Translation("Convert Legacy Media Tags to Markup"),
 *   description = @Translation("See https://github.com/dcycle/convert_media_tags_to_markup for details."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 * )
 */
class ConvertLegacyMediaTagsToMarkup extends FilterBase {

  use CommonUtilities;

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    try {
      return new FilterProcessResult(App::instance()->filterText($text));
    }
    catch (\Exception $t) {
      $this->watchdogThrowable($t);
      return new FilterProcessResult($text);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return '<p>Coverts legacy imported media tags to images.</p>';
  }

}
