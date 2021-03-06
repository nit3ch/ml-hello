<?php

namespace Drupal\lswitch\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Database;
use Drupal\language\Entity\ConfigurableLanguage;



/**
 * Form that displays list all all language switch avialable.
 */
class MapDomainForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'map_domain_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get all standard languages.
    $standard_languages = self::getStandardLangauges();
    $form['domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain'),
      '#description' => $this->t('Please enter only the third level domain/sub-domain'),
    ];
    $form['language'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Language'),
      '#options' => $standard_languages,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('ADD'),
    ];
    return $form;
  }

  /**
   *  {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $domain = $form_state->getValue('domain');
    $lang = $form_state->getValue('language');

    // add the language first
    // todo: Chekc if it is already enabled
    $language = ConfigurableLanguage::createFromLangcode('hi');
    $language->save();
    // Start db connection.
    $connection = Database::getConnection();
    // Now start transaction will help us in rollback if something bad happens.
    $txn = $connection->startTransaction();
    try {
      $query = $connection->insert('domain_lang')
        ->fields([
          'domain',
          'language',
        ])
        ->values([
          $domain,
          $lang,
        ])
        ->execute();
      drupal_set_message('succesfully saved');
      $response = new RedirectResponse('/admin/config/regional/lswitch');
      $response->send();
    }
    catch (Exception $e) {
      // Something went wrong somewhere, so roll back now.
      $txn->rollBack();
      // Log the exception to watchdog.
      \Drupal::logger('type')->error($e->getMessage());
    }
  }

  /**
   * Return assosicative array language id and label.
   */
  public function getStandardLangauges() {
    $languages = LanguageManager::getStandardLanguageList();
    foreach ($languages as $key => $value) {
      $options[$key] = $value[0] . '/' . $value[1];
    }
    return $options;
  }

}
