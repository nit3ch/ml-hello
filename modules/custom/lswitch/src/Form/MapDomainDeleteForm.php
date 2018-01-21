<?php

namespace Drupal\lswitch\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Database;

/**
 * Defines a confirmation form to confirm deletion of mapping by lid.
 */
class MapDomainDeleteForm extends ConfirmFormBase {

  /**
   * ID of the item to delete.
   *
   * @var int
   */

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $lid = NULL) {
    $this->id = $lid;
    $form['storage'] = $lid;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $lid = $form['storage'];

    // Start db connection.
    $connection = Database::getConnection();
    // Now start transaction will help us in rollback if something bad happens.
    $txn = $connection->startTransaction();
    try {
      $query = $connection->delete('domain_lang');
      $query->condition('lid', $lid);
      $query->execute();
      // Set message.
      drupal_set_message('Successfully deleted');
      // Redirect to the listing page.
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
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "confirm_delete_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('lswitch.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete this ?');
  }

}
