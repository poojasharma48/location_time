<?php

namespace Drupal\location_time\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides a 'Location Time' Block.
 *
 * @Block(
 *   id = "location_time",
 *   admin_label = @Translation("Location Time"),
 *   category = @Translation("Location Time"),
 * )
 */
   
class locTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $configfactory;
  protected $current_time_service;

  /**
    * @param array $configuration
    * @param string $plugin_id
    * @param mixed $plugin_definition
  */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, $current_time_service) {
      parent::__construct($configuration, $plugin_id, $plugin_definition);
      $this->configfactory = $config_factory;
      $this->current_time_service = $current_time_service;
  }

  /**
   * {@inheritdoc}
  */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('location_time.current_time_service')
    );
  }

  public function build() {
    $config = $this->configfactory->get('location_time.timezone');
    $country = $config->get('country');  
    $city = $config->get('city');  
    $location_timezone = $config->get('location_timezone'); 
    $date_format = 'j M Y - h:i a'; 
    $date_type = 'custom';
    /*Get current time by calling the custom 'current time' service.*/
    $time = $this->current_time_service->getClockCurrentTime($date_type, $date_format, $location_timezone);
    /*Get the name of the offset, e.g. 'GMT'.*/
    $offset_name = $this->current_time_service->getClockCurrentTime($date_type, 'T', $location_timezone);
    /*Get the time zone offset in seconds.*/
    $offset_seconds = $this->current_time_service->getClockCurrentTime($date_type, 'Z', $location_timezone);
    /* Get Daylight Savings Time information. '1' for yes, '0' for no.*/
    $daylight_savings_time = $this->current_time_service->getClockCurrentTime($date_type, 'I', $location_timezone);
    $js_settings = array(
      'time'                  => $time,
      'time_zone'             => $location_timezone,
      'date_format'           => $date_format,
      'offset_name'           => $offset_name,
      'offset_seconds'        => $offset_seconds,
      'daylight_savings_time' => $daylight_savings_time,
    );

    $build['#attached']['library'][] = 'location_time/clock_lib';
    $build['#attached']['drupalSettings'] = $js_settings;

    $build['#theme'] = 'loc_time_block';
    $build['#country'] = $country;
    $build['#city'] = $city;
    $build['#current_time'] = $time;

    return $build;

  }
   
}

?>
