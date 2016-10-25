<?php

final class PhabricatorBlockersQuery extends PhabricatorQuery {

  private $objectPHIDs;
  private $blockerPHIDs;

  public static function loadBlockersForPHID($phid) {
    if (!$phid) {
      return array();
    }

    $blockers = id(new PhabricatorBlockersQuery())
      ->withObjectPHIDs(array($phid))
      ->execute();
    return $blockers[$phid];
  }

  public function withObjectPHIDs(array $object_phids) {
    $this->objectPHIDs = $object_phids;
    return $this;
  }

  public function withBlockerPHIDs(array $blocker_phids) {
    $this->blockerPHIDs = $blocker_phids;
    return $this;
  }

  public function execute() {
    $query = new PhabricatorEdgeQuery();
    $edge_type = PhabricatorObjectHasBlockerEdgeType::EDGECONST;

    $query->withSourcePHIDs($this->objectPHIDs);
    $query->withEdgeTypes(array($edge_type));

    if ($this->blockerPHIDs) {
      $query->withDestinationPHIDs($this->blockerPHIDs);
    }

    $edges = $query->execute();

    $results = array_fill_keys($this->objectPHIDs, array());
    foreach ($edges as $src => $edge_types) {
      foreach ($edge_types[$edge_type] as $dst => $data) {
        $results[$src][] = $dst;
      }
    }

    return $results;
  }
}
