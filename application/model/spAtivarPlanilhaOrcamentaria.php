<?php

/**
 * Description of spAtivarPlanilhaOrcamentaria
 * Criado em 18/01/2016 - Fernão Lara
 */
class spAtivarPlanilhaOrcamentaria extends GenericModel {
        
    protected $_banco = 'SAC';
    protected $_name  = 'spAtivarPlanilhaOrcamentaria';

    public function exec($idPronac){
      $sql = "exec ".$this->_banco.".dbo.".$this->_name." $idPronac";
      return $this->getAdapter()->query($sql);
    }
}
?>