<?php

namespace Drupal\lswitch\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Database\Database;

class lswitchSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('switchLanguage',280);
    return $events;
  }

  public function switchLanguage(GetResponseEvent $event) {
    //Get the sub-domain which will decide the site language
    $base_url = $_SERVER['HTTP_HOST'];
    $url = strstr(str_replace("www.","",$base_url), ".",true);

    // Get the session param which will update the site language
    $config = \Drupal::config('language.negotiation')->get('session');
    $param = $config['parameter'];

    //start db connection
    $connection = Database::getConnection();
    // now start transaction will help us in rollback if something bad happens
    $txn = $connection->startTransaction();
    try {
      $lang = $connection->select('domain_lang', 'd')
        ->fields('d',array('language'))
        ->condition('d.domain', $url);
      $executed = $lang->execute();
      }
    catch (Exception $e) {
        // Something went wrong somewhere, so roll back now.
        $txn->rollBack();
        // Log the exception to watchdog.
        \Drupal::logger('type')->error($e->getMessage());
      }
    // Fetch assocaited
    $results = $executed->fetchAssoc(\PDO::FETCH_OBJ);
    $language = $results['language'];

    //check if language is set, else we will set en to default
    if(isset($language)) {
      $def_lang =  $language;
    } else {
      $def_lang = 'en';
    }
    $_SESSION[$param] = $def_lang;
  }

}
