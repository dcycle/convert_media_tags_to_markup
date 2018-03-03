<?php

namespace Drupal\convert_media_tags_to_markup\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Component\Serialization\Json;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\Html;

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
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class ConvertLegacyMediaTagsToMarkup extends FilterBase {

  const MEDIA_WYSIWYG_TOKEN_REGEX = '/\[\[\{.*?"type":"media".+?\}\]\]/s';

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
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    try {
      $rendered_text = $text;
      $count = 1;
      preg_match_all(self::MEDIA_WYSIWYG_TOKEN_REGEX, $text, $matches);
      if (!empty($matches[0])) {
        foreach ($matches[0] as $match) {
          $replacement = $this->tokenToMarkup(array($match), FALSE, $langcode);
          $rendered_text = str_replace($match, $replacement, $rendered_text, $count);
        }
      }
      return new FilterProcessResult($rendered_text);
    }
    catch (\Exception $e) {
      $this->watchdogException($e);
      return new FilterProcessResult($text);
    }
  }

  /**
   * Turn an array of css property => value pairs to a string.
   *
   * @param array $properties
   *   A keyed array with css property => value pairs.
   *
   * @see media_wysiwyg_parse_css_declarations()
   */
  public function stringifyCssDeclarations(array $properties) {
    $declarations = array();
    foreach ($properties as $property => $value) {
      $declarations[] = $property . ':' . $value;
    }
    return implode(';', $declarations);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return '<p>Coverts legacy imoprted media tags to images.</p>';
  }

  /**
   * Replace callback to convert a media file tag into HTML markup.
   *
   * This code is adapted from
   * http://cgit.drupalcode.org/media/tree/modules/media_wysiwyg/includes/media_wysiwyg.filter.inc?h=7.x-3.x.
   *
   * @param string $match
   *   Takes a match of tag code.
   * @param bool $wysiwyg
   *   Set to TRUE if called from within the WYSIWYG text area editor.
   *
   * @return string
   *   The HTML markup representation of the tag, or an empty string on failure.
   */
  public function tokenToMarkup($match, $wysiwyg = FALSE, $langcode = NULL) {
    try {
      $match = str_replace("[[", "", $match);
      $match = str_replace("]]", "", $match);
      $tag = $match[0];

      if (!is_string($tag)) {
        throw new \Exception('Unable to find matching tag');
      }

      $tag_info = $this->drupalJsonDecode($tag);
      if (!isset($tag_info['fid'])) {
        throw new \Exception('No file Id');
      }

      $file = $this->fileLoad($tag_info['fid']);
      $uri = $file->getFileUri();
      $filepath = file_create_url($uri);
      $alt = empty($tag_info['attributes']['alt']) ? '' : $tag_info['attributes']['alt'];
      $title = $alt;
      $height = empty($tag_info['attributes']['height']) ? '' : 'height="' . $tag_info['attributes']['height'] . '"';
      $width = empty($tag_info['attributes']['width']) ? '' : 'width="' . $tag_info['attributes']['width'] . '"';
      $output = '
      <div class="media media-element-container media-default">
        <div id="file-' . $tag_info['fid'] . '" class="file file-image">
          <div class="content">
            <img alt="' . $alt . '" title="' . $title . '" class="media-element  file-default" src="' . $filepath . '" ' . $height . ' ' . $width . '>
          </div>
        </div>
      </div>';
      return $output;
    }
    catch (\Exception $e) {
      $this->watchdogException($e);
      return '';
    }
  }

  /**
   * Mockable wrapper around watchdog_exception().
   */
  public function watchdogException(\Exception $e) {
    watchdog_exception('convert_media_tags_to_markup', $e);
  }

}
