<?php

namespace Marcohern\Dimages\Lib\Naming;

use Marcohern\Dimages\Lib\DimageFunctions;

/**
 * It represents an image. Each of the properties
 * corresponds to a field of the image record, which
 * are combined to form the file name.
 */
class BaseDimageName {

  /**
   * Represents the entity of the target.
   * For example, for user profiles, the entity 
   * can be: user-profile.
   */
  public $entity;

  /**
   * The identity of the target. usually a  slug of
   * a propper name. For example, for user profiles
   * the identity can be: tom-cruise or brad-pitt.
   */
  public $identity;

  /**
   * Index of the image. Some items can have more than
   * one image, so we manage them using a numeric index.
   */
  public $index;

  /**
   * Dynamic property, represents the size of the image,
   * which is essentially what the image will be used
   * for. For example, you may want a medium square image
   * to display the user profiles at the top of the profile
   * page. You may also want a small square image of the
   * profile pic as an icon, displayed in comments
   * sections, for instance. So we would have at least 
   * two profiles: user-default and comment-icon.
   * If it's empty, it means this image is the source in it's
   * original size.
   */
  public $profile = '';

  /**
   * Density of the image. It modifies the target size of the
   * resulting image, to adjust for the capacity of the device.
   * examples include: ldpi, mdpi, hdpi, etc.
   */
  public $density = '';

  /**
   * File Extension of the image. such as jpg, jpeg or png.
   */
  public $ext;

  /**
   * Basic function used to format the name or url of the image.
   * The source image format can have any of the following parameters:
   * %entity    users, pets, books...
   * %identity  john-doe, mr-snuggles, harry-potter-and-the-goblet-of-fire
   * %index     Numeric: 1, 2, 3...
   * %pindex    Padded: 001, 002, 003...
   * %profile   large-icon, user-profile, top-bar
   * %density   ldpi, mdpi, hdpi, xhdpi
   * %ext       jpg, jpeg, png
   * 
   * @param $source image format
   */
  protected function format(string $source) : string {
    $source = str_replace('%entity', $this->entity, $source);
    $source = str_replace('%identity', $this->identity, $source);
    $source = str_replace('%index', $this->index, $source);
    $source = str_replace('%pindex', DimageFunctions::padIndex($this->index), $source);
    $source = str_replace('%profile', $this->profile, $source);
    $source = str_replace('%density', $this->density, $source);
    $source = str_replace('%ext', $this->ext, $source);
    return $source;
  }

  /**
   * Determines if this DimageName is a source image, rather than
   * a derived image. Source images are the ones uploaded by users.
   * Derived images are images generated and resized from the source.
   */
  public function isSource() {
    if (empty($this->profile) && empty($this->density)) return true;
    return false;
  }
}