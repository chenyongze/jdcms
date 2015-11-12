<?php
class AdAction extends BaseAction{
	
	//列表显示
	public function index(){
		$ad = M('Ad');
		$adboard = M('Adboard');
		
		import("ORG.Util.Page");
		$count = $ad->where('is_del=0')->count();
		$page = new Page($count,20);
		$ads = $ad->where('is_del=0')->limit($page->firstRow.','.$page->listRows)->order('ord desc')->select();
		
		$i=0;
		foreach ($ads as $val){
			$ad_list[$i]=$val;
			$adboard_name = $adboard->field('name')->where('id='.$val['board_id'])->find();
			$ad_list[$i]['adboard_name'] = $adboard_name['name'];
			$ad_list[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}

		$page = $page->show();
		$this->assign('page',$page);		
		$this->assign('ad_list',$ad_list);
		
		$ad_type_arr = array('code'=>'代码');
		$this->assign('ad_type_arr', $ad_type_arr);
		$this->display();
	}
	
	//添加
	public function add(){
		$ad_type_arr = array('code'=>'代码');
		$ad_info['start_time']=time();
		$ad_info['end_time']=strtotime("+1 year");
		$this->assign('ad_info',$ad_info);
		$this->assign('ad_type_arr', $ad_type_arr);
		$this->display('edit');
	}
	
	//修改
	public function edit(){
		$ad=M('Ad');
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		if ($_POST['submit']){
			$data=$ad->create();
			$data['start_time']=strtotime($_POST['start_time']);
			$data['end_time']=strtotime($_POST['end_time']);
			if ($id){
				$ad->where('id='.$id)->save($data);
				$this->success('修改成功',U('Ad/index'));
			}else {
				$data['add_time']=time();
				$ad->add($data);
				$this->success('添加成功',U('Ad/index'));
			}			
		}else {
			if ($id==''){
				$this->error('请选择广告！');
			}
			$ad_info=$ad->where('id='.$id)->find();
// 			$ad_type_arr = array('image'=>'图片','code'=>'代码','flash'=>'Flash','text'=>'文字');
			$ad_type_arr = array('code'=>'代码');
			$this->assign('ad_info',$ad_info);
			$this->assign('ad_type_arr', $ad_type_arr);
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$ad=M('Ad');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$ad->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$ad = M('Ad');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$ad->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//推广商品选项
	public function spread(){
	    if ($_POST['submit']){
			$push['spread_status']=intval($_POST['con']['spread_status']);
			$push['spread_position']=intval($_POST['con']['spread_position']);

			$file = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<spread>
<status></status>
<position></position>
</spread> 
XML;
			$xml = simplexml_load_string($file);	
			$xml->status=$push['spread_status'];
			$xml->position=$push['spread_position'];
			$xml->asXML('./Public/statics/spread.xml');

    		$config = $push;
    		$this->updateconfig($config);
    	}else {
    		$this->display();
    	}
	}
	
	//网站信息
	public function sinfo(){
		$admin_mod = M('Admin');
		$result = $admin_mod->field('user_name')->where('id = 1')->find();
		$data['name'] = $result['user_name'];
		$data['domain'] = 'http://'.$_SERVER['HTTP_HOST'].C('web_path');
		$data['versions'] = C('cms_versions');
		$this->ajaxReturn($data);
	}
	
    //修改union_id union_appkey
	public function cinfo(){
		$post_fields['union_id']=isset($_REQUEST['union_id'])?intval($_REQUEST['union_id']):'';
		$admin_mod = M('Admin');
		$result = $admin_mod->field('user_name')->where('id = 1')->find();
		$post_fields['user'] = $result['user_name'];
		$post_fields['site'] = 'http://'.$_SERVER['HTTP_HOST'].C('web_path');

		$url=C('official_website')."push/cinfo";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $post_fields ) );
		$result=curl_exec ( $ch );
		curl_close ( $ch );

		if ('success' == $result){
			$config['union_id']=intval($post_fields['union_id']);
			$this->updateconfig($config);
		}else {
			$this->error('配置信息更新失败');
		}
	}
	
	//cps报表
	public function cps(){

		$admin_mod = M('Admin');
		$result = $admin_mod->field('user_name')->where('id = 1')->find();
		$post_fields['user'] = $result['user_name'];
		$post_fields['site'] = 'http://'.$_SERVER['HTTP_HOST'].C('web_path');
	
		$url=C('official_website')."push/cps";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $post_fields ) );
		$result=curl_exec ( $ch );
		curl_close ( $ch );
		
		if ('no person' == $result){
			$this->error('您无权限查看，详情请到官方查询',U('Ad/info'));
		}elseif ('none' == $result) {
			$this->error('没有相关数据',U('Ad/info'));
		}elseif ('error' == $result) {
			$this->error('您无权限查看，详情请到官方查询',U('Ad/info'));
		}elseif ('no access' == $result) {
			$this->error('您无权限查看，详情请到官方查询',U('Ad/info'));
		}else {
			$cps_list=json_decode($result,true);
			$this->assign('cps_list',$cps_list);
		}
		$this->display();
	}

}