<?php
class ItemsTagsAction extends BaseAction{

	private $_table='ItemsTags';
	//标签列表
	public function index(){
		$items_tags=M($this->_table);
		$where .= 'is_del=0 ';
		if(isset($_GET['keyword'])){
			$keyword = $_GET['keyword'];
			$where.=" and name like '%$keyword%'";
		}
		if(isset($_GET['cate']) && $_GET['cate'] != 0){
			$cate = $_GET['cate'];
			$where.=" and pid = $cate";
		}
		
		import("ORG.Util.Page");
		$count=$items_tags->where($where)->count();
		$page=new Page($count,20);
		$show=$page->show();
		$tags=$items_tags->order('id desc')->where($where)->order('ord desc')->limit($page->firstRow.','.$page->listRows)->select();
		//分类
		$items_cate=M('ItemsCate');
		$cates=$items_cate->where('is_del=0')->select();
		$catesF=$items_cate->where('is_del=0 and pid=0')->select();

		foreach ($cates as $ki => $vi){
			$catesR[$vi['id']] = $vi;	
		}
		
		$this->assign('keyword',$keyword);
		$this->assign('cate',$cate);
		$this->assign('cates_list',$catesR);
		$this->assign('cates',$catesF);
		$this->assign('tags_list',$tags);
		$this->assign('page',$show);
		$this->display();
	}
	
	//添加
	public function add(){
		//分类
		$items_cate=M('ItemsCate');
		$cates=$items_cate->where('is_del=0')->select();
		foreach ($cates as $ki => $vi){
			if ($vi['pid']==0) {
				$cates_list['parent'][] = $vi;
			}else {
				$cates_list['sub'][$vi['pid']][]=$vi;
			}
		}
		$this->assign('cates_list',$cates_list);
		$this->display('edit');
	}
	
	//修改
	function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$tags_cate=M($this->_table);
		if ($_POST['submit']){
			$pid = $_POST['pid']?$_POST['pid']:'';
			$pidarr = explode(",", $pid);
			$data=$tags_cate->create();
			$data['pid'] = isset($pidarr[0])?$pidarr[0]:0;
			$data['sid'] = isset($pidarr[1])?$pidarr[1]:0;
			if($id){
				if($tags_cate->save($data)){
					$this->success('修改成功！',U('ItemsTags/index'));
				}
			}else{
				$where['name'] = $_REQUEST['name']?trim($_REQUEST['name']):'';
				$where['pid'] = $data['pid'];
				$tags_info=$tags_cate->field('id')->where($where)->limit(1)->find();
				if(!$tags_info['id']){
					$tags_cate->add($data);
					$this->success('添加成功！',U('ItemsTags/index'));
				}else{
					$this->success('已存在该标签！',U('ItemsTags/index'));
				}
			}
		}else {
			$where['id'] = $id;
			$where['is_del'] = 0;
			$tags_info=$tags_cate->where($where)->limit(1)->find();
			//分类
			$items_cate=M('ItemsCate');
			$cates=$items_cate->where('is_del=0')->select();
			foreach ($cates as $ki => $vi){
				if ($vi['pid']==0) {
					$cates_list['parent'][] = $vi;
				}else {
					$cates_list['sub'][$vi['pid']][]=$vi;
				}
			}
			$this->assign('tags',$tags_info);
			$this->assign('cates_list',$cates_list);
			$this->display();
		}
	}

	//删除
	public function delete(){
		$tags_mod=M('ItemsTags');
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的标签！');
		}
		$delarr = $_POST['id'];
		$del_id = implode (',',$delarr);
		if($del_id){
			$data['is_del'] = 1;
			if($tags_mod->where('id in ('.$del_id.')')->save($data)){
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
			$tags_mod = M('ItemsTags');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$tags_mod->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$items_tags = M($this->_table);
		$where['id']=$id;
		$data[$type]=array('exp',"($type+1)%2");
		$items_tags->where($where)->save($data);
		$val=$items_tags->field($type)->where($where)->find();
		$this->ajaxReturn($val[$type]);
	}
}
