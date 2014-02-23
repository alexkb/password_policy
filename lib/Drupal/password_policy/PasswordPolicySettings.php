<?php

namespace Drupal\password_policy;

use Drupal\Core\Config\ConfigFactoryInterface;
// use something like this for having a class that does all this password stuff.
// use Drupal\Core\Path\AliasManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PasswordPolicySettings extends ConfigFormBase {
  
  /**
   * {@inheritdoc}
   */
  function getFormId() {
    return "password_policy_settings";
  }
  
  /**
   * {@inheritdoc}
   */
  function buildForm(array $form, array &$form_state) {
    $site_config = $this->configFactory->get('password_poicy');
    
    $form['expiration'] = array(
      '#type' => 'fieldset',
      '#title' => t('Expiration settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE
    );
    $form['expiration']['password_policy_admin'] = array(
      '#type' => 'checkbox',
      '#title' => t('Admin (UID=1) password expires.'),
      '#default_value' => $site_config->get('password_policy_admin'),
      '#description' => t('Admin account password will obey expiration policy.'),
    );
    $form['expiration']['password_policy_begin'] = array(
      '#type' => 'radios',
      '#title' => t('Beginning of password expirations'),
      '#default_value' => $site_config->get('password_policy_begin'),
      '#options' => array(
        '0' => t('After expiration time from setting a default policy (all passwords are valid during the expiration time from setting the default policy, and after that older than expiration time passwords expire).'), 
        '1' => t('Setting a default policy (passwords older than expiration time expire after setting the default policy, retroactive behaviour).')),
    );
    $form['expiration']['password_policy_block'] = array(
      '#type' => 'radios',
      '#title' => t('Blocking expired accounts'),
      '#default_value' => $site_config->get('password_policy_block', 0),
      '#options' => array(
        '0' => t('Expired accounts are blocked. Only administrators can unblock them.'), 
        '1' => t('The user with expired account is not blocked, but sent to a change password page. If the password is not changed, the account is blocked and the user cannot login again.')),
    );

    // Visibility
    $form['visibility'] = array(
      '#type' => 'fieldset',
      '#title' => t('Visibility settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE
    );
    $form['visibility']['password_policy_show_restrictions'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show restrictions on password change page.'),
      '#default_value' => $site_config->get('password_policy_show_restrictions'),
      '#description' => t('Should password restrictions be listed on the password change page. A javascript warning block will be shown anyways if ithe typed in password does not meet the restrictions.'),
    );

    // E-mail notification settings.
    $form['email'] = array(
      '#type' => 'fieldset',
      '#title' => t('E-mail notification settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE
    );
    $form['email']['password_policy_warning_subject'] = array(
      '#type' => 'textfield',
      '#title' => t('Subject of warning e-mail'),
      '#default_value' => '', //_password_policy_mail_text('warning_subject'),
      '#maxlength' => 180,
      '#description' => t('Customize the subject of the warning e-mail message, which is sent to remind of password expiration.') .' '. t('Available variables are:') .' !username, !site, !uri, !uri_brief, !mailto, !date, !login_uri, !edit_uri, !days_left.',
    );
    $form['email']['password_policy_warning_body'] = array(
      '#type' => 'textarea',
      '#title' => t('Body of warning e-mail'),
      '#default_value' => '', //_password_policy_mail_text('warning_body'),
      '#rows' => 15,
      '#description' => t('Customize the body of the warning e-mail message, which is sent to remind of password expiration.') .' '. t('Available variables are:') .' !username, !site, !uri, !uri_brief, !mailto, !date, !login_uri, !edit_uri, !days_left.',
    );
    
    return parent::buildForm($form, $form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  function validate(array $form, array &$form_state) {
    parent::validateForm($form, $form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    
    $this->configFactory->get('password_policy')
      ->set('password_policy_admin', $form_state['values']['password_policy_admin'])
      ->set('password_policy_begin', $form_state['values']['password_policy_begin'])
      ->set('password_policy_block', $form_state['values']['password_policy_block'])
      ->set('password_policy_show_restrictions', $form_state['values']['password_policy_show_restrictions'])
      ->set('page.front', $form_state['values']['site_frontpage'])
      ->set('page.403', $form_state['values']['site_403'])
      ->set('page.404', $form_state['values']['site_404'])
      ->save();
    
    parent::submitForm($form, $form_state);
  }
}