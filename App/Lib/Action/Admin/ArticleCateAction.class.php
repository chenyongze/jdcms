<?php
class ArticleCateAction extends BaseAction{
	
	public function index(){
		$article_cate=M('ArticleCate');
		$data=$article_cate->order('ord desc,id desc')->where("is_del=0")->select();
		$this->assign('article_cate',$data);
		$this->display();	
	}
	
	//添加
	public function add(){
		$article_cate=M('ArticleCate');
		if ($_POST['submit']){
			
			$data=$article_cate->create();
			$row=$article_cate->add($data);
			if ($row){
				$this->success('添加成功！',U('ArticleCate/index'));
			}else {
				$this->error($article_cate->getError());
			}
			
		}else {
			
			$cates=$article_cate->order('ord desc,id desc')->where("is_del=0")->select();	
			$this->assign('cate_list',$cates);
			$this->display();
		}	
	}
	
	//修改
	public function edit(){
		
		$id=$_GET['id']?$_GET['id']:'';		
		$article_cate=M('ArticleCate');
		
		if ($_POST['submit']){
			$data=$article_cate->create();
			$save=$article_cate->save($data);
			$this->success('修改成功！',U('ArticleCate/index'));
			
		}else {
			if ($id==''){
				$this->error('请选择分类！');
			}
			$cate_info=$article_cate->where('id='.$id)->find();			
			$this->assign('cate_info',$cate_info);
			$this->display();	
		}		
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$article_cate = M('ArticleCate');
		$article_mod=M("Article");
		foreach ($del_id as $id){
			$article_cate->where('id='.$id." and is_del=0")->setField('is_del',1);
			$article_mod->where("cate_id=$id and is_del=0")->setField("is_del",1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$article_cate = M('ArticleCate');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ord'] = $ordid;
				$article_cate->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$article_cate = M('ArticleCate');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$article_cate->where($data)->save($set);
		$val=$article_cate->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	
	
	
	
	
	
	
	
	
	
	
}