<?php

namespace Drupal\lswitch\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Database;

/**
 * Form that displays list all all language switch avialable.
 */
class MapDomainUpdateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'map_domain_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $lid = '') {
    $standard_languages = self::getStandardLangauges();
    $results = self::get_default_value($lid);
    $form['storage'] = $lid;
    $form['domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain'),
      '#default_value' => $results['domain'],
    // todo: update the text.
      '#description' => $this->t('Please enter only the sub-domain'),
      '#maxlength' => 128,
    ];
    $form['language'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Language'),
      '#default_value' => $results['language'],
      '#options' => $standard_languages,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];
    return $form;
  }

  /**
   *  {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $domain = $form_state->getValue('domain');
    $lang = $form_state->getValue('language');
    $lid = $form['storage'];
    $query = \Drupal::database()->update('domain_lang');
    $query->fields([
      'domain' => $domain,
      'language' => $lang,
    ]);
    $query->condition('lid', $lid);
    $query->execute();
    drupal_set_message('Successfully Updated');
    $response = new RedirectResponse('admin/config/regional/lswitch');
    $response->send();
  }

  /**
   * return standard language array with lang code
   */
  public function getStandardLangauges() {
    $languages = LanguageManager::getStandardLanguageList();
    foreach ($languages as $key => $value) {
      $options[$key] = $value[0] . '/' . $value[1];
    }
    return $options;
  }

  /**
   *  Get default value of domain/lang by lid
   */
  public function get_default_value($lid) {
    // Start db connection.
    $connection = Database::getConnection();
    // Now start transaction will help us in rollback if something bad happens.
    $txn = $connection->startTransaction();
    try {
      $query = $connection->select('domain_lang', 'd')
        ->fields('d')
        ->condition('d.lid', $lid);
      $executed = $query->execute();
    }
    catch (Exception $e) {
      // Something went wrong somewhere, so roll back now.
      $txn->rollBack();
      // Log the exception to watchdog.
      \Drupal::logger('type')->error($e->getMessage());
    }
    $results = $executed->fetchAssoc(\PDO::FETCH_OBJ);
    return $results;
  }

}
