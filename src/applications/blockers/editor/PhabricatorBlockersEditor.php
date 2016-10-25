<?php

final class PhabricatorBlockersEditor extends PhabricatorEditor {

  private $object;
  private $explicitBlockerPHIDs = array();
  private $implicitBlockerPHIDs = array();
  private $unblockPHIDs       = array();

  public function setObject(PhabricatorBlockerableInterface $object) {
    $this->object = $object;
    return $this;
  }

  /**
   * Add explicit subscribers. These subscribers have explicitly subscribed
   * (or been subscribed) to the object, and will be added even if they
   * had previously unsubscribed.
   *
   * @param list<phid>  List of PHIDs to explicitly subscribe.
   * @return this
   */
  public function blockExplicit(array $phids) {
    $this->explicitBlockerPHIDs += array_fill_keys($phids, true);
    return $this;
  }


  /**
   * Add implicit subscribers. These subscribers have taken some action which
   * implicitly subscribes them (e.g., adding a comment) but it will be
   * suppressed if they've previously unsubscribed from the object.
   *
   * @param list<phid>  List of PHIDs to implicitly subscribe.
   * @return this
   */
  public function blockImplicit(array $phids) {
    $this->implicitBlockerPHIDs += array_fill_keys($phids, true);
    return $this;
  }


  /**
   * Unsubscribe PHIDs and mark them as unsubscribed, so implicit subscriptions
   * will not resubscribe them.
   *
   * @param list<phid>  List of PHIDs to unsubscribe.
   * @return this
   */
  public function unblock(array $phids) {
    $this->unblockPHIDs += array_fill_keys($phids, true);
    return $this;
  }


  public function save() {
    if (!$this->object) 
    {
      throw new PhutilInvalidStateException('setObject');
    }
    $actor = $this->requireActor();
    $src = $this->object->getPHID();

    
    if ($this->implicitBlockerPHIDs) 
    {
        $unBlock = PhabricatorEdgeQuery::loadDestinationPHIDs($src,PhabricatorObjectHasUnblockedEdgeType::EDGECONST);
        
        $unBlock = array_fill_keys($unBlock, true);
        
        $this->implicitBlockerPHIDs = array_diff_key($this->implicitBlockerPHIDs,$unblock);
    }

    $add = $this->implicitBlockerPHIDs + $this->explicitBlockerPHIDs;
    $del = $this->unblockPHIDs;

    // If a PHID is marked for both subscription and unsubscription, treat
    // unsubscription as the stronger action.
    $add = array_diff_key($add, $del);

    if ($add || $del) {
      $uBlockType = PhabricatorObjectHasUnblockedEdgeType::EDGECONST;
      $blockType = PhabricatorObjectHasBlockedEdgeType::EDGECONST;

      $editor = new PhabricatorEdgeEditor();

      foreach ($add as $phid => $ignored) {
        $editor->removeEdge($src, $uBlockType, $phid);
        $editor->addEdge($src, $blockType, $phid);
      }

      foreach ($del as $phid => $ignored) {
        $editor->removeEdge($src, $blockType, $phid);
        $editor->addEdge($src, $uBlockType, $phid);
      }

      $editor->save();
    }
  }

}
