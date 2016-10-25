<?php

final class PhabricatorUnblockedFromObjectEdgeType
  extends PhabricatorEdgeType {

  const EDGECONST = 100;

  public function getInverseEdgeConstant() {
    return PhabricatorUnblockedFromObjectEdgeType::EDGECONST;
  }

  public function shouldWriteInverseTransactions() {
    return true;
  }

}
