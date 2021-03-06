<?php

namespace Drupal\lswitch\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class Language Mapping.
 *
 * @package Drupal\lwitch\Controller
 */
class LanguageMapping extends ControllerBase {

  /**
   * Display mapping list.
   */
  public function display() {

    $connection = Database::getConnection();
    $sth = $connection->select('domain_lang', 'd')
      ->fields('d');

    $executed = $sth->execute();

    $results = $executed->fetchAll(\PDO::FETCH_OBJ);

    // Iterate results.
    foreach ($results as $row) {
      // Edit link.
      $edit = Url::fromRoute('lswitch.update', ['lid' => $row->lid]);
      $elink = Link::fromTextAndUrl(t('edit'), $edit);
      // Delete link.
      $delete = Url::fromRoute('lswitch.delete', ['lid' => $row->lid]);
      $dlink = Link::fromTextAndUrl(t('delete'), $delete);

      $rows[] = ['domain' => $row->domain, 'language' => $row->language, 'Edit' => ['data' => $elink], 'Delete' => ['data' => $dlink]];
    }

    $form['lswitch_table'] = [
      '#type' => 'table',
      '#header' => ['domain', 'language', 'edit', 'delete'],
      '#rows' => $rows,
      '#empty' => t('No mapping found'),
    ];
    $add = t('Add Domain');
    $form['add_domain'] = [
      '#markup' => "<a href='/admin/config/regional/lswitch/add'> $add  </a>",
    ];

    return $form;
  }

}
