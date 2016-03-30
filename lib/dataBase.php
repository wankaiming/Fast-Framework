<?php
//数据库操作类
class dataBase{
    public $db;
	public $lastSql;
	public $queryResult;
	public $debug=DEBUG_SQL;

	//数据库连接
    public function __construct($host,$user,$pass,$db){
		$this->db = new mysqli($host,$user,$pass,$db) or $this->showError();
		$this->db->query('set names utf8');
    }
	
	//是否开启调试
	public function setDebug($flag=false){
		$this->debug=$flag;		
	}

	//执行硬编码sql
    public function execute($sql){
		$this->lastSql=$sql;
		if($this->debug==true){
			echo htmlspecialchars($sql).'<br/>';
		}
        return $this->queryResult = $this->db->query($sql) or $this->showError();
    }
	
	//获取一个字段的值
    public function getOne($sql=''){
		if($sql!=''){
			$this->execute($sql);
		}
        if ($row = $this->queryResult->fetch_array(MYSQLI_ASSOC))
			return (array_values($row)[0]);
        else
            return false;
    }
	
	//获取一行结果集
    public function getRow($sql=''){
		if($sql!=''){
			$this->execute($sql);
		}
		return $this->queryResult->fetch_array(MYSQLI_ASSOC);
    }
	
	//获取所有结果集
    public function getAll($sql=''){
		if($sql!=''){
			$this->execute($sql);
		}
		$dataArr = array();
		while ($row = $this->queryResult->fetch_array(MYSQLI_ASSOC)) {
			$dataArr[]= $row;
		}
		return $dataArr;
    }

	//获取查询的sql语句
	public function getLastSql(){
        return $this->lastSql;
    }

	//显示错误
	public function showError(){
		exit('Oh,the database operation error.<br/>'.($this->lastSql).'<br/>'.$this->db->error);		
	}
	
	//查询
	public function select($table,$fieldArr='*',$whereArr='',$start=0,$rows=10,$orderby='',$desc='DESC'){
		if($fieldArr!="*" && is_array($fieldArr) && !empty($fieldArr)){
			$fieldStr = ' '.implode(",",$fieldArr);
		}else{
			$fieldStr = ' '.$fieldArr;
		}
		if($whereArr!="" && is_array($whereArr) && !empty($whereArr)){
			foreach($whereArr as $key=>$val){
				$val = "'".$this->filter($val)."'";
				$whereStrArr[] = "$key=$val";
			}
			$whereStr = implode(" AND ",$whereStrArr);
		}else{
			$whereStr = '1';
		}
		if($orderby!=''){
			$orderbyStr = " ORDER BY $orderby $desc ";
		}else{
			$orderbyStr = '';
		}
		
		$start = trim($start);
		if(strtolower($start)=='all'){
			$limitStr ='';
		}else{
			$limitStr =" LIMIT $start,$rows ";
		}
		
		$querySql = "SELECT $fieldStr FROM $table WHERE $whereStr $orderbyStr $limitStr";
		$this->execute($querySql);
		
		if(stristr($fieldStr,' count')){
			//返回记录数
			$data = $this->getRow();
			$data = array_values($data);
		}else{
			//返回结果集
			$data = $this->getAll();
		}

		//如果只是获取一行直接返回一维数组
		if($rows==1 && !empty($data)){
			return $data[0];
		}else{
			return $data;
		}
	}
	
	//插入
	public function insert($table,$dataArr){
		if(!empty($dataArr) && is_array($dataArr)){
				//二维数组的情况
			if(!empty($dataArr[0]) && is_array($dataArr[0])){
				foreach($dataArr as $key=>$arr){
					if($key==0){
						$fieldStrArr = array_keys($arr);
						$fieldStr = implode(',',$fieldStrArr);
					}
					$fieldValArr = array_values($arr);
					foreach($fieldValArr as &$val){
						$val = $this->filter($val);
					}
					$fieldValStrArr[] = "(".implode(',',$fieldValArr).")";
				}
				$fieldValStr = implode(",",$fieldValStrArr);
				$querySql = "INSERT INTO $table ($fieldStr) VALUES $fieldValStr";
			}else{
				//一维数组的情况
				$fieldStrArr = array_keys($dataArr);
				$fieldStr = implode(',',$fieldStrArr);
				
				$fieldValArr = array_values($dataArr);
				foreach($fieldValArr as &$val){
					$val = $this->filter($val);
				}
				$fieldValStr = "'".implode("','",$fieldValArr)."'";
				$querySql = "INSERT INTO $table ($fieldStr) VALUES ($fieldValStr)";
			}
			
			$data = $this->execute($querySql);
			
			if($data){
				$data = $this->db->insert_id;
			}
			return $data;
		}
	}
	
	//修改
	public function update($table,$dataArr,$where){
		if(is_array($dataArr)){
			foreach($dataArr as $key=>$val){
				$val = "'".$this->filter($val)."'";
				$dataStrArr[] = "$key=$val";
			}
			$dataStr = implode(',',$dataStrArr); 
		}
		$querySql = "UPDATE $table SET $dataStr WHERE $where";
		return $this->execute($querySql);
	}
	
	//删除
	public function delete($table,$where){
		$querySql = "DELETE FROM $table WHERE $where";
		return $this->execute($querySql);
	}
	
	//过滤字符串 
	public function filter($str){
		return $this->db->real_escape_string($str);
	}
	
	public function __destruct() {
		if($this->queryResult){
			$this->queryResult->close();
		}
		if(is_resource($this->db)){
			$this->db->close();
		}
	}
}
?>