<?php
class ItemsCateAction extends BaseAction{

	//分类列表
	public function index(){
		$items_cate=M('ItemsCate');
		$cates=$items_cate->where('is_del = 0')->order('ord desc')->select();
		foreach ($cates as $val) {
			if ($val['pid']==0) {
				$cates_list['parent'][] = $val;
			}else {
				$cates_list['sub'][$val['pid']][]=$val;
			}
		}
		$this->assign('cates_list',$cates_list);
		$this->display();
	}
	
	//添加
	public function add(){
		$items_cate=M('ItemsCate');
		if ($_POST['submit']){
			$data=$items_cate->create();
	
			//保存上传图片
			if ($_FILES['img']['name'] != '') {
				mkdir('./Uploads/Cate/');
				$thumb=array('width'=>100,'height'=>1000);
				$upload_info = $this->upload('./Uploads/Cate/',$thumb);
				$data['img'] = '/Uploads/Cate/s_'. $upload_info['0']['savename'];
			}
				
			$items_cate->add($data);
			$this->success('添加成功',U('ItemsCate/index'));
		}else{
			$cates=$items_cate->where('is_del = 0')->select();
	
			foreach ($cates as $val) {
				if ($val['pid']==0) {
					$cates_list['parent'][] = $val;
				}else {
					$cates_list['sub'][$val['pid']][]=$val;
				}
			}
			$this->assign('cates_list',$cates_list);
			$this->display();
		}
	}
	
	//修改
	function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$items_cate=M('ItemsCate');
	
		if ($_POST['submit']){
			$data=$items_cate->create();

			//保存上传图片
			if ($_FILES['img']['name'] != '') {
				mkdir('./Uploads/Cate/');
				$thumb=array('width'=>100,'height'=>1000);
				$upload_info = $this->upload('./Uploads/Cate/',$thumb);
				$data['img'] = '/Uploads/Cate/s_'. $upload_info['0']['savename'];
			}
			
			if ($id){
				$items_cate->where('id='.$id)->save($data);
				$this->success('修改成功！',U('ItemsCate/index'));
			}else {
				$items_cate->add($data);
				$this->success('添加成功！',U('ItemsCate/index'));
			}
		}else {
			$cate_info=$items_cate->where('id='.$id)->find();
			$cate_p=$items_cate->where('id='.$cate_info['pid'])->find();
			$cate_info['pname']=$cate_p['name'];		
			$this->assign('cate',$cate_info);
			$this->display();	
		}
	}
	
	//删除
	public function delete(){
		
		$items_cate=M('ItemsCate');
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的分类！');
		}
		$del_id = $_POST['id'];
		
		$flag=true;
		foreach ($del_id as $id){
			$where['pid']=$id;
			$where['is_del']=0;
			$row=$items_cate->where($where)->find();
			if ($row){
				$flag=false;
				$error[] =$id;
			}else {
				$data['is_del'] = 1;
				$items_cate->where('id='.$id)->save($data);
			}
		}
			
		$str=implode(' ',$error);
		if ($flag){
			$this->success('删除成功！');
		}else {
			$this->error('ID为'.$str.'的分类下有其他分类，无法删除！');
		}
	}
	
	//排序
	public function order(){

		if ($_POST['order']){
			$items_cate = M('ItemsCate');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$items_cate->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$items_cate = M('ItemsCate');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$items_cate->where($data)->save($set);
		$val=$items_cate->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//对应原始分类
	public function compareCate(){
		$xml_array=simplexml_load_file('http://houtai.com/cateXML.php'); //把 xml 文档载入对象中。
		if($xml_array){
			echo "<select name='cate_select' size='6'>";			
			foreach($xml_array->channel->item as $tmp){
	      echo "<option value='".$tmp->cataid."'>".$tmp->title."</option>";
			}
			echo "</select>";
		}
	}
}
