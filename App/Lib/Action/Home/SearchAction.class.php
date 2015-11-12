<?php
class SearchAction extends BaseAction {
	public function index(){
		$this->assign("sty",array('index','style1'));//需要显示的样式
		$url_model=C("url_model");
		$keywords = isset($_REQUEST['keywords']) && trim($_REQUEST['keywords']) ? htmlspecialchars(trim($_REQUEST['keywords'])) :'';
		$sortby = isset($_REQUEST['sortby']) && trim($_REQUEST['sortby']) ? trim($_REQUEST['sortby']) : '';
		if(!$keywords){
			$this->assign("nokey","1");
			$this->display();
			exit();
		}
		if(!$sortby){
			$sql_order="ord desc";
			if($keywords!=""){
				$sql_where = "status='1' and is_del='0' and title LIKE '%$keywords%'";
			}else{
				$sql_where = "status='1' and is_del='0'";
			}
		}else{
			if($sortby=="likes"){
				$sql_order="ord desc,likes desc";
				if($keywords!=""){
					$sql_where = "status='1' and is_del='0' and title LIKE '%$keywords%'";
				}else{
					$sql_where = "status='1' and is_del='0'";
				}
			}elseif($sortby=="time"){
				$sql_order="ord desc,add_time desc";
				if($keywords!=""){
					$sql_where = "status='1' and is_del='0' and title LIKE '%$keywords%'";
				}else{
					$sql_where = "status='1' and is_del='0'";
				}
			}else{
				get_404();
			}
		}
		
		//推广
		if (C('spread_status')){
			$spread_content=$this->item_spread();
			preg_match('/<script(.*?)]/si',$spread_content,$matches);
			$spread_info=trim($matches[0],']');
			$spread_info=str_replace('广告品类',$keywords,$spread_info);
			$this->assign('spread_info',$spread_info);
		}
		
		$items_mod=M("Items");
		import("@.ORG.FallPage"); // 导入分页类
		$count=$items_mod->where($sql_where)->count();
		$Page= new Page($count,20); // 实例化分页类传入总记录数和每页显示的记录数
		$show =$Page->show(); // 分页显示输出
		$field = "id,cid,title,price,img,url,uid,sid,likes,comments,add_time";
		$items_list=$items_mod->where("$sql_where")->field($field)->order($sql_order)->limit(($Page->firstRow*5).','.$Page->listRows)->select();
	
		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		$this->assign('page',$show); // 赋值分页输出
		$this->assign("count",$count);
		$this->assign('sortby', $sortby);
		$this->assign("keywords",$keywords);
		
		//店铺信息
		$this->shop_list();

		$this->display();
	}
	
	public function album(){
		header("Content-Type:text/html; charset=UTF-8");
		$this->assign("sty",array('index','style1'));
		$keywords = isset($_REQUEST['keywords']) && trim($_REQUEST['keywords']) ? htmlspecialchars(trim($_REQUEST['keywords'])) :'';
		$this->assign("curpage","album");
		$album_mod=M("Album");
		$album_cate_mod=M("album_cate");
		
		$album_cate_list=$album_cate_mod->where("status=1 and is_del=0")->order("ord desc")->select();
		$join_ac=" ".C("db_prefix")."album_cate as ac on ".C("db_prefix")."album.cid=ac.id";
		$join_ai=" ".C("db_prefix")."album_items as ai on ".C("db_prefix")."album.id=ai.pid";
		$join_items=" ".C("db_prefix")."items as i on ai.items_id=i.id";
		$join=array($join_ac,$join_ai,$join_items);
		if($keywords!=""){
			$this->assign("album",'album');
			$where="ac.is_del=0 and ".C("db_prefix")."album.status=1 and ".C("db_prefix")."album.is_del=0 and i.is_del=0 and i.status=1 and ".C("db_prefix")."album.title like '%$keywords%'";
		}else{
			$where="ac.is_del=0 and ".C("db_prefix")."album.status=1 and ".C("db_prefix")."album.is_del=0 and i.is_del=0 and i.status=1";
		}
		$field=C("db_prefix")."album.id,".C("db_prefix")."album.title,".C("db_prefix")."album.info,".C("db_prefix")."album.add_time ";
		if(!$keywords){
			$this->assign("nokey","1");
			$this->display('Album:index');
			exit();
		}
		import("ORG.Util.Page");
		$count=$album_mod->distinct(true)->join($join)->where($where)->count("DISTINCT ".C("db_prefix")."album.id");
		$this->assign("count",$count);
		$Page= new Page($count,10);
		$show =$Page->show();
		$album_list=$album_mod->group("id")->join($join)->where($where)->field($field)->order(" ".C("db_prefix")."album.add_time desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->items_paiHang();
		$this->assign('keywords',$keywords);
		$this->assign('page',$show);
		$this->album_list($album_list);
		$this->assign("album_cate_list",$album_cate_list);
		$this->display('Album:index');
	}
}
