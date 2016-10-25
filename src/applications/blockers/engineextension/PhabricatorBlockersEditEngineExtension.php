<?php

final class PhabricatorBlockersEditEngineExtension
  extends PhabricatorEditEngineExtension {

  const EXTENSIONKEY = 'blockers.blocker';

  public function getExtensionPriority() {
    return 750;
  }

  public function isExtensionEnabled() {
    return true;
  }

  public function getExtensionName() {
    return pht('Blockers');
  }

  public function supportsObject(
    PhabricatorEditEngine $engine,
    PhabricatorApplicationTransactionInterface $object) {
    return ($object instanceof PhabricatorSubscribableInterface);
  }

  public function buildCustomEditFields(
    PhabricatorEditEngine $engine,
    PhabricatorApplicationTransactionInterface $object) {
	
	
    $blockers_type = PhabricatorTransactions::TYPE_BLOCKERS;

    $object_phid = $object->getPHID();
    if ($object_phid) {
      $blocks_phids = PhabricatorBlockersQuery::loadSubscribersForPHID(
        $object_phid);
    } else {
      $blocks_phids = array();
    }

    $blockers_field = id(new PhabricatorBlockersEditField())
      ->setKey('blockersPHIDs')
      ->setLabel(pht('Blockers'))
      ->setEditTypeKey('blockers')
      ->setAliases(array('blocker', 'blockers'))
      ->setIsCopyable(true)
      ->setUseEdgeTransactions(true)
      ->setCommentActionLabel(pht('Change Blockers'))
      ->setCommentActionOrder(9000)
      ->setDescription(pht('Choose blockers.'))
      ->setTransactionType($blockers_type)
      ->setValue($blocks_phids);

    $blockers_field->setViewer($engine->getViewer());

    $edit_add = $blockers_field->getConduitEditType('blockers.add')
      ->setConduitDescription(pht('Add blockers.'));

    $edit_set = $blockers_field->getConduitEditType('blockers.set')
      ->setConduitDescription(
        pht('Set blockers, overwriting current value.'));

    $edit_rem = $blockers_field->getConduitEditType('blockers.remove')
      ->setConduitDescription(pht('Remove blockers.'));

    return array(
      $blockers_field
    );
  }

}
