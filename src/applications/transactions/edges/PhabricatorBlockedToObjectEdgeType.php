<?php

final class PhabricatorBlockedToObjectEdgeType
  extends PhabricatorEdgeType {

  const EDGECONST = 99;

  public function getInverseEdgeConstant() {
    return PhabricatorBlockedToObjectEdgeType::EDGECONST;
  }

  public function shouldWriteInverseTransactions() {
    return true;
  }

}
