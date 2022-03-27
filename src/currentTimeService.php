<?php

namespace Drupal\location_time;

use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\Datetime\DateFormatter;

/**
 * Class currentTimeService
 * @package Drupal\location_time\Services
 */
class currentTimeService {

  protected $language_manager;
  protected $date_formatter;

  /**
   * CustomService constructor.
   * @param LanguageDefault $language_manager
   * @param DateFormatter date_formatter
  */ 

  public function __construct(LanguageDefault $language_manager, DateFormatter $date_formatter) {
    $this->language_manager = $language_manager;
    $this->date_formatter = $date_formatter;
  } 

  /**
   * @return \Drupal\Component\Render\MarkupInterface|string
  */

  public function getClockCurrentTime($type, $format = '', $location_timezone) {
    // Get current language code.
    $lang = $this->language_manager->get();
    $langcode = $lang->getId();
    /*Get the current date*/
    $date_formatter = $this->date_formatter;
    $formatted_date = $date_formatter->format(time(), $type, $format, $location_timezone, $langcode);

    return $formatted_date;
  }

}