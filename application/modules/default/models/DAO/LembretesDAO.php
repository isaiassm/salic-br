<?php
class LembretesDAO extends Zend_Db_Table
{

       	protected $_name    = 'sac.dbo.Projetos';

    public function buscar($sql)   
    {
       	//echo $sql . "<br>";
       	$db = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_DB :: FETCH_OBJ);
		$resultado = $db->fetchAll($sql);
		return $resultado;
	} 
	
    public function inserirLembrete($anoprojeto, $sequencial, $lembrete)   
    {
       	$db = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $objAcesso= new Acesso();
      	$sql = "INSERT INTO sac.dbo.Lembrete (Logon, AnoProjeto, Sequencial, DtLembrete, Lembrete)
				VALUES (75, '$anoprojeto', '$sequencial', {$objAcesso->getDate()}, '$lembrete')";
       	
		$resultado = $db->query($sql);
		return $resultado;
	} 
	
	public static function buscaLembrete($pronac)
	{
		$sql = "select 
   		Pr.AnoProjeto+Pr.Sequencial as nrpronac,
        lm.Lembrete as lembrete, lm.Contador,
		CONVERT(CHAR(10),lm.DtLembrete,103) as dtlembrete,
		Pr.IdPRONAC, 
		Pr.NomeProjeto 
from sac.dbo.Lembrete lm
inner join sac.dbo.Projetos Pr on Pr.AnoProjeto = lm.AnoProjeto and lm.Sequencial = Pr.Sequencial
 where Pr.IdPRONAC = " . $pronac . " ";
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_DB :: FETCH_OBJ);
		$resultado = $db->fetchAll($sql);
		return $resultado;
	}

	
	
	
	
	
	
	
		public static function pesquisaLembrete($pronac, $dtlembrete = null)
	{

	$sql = "select Pr.AnoProjeto+Pr.Sequencial as nrpronac, lm.Lembrete as lembrete, lm.Contador,
		CONVERT(CHAR(10),lm.DtLembrete,103) as dtlembrete,
		Pr.IdPRONAC, 
		Pr.NomeProjeto,
		Pr.AnoProjeto,
		Pr.Sequencial 
from sac.dbo.Projetos Pr 
INNER join sac.dbo.Lembrete lm on Pr.AnoProjeto = lm.AnoProjeto and lm.Sequencial = Pr.Sequencial
 where Pr.IdPRONAC = " . $pronac . " and CONVERT(CHAR(10),lm.DtLembrete,103) = '" . $dtlembrete . "'";	
		

		
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_DB :: FETCH_OBJ);
		$resultado = $db->fetchAll($sql);
		
		return $resultado;
	}

	public static function buscaProjeto($pronac, $data=null){

		$sql = "select AnoProjeto+Sequencial as nrpronac, 
				IdPRONAC,
				NomeProjeto,
				AnoProjeto,
				Sequencial 
				from     sac.dbo.Projetos 
 				where 	 IdPRONAC = ".$pronac;
	
			
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_DB :: FETCH_OBJ);
		$resultado = $db->fetchAll($sql);
		return $resultado;
	}

	
 	public function alterarlembrete($contador, $lembrete)
 	{

 			$sql2 = "update sac.dbo.Lembrete
				SET 
					Lembrete       		= '" . $lembrete . "' 
				where Contador = '" . $contador . "'";  
		//echo $sql;die();		
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_DB :: FETCH_OBJ);
		$resultado2 = $db->query($sql2);
 	}
 	
	
 	 	public function exluirlembrete($contador)
 	{

 	            $sql3 = "DELETE 
 	               				FROM sac.dbo.Lembrete 
 	            				WHERE Contador = '" . $contador . "'";                       
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->setFetchMode(Zend_DB :: FETCH_OBJ);
                $resultado3 = $db->fetchAll($sql3);
                
      
 		
 	}
 	
}	

// fecha class
				