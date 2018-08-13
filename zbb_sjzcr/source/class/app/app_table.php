<?php
/**
 *    秘密信息
 *    
 *    表操作实体对象基类
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}


class app_table extends app_base
{

	protected $_table;
	protected $_pk;
	protected $_pre_cache_key;

	public function __construct($para = array()) {
		if(!empty($para)) {
			$this->_table = $para['table'];
			$this->_pk = $para['pk'];
		}
		
		parent::__construct();
	}

	public function getTable() {
		return $this->_table;
	}

	public function setTable($name) {
		return $this->_table = $name;
	}

	public function count() {
		$count = (int) DB::result_first("SELECT count(*) FROM ".DB::table($this->_table));
		return $count;
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false) {
		if(isset($val) && !empty($data) && is_array($data)) {
			$this->checkpk();
			$ret = DB::update($this->_table, $data, DB::field($this->_pk, $val), $unbuffered, $low_priority);
			foreach((array)$val as $id) {
				$this->update_cache($id, $data);
			}
			return $ret;
		}
		return !$unbuffered ? 0 : false;
	}

	public function delete($val, $unbuffered = false) {
		$ret = false;
		if(isset($val)) {
			$this->checkpk();
			$ret = DB::delete($this->_table, DB::field($this->_pk, $val), null, $unbuffered);
			$this->clear_cache($val);
		}
		return $ret;
	}

	public function truncate() {
		DB::query("TRUNCATE ".DB::table($this->_table));
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		return DB::insert($this->_table, $data, $return_insert_id, $replace, $silent);
	}

	public function checkpk() {
		if(!$this->_pk) {
			throw new DbException('Table '.$this->_table.' has not PRIMARY KEY defined');
		}
	}

	public function fetch($id, $force_from_db = false){
		$data = array();
		if(!empty($id)) {
			if($force_from_db || ($data = $this->fetch_cache($id)) === false) {
				$data = DB::fetch_first('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field($this->_pk, $id));
				if(!empty($data)) $this->store_cache($id, $data);
			}
		}
		return $data;
	}

	public function fetch_all($ids, $force_from_db = false) {
		$data = array();
		if(!empty($ids)) {
			if($force_from_db || ($data = $this->fetch_cache($ids)) === false || count($ids) != count($data)) {
				if(is_array($data) && !empty($data)) {
					$ids = array_diff($ids, array_keys($data));
				}
				if($data === false) $data =array();
				if(!empty($ids)) {
					$query = DB::query('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field($this->_pk, $ids));
					while($value = DB::fetch($query)) {
						$data[$value[$this->_pk]] = $value;
						$this->store_cache($value[$this->_pk], $value);
					}
				}
			}
		}
		return $data;
	}

	public function fetch_all_field(){
		$data = false;
		$query = DB::query('SHOW FIELDS FROM '.DB::table($this->_table), '', 'SILENT');
		if($query) {
			$data = array();
			while($value = DB::fetch($query)) {
				$data[$value['Field']] = $value;
			}
		}
		return $data;
	}

	public function range($start = 0, $limit = 0, $sort = '') {
		if($sort) {
			$this->checkpk();
		}
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}

	public function optimize() {
		DB::query('OPTIMIZE TABLE '.DB::table($this->_table), 'SILENT');
	}

	public function fetch_cache($ids, $pre_cache_key = null) {
		$data = false;
		return $data;
	}

	public function store_cache($id, $data, $cache_ttl = null, $pre_cache_key = null) {
		$ret = false;
		return $ret;
	}

	public function clear_cache($ids, $pre_cache_key = null) {
		$ret = false;
		return $ret;
	}

	public function update_cache($id, $data, $cache_ttl = null, $pre_cache_key = null) {
		$ret = false;
		return $ret;
	}

	public function update_batch_cache($ids, $data, $cache_ttl = null, $pre_cache_key = null) {
		$ret = false;
		return $ret;
	}

	public function reset_cache($ids, $pre_cache_key = null) {
		$ret = false;
		return $ret;
	}

	public function increase_cache($ids, $data, $cache_ttl = null, $pre_cache_key = null) {
		
	}

	public function __toString() {
		return $this->_table;
	}

}

?>