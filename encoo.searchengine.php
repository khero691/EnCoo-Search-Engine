<?

/**
* @author enCoo Developments © Vladyslav Halimskyi 2018
* @package enCoo/SearchEngine
*/

class EnCooSE {
	
	protected $request;

	protected $seObjects;

	protected $db;

	protected $cogs;

	public function __construct($cogs) {
		$this->seObjects = [];
		$this->db = new DB();
		$this->cogs = json_decode($cogs, true);

		return true;
	}

	public function search($content) {
		if(!$content) {
			exit('Enter you request please.');
		}

		$this->request = $content;

		$this->request = $this->clear($this->request);

		return $this->engine($this->request);
	}

	public function sortArray( $data, $ffield ) {
	    $ffield = (array) $ffield;
	    usort( $data, function($a, $b) use($ffield) {
	        $retval = 0;
	        foreach( $ffield as $fieldname ) {
	            if( $retval == 0 ) $retval = strnatcmp( $a[$fieldname], $b[$fieldname] );
	        }
	        return $retval;
	    } );
	    return $data;
	}

	protected function engine($key) {
		if(count($this->cogs) > 0) {
			$id = 0;
			if(isset($this->cogs['tables'])) {
				$int = 0;
				foreach ($this->cogs['tables'] as $table) {
					$table = trim($table);
					$reqult = $this->db->query("SELECT * FROM `$table` ORDER BY `id`");
					do {
						if(isset($row['id'])) {
							foreach ($this->cogs['fields'][$int] as $field) {
								$field = trim($field);
								if(preg_match("/".$key."/i", $row[$field])) {
									$this->seObjects[$id] = $row;
									$this->seObjects[$id]['table'] = $table;
									if(isset($this->seObjects[$id]['password']))
										$this->seObjects[$id]['password'] = '';
									$id++;
								}
							}
						}
					} while ($row = $reqult->fetch_assoc());
					$int++;
				}
				if($id==0) $this->seObjects['message'] = 'Nothing found.';
			} else return $this->errorRes('2');
		} else return $this->errorRes('1');

		return $this->seObjects;
	}

	protected function errorRes($code) {
		switch ($code) {
			case 1:
				return 'Error, no settings configured.';
				break;

			case 2:
				return 'This table is not found!';
				break;
			
			default:
				'Ok!';
				break;
		}
	}

	protected function clear($content) {
		$content = $this->db->escapeString($content);
		$content = strip_tags($content);
		$content = htmlspecialchars($content);
		$content = urldecode($content);
		$content = htmlspecialchars_decode($content);
		return $content;
	}
}

class DB extends DatabaseSettings {
	var $classQuery;
	var $mysqli;
	
	var $errno = '';
	var $error = '';
	
	function DB() {
		
		$settings = DatabaseSettings::getSettings();
		
		$host = $settings['dbhost'];
		$name = $settings['dbname'];
		$user = $settings['dbusername'];
		$pass = $settings['dbpassword'];
		
		$this->mysqli = new mysqli( $host , $user , $pass , $name );
		$this->mysqli->query("SET CHARACTER SET 'utf8'");
		$this->mysqli->query("set character_set_client='utf8'");
		$this->mysqli->query("set character_set_results='utf8'");
		$this->mysqli->query("set collation_connection='utf8_general_ci'");
		$this->mysqli->query("SET NAMES utf8");
	}
	
	function query( $query ) {
		$this->classQuery = $query;
		return $this->mysqli->query( $query );
	}
	
	function escapeString( $query ) {
		return $this->mysqli->escape_string( $query );
	}
	
	function numRows( $result ) {
		return $result->num_rows;
	}
	
	function lastInsertedID() {
		return $this->mysqli->insert_id;
	}
	
	function fetchAssoc( $result ) {
		return $result->fetch_assoc();
	}
	
	function fetchArray( $result , $resultType = MYSQLI_ASSOC ) {
		return $result->fetch_array( $resultType );
	}
	
	function fetchAll( $result , $resultType = MYSQLI_ASSOC ) {
		return $result->fetch_all( $resultType );
	}
	
	function fetchRow( $result ) {
		return $result->fetch_row();
	}
	
	function freeResult( $result ) {
		$this->mysqli->free_result($result);
	}
	
	function close() {
		$this->mysqli->close();
	}
	
	function sql_error() {
		if(empty($error)) {
			$errno = $this->mysqli->errno;
			$error = $this->mysqli->error;
		}
		return $errno . ' : ' . $error;
	}

}