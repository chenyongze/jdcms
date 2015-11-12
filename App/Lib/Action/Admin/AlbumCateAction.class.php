<?php
class AlbumCateAction extends BaseAction{

	//标签列表
	public function index(){
		$albumcate=M('AlbumCate');
		$where .= 'is_del=0 ';
		
		import("ORG.Util.Page");
		$count=$albumcate->where($where)->count();
		$page=new Page($count,20);
		$show=$page->show();
		$list=$albumcate->where($where)->limit($page->firstRow.','.$page->listRows)->order('ord desc')->select();
		foreach ($list as $key=>$val){
			$list[$key]['key']= ++$page->firstRow;
		}
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	
	//添加
	public function add(){
		//分类
		$this->display('edit');
	}

	//修改
	function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$albumcate=M('AlbumCate');
		if ($_POST['submit']){
			$id = $_POST['id']?$_POST['id']:'';
			$data=$albumcate->create();
	
			if($id){
				if($albumcate->save($data)){
					$this->success('修改成功',U('AlbumCate/index'));
				}else{
					$this->error('修改有误',U('AlbumCate/index'));
				}
			}else{
				$where['name'] = $_REQUEST['name']?trim($_REQUEST['name']):'';
				$albumcate_info=$albumcate->field('id')->where($where)->limit(1)->find();
				if(!$albumcate_info['id']){
					$data['add_time']=time();
					$albumcate->add($data);
					$this->success('添加成功',U('AlbumCate/index'));
				}else{
					$this->success('已存在该专辑分类',U('AlbumCate/index'));
				}
			}
		}else {
			$where['id'] = $id;
			$where['is_del'] = 0;
			$albumcate_info=$albumcate->where($where)->limit(1)->find();
	
			$this->assign('albumcate',$albumcate_info);
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		$albumcate=M('AlbumCate');

		if (!isset($_POST['id'])){
			$this->error('请选择要删除的专辑分类！');
		}
		$delarr = $_POST['id'];
		$del_id = implode (',',$delarr);
		if($del_id){
			$data['is_del'] = 1;
			if($albumcate->where('id in ('.$del_id.')')->data($data)->save()){
				$this->success('删除成功！');
			}else{
				$this->error('删除有误请重试！');
			}
		}else{
			$this->error('删除有误请重试！');
		}
	}
	
	//排序
	public function order(){
	
		if ($_POST['order']){
			$items_cate = M('AlbumCate');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$items_cate->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}
}
