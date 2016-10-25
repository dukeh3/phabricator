<?php

final class PhabricatorBlockersApplication extends PhabricatorApplication {

  public function getName() {
    return pht('Blockers');
  }

  public function isLaunchable() {
    return false;
  }

  public function canUninstall() {
    return false;
  }

  public function getEventListeners() {
  /*  return array(
      new PhabricatorSubscriptionsUIEventListener(),
    );*/
  }

  public function getRoutes() {
    return array();
      /*return array(
      '/subscriptions/' => array(
        '(?P<action>add|delete)/'.
        '(?P<phid>[^/]+)/' => 'PhabricatorSubscriptionsEditController',
        'list/(?P<phid>[^/]+)/' => 'PhabricatorSubscriptionsListController',
        'transaction/(?P<type>add|rem)/(?<phid>[^/]+)/'
          => 'PhabricatorSubscriptionsTransactionController',
      ),
    );*/
  }

}
