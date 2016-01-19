<?php

class Application_Model_Propostas extends Application_Model_Clientes
{

	private $name = 'zf_propostas';
	private $name_valores = 'zf_propostas_valores';
	private $_cliente_table = 'zf_clientes';

	/**
	 * (non-PHPdoc)
	 * @see Application_Model_Clientes::insert()
	 */
	public function insert(array $data)
	{
		if (empty($this->selectByClientId($data['id']))) {
			
			$data_proposta = array();
			$data_proposta['id_cliente'] = $data['id'];
			$data_proposta['locked'] = $data['locked'];
			$data_proposta['locked_by'] = $data['locked_by'];
			$data_proposta['status'] = '2';
			$data_proposta['state'] = '1';			
			$data_proposta['created_user_id'] = $data['created_user_id'];
			
			unset($data['id']);			
			unset($data['locked']);
			unset($data['locked_by']);
			unset($data['status']);
			unset($data['state']);
			unset($data['created_user_id']);
			
			$data_proposta['dados_extras'] = json_encode($data);
			
			$result = $this->db->insert($this->name, $data_proposta);
			return $result;
		} else {
			return false;
		}
	}

	/**
	 *
	 * @return mixed
	 * @throws Zend_Exception
	 */
	public function selectAll($filter = 1)
	{
		$sql = "SELECT * FROM " . $this->name . " as p LEFT JOIN " . $this->_cliente_table . " as c ON p.id_cliente = c.id WHERE p.state = ? ORDER BY p.id DESC";
		try {
			$result = $this->db->fetchAll($sql, array(
					$filter
			), ZEND_DB::FETCH_OBJ);
			return $result;
		} catch (Zend_Exception $e) {
			throw new Zend_Exception($e->getMessage());
			return false;
		}
	}
	
	
	public function getPropostasAutorizadas(){
	    $sql = 'SELECT * FROM ' 
	           . $this->name .' AS pr LEFT JOIN ' 
               . $this->name_valores . ' AS pv ON pr.id_cliente = pv.id_cliente WHERE pr.autorizado = 1 ORDER BY pr.id DESC';
	    
        try {
            $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
            return $this->db->fetchAll($sql);
        }catch (Zend_Exception $e){
            throw new Zend_Exception($e->getMessage());
            return false;
        }
	}

	/**
	 *
	 * @param string $id
	 * @throws Zend_Exception
	 * @return mixed | boolean
	 */
	public function selectByUsersIds($ids, $filter = 1)
	{
		$sql = "SELECT * FROM " . $this->name . " as p LEFT JOIN " . $this->_cliente_table . " as c ON p.id_cliente = c.id WHERE c.created_user_id IN (" . $ids . ") AND p.state = ? ORDER BY p.id DESC";

		try {
			$result = $this->db->fetchAll($sql, array(
					$filter
			), ZEND_DB::FETCH_OBJ);
			return $result;
		} catch (Zend_Exception $e) {
			throw new Zend_Exception($e->getMessage());
			return false;
		}
	}
	
	/**
	 * Retorna um array
	 * @param string|int $clientId
	 * @param array $ids
	 * @throws Zend_Exception
	 * @return mixed|boolean
	 */
	public function selectByClientId($clientId, array $ids = null)
	{
		try {
			if ($ids) {
				$sql = 'SELECT * FROM ' . $this->name . ' as p LEFT JOIN ' . $this->_cliente_table . ' as c ON p.id_cliente = c.id WHERE c.id = ? AND c.created_user_id IN (' . $ids . ')';
			} else {
				$sql = 'SELECT * FROM ' . $this->name . ' as p LEFT JOIN ' . $this->_cliente_table . ' as c ON p.id_cliente = c.id WHERE c.id = ?';
			}

			$result = $this->db->fetchRow($sql, array(
					$clientId
			), Zend_Db::FETCH_ASSOC);
			return $result;
		} catch (Zend_Db_Exception $e) {
			throw new Zend_Exception($e->getMessage());
			return false;
		}
	}

	/**
	 *
	 * @param int $id
	 * @param array $data
	 * @throws Exception
	 */
	public function update($id, $data)
	{
		$data_proposta = array();

		if(isset($data['descricao'])){
			$data_proposta['descricao'] = $data['descricao'];
		}

		$data_proposta['locked'] = $data['locked'];
		$data_proposta['locked_by'] = $data['locked_by'];

		if(isset($data['status'])){
			$data_proposta['status'] = $data['status'];
		}
		if(isset($data['state'])){
			$data_proposta['state'] = $data['state'];
		}

		unset($data['descricao']);
		unset($data['locked']);
		unset($data['locked_by']);
		unset($data['status']);
		unset($data['state']);

		$data_proposta['dados_extras'] = json_encode($data);

		try {
			$where = $this->db->quoteInto('id_cliente = ?', $id);
			$result = $this->db->update($this->name, $data_proposta, $where);
			parent::update($id, $data);
			return $result;
		} catch (Zend_Db_Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	/**
	 * 
	 * @param string|number $id
	 * @param array $data
	 * @throws Exception
	 */
	public function updateSample($id,$data){
	    try {
	        $where = $this->db->quoteInto('id_cliente = ?', $id);
	        
	        if(isset($data['id'])){
	            unset($data['id']);
	        }
	        
	        $result = $this->db->update($this->name, $data, $where);	        
	        return $result;
	    } catch (Zend_Db_Exception $e) {
	        throw new Exception($e->getMessage());
	    }
	}

	public function lockRow($id, $current_user_id, $value)
	{
		try {
			$where = $this->db->quoteInto('id_cliente = ?', $id);
			$result = $this->db->update($this->name, array(
					'locked' => $value,
					'locked_by' => $current_user_id
			), $where);
			parent::lockRow($id, $current_user_id, $value);
			return $result;
		} catch (Zend_Db_Exception $e) {
			throw new Zend_Db_Exception($e->getMessage());
			return false;
		}
	}

	/**
	 * Method remove
	 *
	 * @param
	 *            int
	 * @return boolean
	 */
	public function delete($id)
	{
		try {
			$where = $this->db->quoteInto('id_cliente = ?', $id);
			$this->db->delete($this->name_valores, $where);
			$this->db->delete($this->name, $where);
			return true;
		} catch (Zend_Db_Adapter_Exception $e) {
			throw new Zend_Exception($e->getMessage());
		}
	}

	/**
	 * trash atualiza estado do item
	 *
	 * @param int $id
	 * @param int $state
	 * @throws Zend_Exception
	 */
	public function trash($id, $state = 0)
	{
		try {
			$where = $this->db->quoteInto('id_cliente = ?', $id);
			$bind = array(
					'state' => $state
			);
			$this->db->update($this->name, $bind, $where);
			return true;
		} catch (Zend_Db_Adapter_Exception $e) {
			throw new Zend_Exception($e->getMessage());
		}
	}

	public function lastInserId()
	{
		return $this->db->lastInsertId($this->name);
	}


	/**
	 * @param string $id
	 * @throws Zend_Db_Exception
	 * @return mixed|boolean
	 */
	public function selectCondicoesPagamento($id){
		try{
			if($id){
				$sql = 'SELECT * FROM '. $this->name_valores . ' WHERE id_cliente = ?';
				$result = $this->db->fetchRow($sql, array($id), Zend_Db::FETCH_ASSOC);
				return $result;	
			}else{
				return false;
			}
		}catch (Zend_Db_Exception $e){
			throw new Zend_Db_Exception($e->getMessage());
		}
	}

	/**
	 *
	 * @param array $data
	 * @throws Zend_Db_Exception
	 */
	public function insertCondicoesPagamento(array $data){
		try{
			$result = $this->db->insert($this->name_valores, $this->clearData($data, $this->name_valores));
			return $result;
			
		}catch (Zend_Db_Exception $e){
			throw new Zend_Db_Exception($e->getMessage());
		}
	}

	/**
	 *
	 * @param string $id
	 * @param array $data
	 * @throws Zend_Db_Exception
	 * @return number|boolean
	 */
	public function updateCondicoesPagamento($id, array $data){
		try{
			if($id){
				$where = $this->db->quoteInto('id_cliente = ?', $id);
				$result = $this->db->update($this->name_valores, $data, $where);
				return $result;
			}else{
				return false;
			}
		}catch (Zend_Db_Exception $e){
			throw new Zend_Db_Exception($e->getMessage());
		}
	}

}
