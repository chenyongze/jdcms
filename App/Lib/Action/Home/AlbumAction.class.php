<?php
class AlbumAction extends BaseAction{
	public function index(){
		$this->assign("sty",array('index','style1'));
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$this->assign("albumCid",$id);
		$this->assign("curpage","album");
		$album_mod=M("Album");
		$album_cate_mod=M("album_cate");
		$album_cate_list=$album_cate_mod->where("is_del=0")->select();

		$join_ac=" ".C("db_prefix")."album_cate as ac on ".C("db_prefix")."album.cid=ac.id";
		$join_ai=" ".C("db_prefix")."album_items as ai on ".C("db_prefix")."album.id=ai.pid";
		$join_items=" ".C("db_prefix")."items as i on ai.items_id=i.id";
		$join=array($join_ac,$join_ai,$join_items);
		$where="ac.is_del=0 and ".C("db_prefix")."album.status=1 and ".C("db_prefix")."album.is_del=0 and i.is_del=0 and i.status=1";
		$field=C("db_prefix")."album.id,".C("db_prefix")."album.title,".C("db_prefix")."album.info,".C("db_prefix")."album.add_time ";
		import("@.ORG.FallPage");
		$seo['title'] = "";
		if($id){
			$album_cate_info=$album_cate_mod->where("id=$id and is_del=0")->find();
			$seo['title']=!empty($album_cate_info['seo_title']) ? $album_cate_info['seo_title'] : $album_cate_info['title'];
			$seo['title'].=" - ".C("site_name");
			$seo['keys']=!empty($album_cate_info['seo_keys']) ? $album_cate_info['seo_keys'] : C("site_keyword");
			$seo['desc']=!empty($album_cate_info['seo_desc']) ? $album_cate_info['seo_desc'] : C("site_description");
			if(!$album_cate_info){
				get_404();
			}
			$count=$album_mod->distinct(true)->join($join)->where($where." and ".C("db_prefix")."album.cid=$id")->count("DISTINCT ".C("db_prefix")."album.id" );
		}else{
			$seo['title'].="专辑 - ".C("site_name");
			$seo['keys']=C("site_name")."专辑，".C("site_keyword");
			$seo['desc']=C("site_name")."专辑，".C("site_description");
			$count=$album_mod->distinct(true)->join($join)->where($where)->count("DISTINCT ".C("db_prefix")."album.id" );
		}
		$this->assign("count",$count);
		$Page= new Page($count,2);
		$show =$Page->show(); 
		if($id){
			$album_list=$album_mod->join($join)->group("id")->where($where." and ".C("db_prefix")."album.cid=$id")->field($field)->order(" ".C("db_prefix")."album.add_time desc")->limit(($Page->firstRow*5).','.($Page->listRows*5))->select();
		}else{
			$album_list=$album_mod->join($join)->group("id")->where($where)->field($field)->order(" ".C("db_prefix")."album.add_time desc")->limit(($Page->firstRow*5).','.($Page->listRows*5))->select();
		}
		$this->items_paiHang();
		$this->assign('page',$show);
		$this->album_list($album_list);
		$this->assign("album_cate_list",$album_cate_list);
		
		
		$this->assign("seo",$seo);
		$this->display();
	}
	public function addAlbum(){
		$this->assign("sty",array('index','usercenter'));
		$items_id=intval($_GET['id']);
		$items_mod=M("Items");
		$items_info=$items_mod->where("id=$items_id and is_del=0 and status=1")->find();
		if($items_info){
			$album_mod=M("Album");
			$uid=$_COOKIE['id'];
			if($uid){
				$album_where="ac.is_del=0 and ".C("db_prefix")."album.status=1 and ".C("db_prefix")."album.uid=$uid and ".C("db_prefix")."album.is_del=0";
				$join_ac=" ".C("db_prefix")."album_cate as ac on ".C("db_prefix")."album.cid=ac.id";
				$album_list=$album_mod->join($join_ac)->where($album_where)->field(" ".C("db_prefix")."album.id,".C("db_prefix")."album.title,".C("db_prefix")."album.add_time,".C("db_prefix")."album.info")->select();
				if($album_list){
					$this->assign("album_list",$album_list);
				}
				$this->assign("items_id",$items_id);
			}else{
				$url=get_url('login','','user');
				header('location:'.$url);
			}
		}else{
			get_404();
		}
		
		
		$this->display();
	}
	public function addAlbumBtn(){
		$items_mod=M("Items");
		$album_cate_mod=M("album_cate");
		$album_mod=M("Album");
		$album_items_mod=M("Album_items");
		$data["pid"]=$_POST["album_id"];
		$data['info']=$_POST["info"];
		$data['add_time']=time();
		$data['items_id']=$_POST["items_id"];
		if($_COOKIE['id']){
			if($data["pid"]==0){
				//判断分类中是否有‘默认’分类
				$album_cate_default=$album_cate_mod->where("title='默认' and is_del=0")->find();
				if($album_cate_default){
					$ac_id=$album_cate_default['id'];
				}else{
					$ac['add_time']=time();
					$ac['title']="默认";
					$ac_id=$album_cate_mod->add($ac);
				}
				$album['add_time']=time();
				$album['uid']=$_COOKIE['id'];
				$album['cid']=$ac_id;
				$album_list=$album_mod->where("title='默认专辑' and is_del=0 and uid=".$_COOKIE['id'])->select();
				
				if(!$album_list){
					$result=$album_mod->add($album);
					if($result){
						$data["pid"]=$result;
					}
				}else{
					echo "该商品已添加到该专辑";
					return false; 
				}
				
			}
			$items_id=$_POST["items_id"];
			$pid=$data["pid"];
			$haves=$items_mod->where("id=$items_id")->getField("haves");
			$album_items_info=$album_items_mod->where("items_id=$items_id and pid=$pid")->find();
			if($album_items_info){
				echo "该商品已添加到该专辑";
			}else{
				if($album_items_mod->add($data)){
					echo "添加成功";
				}
			}
		}
	}
}
?>