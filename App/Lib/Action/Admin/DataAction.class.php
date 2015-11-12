<?php
class DataAction extends BaseAction{
	
	//显示数据库表
	public function index(){
		
		$rs = new Model();
		$list = $rs->query('SHOW TABLES FROM '.C('db_name'));
		$tablearr = array();
		foreach ($list as $key => $val) {
			if (strpos(current($val), C('db_prefix')) !== false){
				$tablearr[$key] = current($val);
			}
		}
		$this->assign('list_table',$tablearr);
		$this->display();
	}
	
	//备份操作
	public function back(){
		if(empty($_POST['id'])){
			$this->error('请选择需要备份的数据库表!');
		}	
		$filesize = intval($_POST['filesize']);
		if ($filesize<1) {
			$this->error('请为分卷大小设置一个整数值!');
		}
		$file = RUNTIME_PATH.'DataBackup/';
		$random = mt_rand(1000, 9999);
		$sql = ''; 
		$p = 1;
		foreach($_POST['id'] as $table){
			$rs = M(ucfirst(str_replace(C('db_prefix'),'',$table)));
			$array = $rs->select();
			$sql.= "TRUNCATE TABLE `$table`;\n";
			foreach($array as $value){
				$sql.= $this->get_sql($table, $value);
				if (strlen($sql) >= $filesize*1000) {
					$filename = $file.date('Ymd').'_'.$random.'_'.$p.'.sql';
					$dir = dirname($filename);
					if(!is_dir($dir)){
						mkdir($dir);
					}
					@file_put_contents($filename,$sql); //写入
					$p++;
					$sql='';
				}
			}
		}
		if(!empty($sql)){
			$filename = $file.date('Ymd').'_'.$random.'_'.$p.'.sql';
			$dir = dirname($filename);
			if(!is_dir($dir)){
				mkdir($dir);
			}
			@file_put_contents($filename,$sql); //写入
		}
		$this->success('数据库分卷备份已完成,共分成'.$p.'个sql文件存放!',U('Data/show'));
	}
	
	//生成SQL备份语句
	public function get_sql($table, $row){
		$sql = "INSERT INTO `$table` VALUES (";
		$values = array();
		foreach ($row as $value) {
		$values[] = "'" . mysql_real_escape_string($value) . "'";
		}
		$sql .= implode(', ', $values) . ");\n";
		return $sql;
	}
	
	//显示备份文件
	public function show(){
		$filepath = RUNTIME_PATH.'DataBackup/*.sql';
		$filearr = glob($filepath);
		if (!empty($filearr)) {
			foreach($filearr as $k=>$sqlfile){
				preg_match('/([0-9]{8}_[0-9a-z]{4}_)([0-9]+)\.sql/i',basename($sqlfile),$num);
				$backup[$k]['filename'] = basename($sqlfile);
				$backup[$k]['filesize'] = round(filesize($sqlfile)/(1024*1024), 2);
				$backup[$k]['maketime'] = date('Y-m-d H:i:s', filemtime($sqlfile));
				$backup[$k]['pre']    = $num[1];
				$backup[$k]['number'] = $num[2];
				$backup[$k]['path']   = RUNTIME_PATH.'DataBackup/';
			}
			$this->assign('list_backup',$backup);
			$this->display();
		}else{
			$this->error('没有检测到备份文件,请先备份或上传备份文件到'.RUNTIME_PATH.'DataBackup/',U('Data/index'));
		}
	}
	
	//导入备份文件
	public function backin(){
		$rs       = new Model();
		$pre      = $_GET['id'];
		$fileid   = $_GET['fileid'] ? intval($_GET['fileid']) : 1;
		$filename = $pre.$fileid.'.sql';
		$filepath = RUNTIME_PATH.'DataBackup/'.$filename;
		if(file_exists($filepath)){
			$sql = @file_get_contents($filepath);
			$sql = str_replace("\r\n", "\n", $sql);
			foreach(explode(";\n", trim($sql)) as $query) {
				$rs->query(trim($query));
			}
			$url = C('web_path')."index.php?a=backin&m=Data&g=Admin&id=$pre&fileid=";
			$this->success('第'.$fileid++.'个备份文件恢复成功,准备恢复下一个,请稍等!',$url.$fileid);
		}else{
			$this->success('数据库恢复成功!',U('Data/show'));
		}
	
	}
	
	//下载备份文件
	public function backdown(){
		$filepath = RUNTIME_PATH.'DataBackup/'.$_GET['id'];
		if (file_exists($filepath)) {
			$filename = $filename ? $filename : basename($filepath);
			$filetype = trim(substr(strrchr($filename, '.'), 1));
			$filesize = filesize($filepath);
			header('Cache-control: max-age=31536000');
			header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
			header('Content-Encoding: none');
			header('Content-Length: '.$filesize);
			header('Content-Disposition: attachment; filename='.$filename);
			header('Content-Type: '.$filetype);
			readfile($filepath);
			exit;
		}else{
			$this->error('没有找到备份文件!');
		}
	}
	
	//删除备份文件
	public function backdel(){
		$filename = trim($_GET['id']);
		@unlink(RUNTIME_PATH.'DataBackup/'.$filename);
		$this->success($filename.'已经删除!');
	}
	
	//批量删除备份文件
	public function del(){
		foreach($_POST['id'] as $value){
			@unlink(RUNTIME_PATH.'DataBackup/'.$value);
		}
		$this->success('批量删除备份文件成功！');
	}
	
}