<?php

namespace Drupal\background_information\Form;

use Drupal\system\Form\SiteInformationForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Background Information form.
 */
class SiteInformationChildForm extends SiteInformationForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'background_information_child_site_information';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Retrieve the system.site configuration.
    $site_config = $this->config('system.site');

    // Get the original form from the class we are extending.
    $form = parent::buildForm($form, $form_state);

    // Add siteapikey field to the site information section of the form.
    $form['site_information']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => t('Site API Key'),
      '#default_value' => $site_config->get('siteapikey') ?: 'No API Key yet',
      '#description' => t("Configure Site API Key."),
    ];

    // Change form submit button text to 'Update Configuration'
    // if there is already value of siteapikey.
    if ($site_config->get('siteapikey')) {
      $form['actions']['submit']['#value'] = t('Update configuration');
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Let's not allow blank value for siteapikey.
    if ($form_state->isValueEmpty('siteapikey')) {
      $form_state->setErrorByName('siteapikey', "Site API Key field can't be left blank.");
    }
    // Warn use for setting default value as siteapikey.
    if ($form_state->getValue('siteapikey') === 'No API Key yet') {
      $form_state->setErrorByName('siteapikey', "Invalid Site API Key field value.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get siteapikey value.
    $site_api_key = $form_state->getValue('siteapikey');

    // Update siteapikey value Database.
    $this->config('system.site')->set('siteapikey', $site_api_key)->save();
    $this->messenger()->addStatus($this->t('The Site API Key [@api_key] has been saved.', ['@api_key' => $site_api_key]));

    // Pass the values to the parent form so that it form can process
    // the remaining form.
    parent::submitForm($form, $form_state);
  }

}
