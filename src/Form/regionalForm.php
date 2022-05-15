<?php
namespace Drupal\location_time\Form;

use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface; 
use Drupal\Core\Cache\Cache;

/**
 * Form handler for the class.
 *
 * @internal
 */
class regionalForm extends ConfigFormBase {
  /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'location_time.timezone',  
    ];  
  }  
  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'loc_timezone_form';
  } 
  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('location_time.timezone');  
    $country = $config->get('country');  
    $city = $config->get('city');  
    $location_timezone = $config->get('location_timezone');  
    /*set timezone opts*/
    $timezone_opts = array("America/Chicago" => "Chicago", "America/New_York" => "New_York", "Asia/Tokyo" => "Tokyo", "Asia/Dubai" => "Dubai", "Asia/Kolkata" => "Kolkata", "Europe/Amsterdam" => "Amsterdam", "Europe/Oslo" => "Oslo", "Europe/London" => "London");

    $form['loc_time_info'] = [
      '#type' => 'details',
      '#title' => t('loaction Time Details'),
      '#open' => TRUE,
    ];
    $form['loc_time_info']['country'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Enter Country'),
      '#default_value' => isset($country) ? $country : '',
      '#required' => true,
    );
    $form['loc_time_info']['city'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Enter City'),
      '#default_value' => isset($city) ? $city : '',
      '#required' => true,
    );
    $form['loc_time_info']['location_timezone'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select timezone'),
      '#empty_option' => $this->t('Select timezone'),
      '#default_value' => isset($location_timezone) ? $location_timezone : '',
      '#required' => true,
      '#options' => $timezone_opts
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save Configuration'),
      '#button_type' => 'primary',
    );
    
    return $form; 
  }     
  /**
   * Validate the title and the checkbox of the form
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * 
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {  
    
  } 
  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    parent::submitForm($form, $form_state);  
    $this->config('location_time.timezone')  
      ->set('city', $form_state->getValue('city'))  
      ->set('country', $form_state->getValue('country'))  
      ->set('location_timezone', $form_state->getValue('location_timezone'))  
      ->save();       
   /*invalidate block cache when for, submit so that updated timezone display*/
    Cache::invalidateTags(['config:block.block.locationtime']); 
  }

}
