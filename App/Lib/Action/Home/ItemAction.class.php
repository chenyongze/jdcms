<?php
//首页
class ItemAction extends BaseAction {
    public function index(){
		$this->items_paiHang();
		//获取单个商品
		$id =intval($_GET['id']); //从列表页获取的id
		$this->assign("item_id",$id);
		if(!$id){
			get_404();
		}
		$items_mod= D('Items');		
		$field_items="id,title,cid,price,img,comments,url,likes,add_time,seo_title,seo_keys,seo_desc,status,info,uid";
		$item=$items_mod->where("id=$id and is_del='0' ")->field($field_items)->find();
		if(!$item){
			get_404();
		}
		//需要引入的样式
		$this->assign("sty",array('index','style1'));		
		if(!$item['status']){
			$this->assign('status',1);
			$this->display();
			exit;
		}
		
		$this->assign('item',$item);
		$items_tags_mod=M("Items_tags"); 
		//喜欢该单品
		$uid=$_COOKIE['id'];
		if($uid){
			$items_likes_mod=M("Items_likes");
			$likesarr = $items_likes_mod->where("items_id in (".$id.") and uid = ".$uid)->select();
			if(($likesarr)){
				$like = 1;
			}else {
				$like = 0;
			}
			$this->assign('like',$like);
		}
		//获取该商品对应的所有标签数据表
		$items_tags_item_mod=M('Items_tags_item');
		$items_tags_item=$items_tags_item_mod->where("item_id=$id")->select();//根据该商品id获取商品与标签的对应关系信息表数据
		$tag_id_arr=Array();
		foreach($items_tags_item as $valD){
			$tag_id_arr[]=$valD['tag_id'];
		}
		$sql_tid=implode(",",$tag_id_arr);
		$tags=$items_tags_mod->field('id,name,pid,sid,item_nums')->where("id IN ($sql_tid) and is_del=0")->order('ord desc')->select();
		$this->assign("tags",$tags);
		if(is_array($tags)){
			$likei = 1;
			foreach($tags as $valt){
				$tags_str.=$valt["name"]." ";
				if($likei<=3){ //猜你喜欢使用
					$tags_like.=$valt["id"].",";
					$likei++;
				}
			}
		}else{
			$tags_str = '';
		}

		//替换模板title的值
		$seo['title']=!empty($item['seo_title']) ? trim($item['seo_title']) : $item['title'] . "-" . C("site_name");
		$seo['keys']=!empty($item['seo_keys']) ? trim($item['seo_keys']) : $tags_str;
		
		if(!empty($item['seo_desc'])){
			$iteminfo = trim($item['seo_desc']);
		}else if($item['info']){
			Load('extend');//中文字串截取扩展
			$iteminfo = strip_tags($item['info']);
			$iteminfo = msubstr($iteminfo, 0, 100 ,"utf-8", false);
		}else{
			$iteminfo = $item['title'];
		}
		$seo['desc']=$iteminfo;
		$this->assign("seo",$seo);

		//获取与该商品相同类别的其他商品
		$cid=$item['cid'];
		$daytime=time()-86400*7;
		$items_cate_item=$items_mod->where("id!=$id and cid=$cid and is_del=0 and status=1 and add_time>$daytime")->field($field_items)->limit(10)->select();
		$this->items_list($items_cate_item);
		
		//获取商品对应的类别名称
		$items_cate_mod=M("Items_cate");
		$item_cate_name=$items_cate_mod->where("id=$cid")->getField("name");
		$this->assign("item_cate_name",$item_cate_name);
		$this->assign("cid",$cid);

		//猜你喜欢
		$hasid_arr=array($id);
		foreach($tags as $kyta=>$vlta){
			$hasid=implode(",",$hasid_arr);
			$tag_id=$vlta['id'];
			$join_it=" ".C("db_prefix")."items_tags_item as it on ".C("db_prefix")."items_tags.id=it.tag_id";//关联items_tags表
			$join_i= " ".C("db_prefix")."items as i on it.item_id=i.id";//关联items表
			$join=array($join_it,$join_i);
			$where="it.tag_id=$tag_id and it.item_id=i.id and i.status=1 and i.is_del=0 and i.id not in($hasid)";
			$field=" ".C("db_prefix")."items_tags.id,".C("db_prefix")."items_tags.name,i.id as item_id,i.img,i.title";
			$tags_items_list=$items_tags_mod->join($join)->where($where)->field($field)->limit(9)->select();
			foreach($tags_items_list as $kyti=>$vlti){
				$hasid_arr[]=$vlti['item_id'];
			}
			if($tags_items_list){
				$likes_list[$kyta]['item_nums']=$vlta['item_nums'];
				$likes_list[$kyta]['tagname']=$vlta['name'];
				$likes_list[$kyta]['tagid']=$vlta['id'];
				$likes_list[$kyta]['tagitem']=$tags_items_list;
			}
			if(count($likes_list)>2){
				break;
			}
		}
		$this->assign("likes_list",$likes_list);
		
		//分页显示评论
		import("ORG.Util.Page");
		$items_comments_mod=M('Items_comments');//创建用户评论模型
		$count=$items_comments_mod->where("items_id=$id and status=1 and is_del=0")->count();
		$p = new Page($count,8);
		$items_comments_list=$items_comments_mod->where("items_id=$id and status=1 and is_del=0")->order("add_time desc")->limit($p->firstRow.','.$p->listRows)->select();//查找所有评论
		$user_mod=M("User");
		foreach($items_comments_list as $kyC=>$vlC){
			$uid=$vlC['uid'];
			$user_info=$user_mod->where("id=$uid")->find();
			$items_comments_list[$kyC]['uimg']=$user_info['img'];
		}
		$res=array();
		$res['list']=$items_comments_list;
		$res["page"]=$p->show_1();
		$res['count']=$count;
		$this->assign('comments',$res);
		if($this->isAjax()){
			$this->ajaxReturn(array('list'=>$this->fetch('comments'),'count'=>$res['count']));
		}

		//商品的点击量
		$hitCookie_var="hits_".$id;
		$old_hits=$items_mod->where("id=$id")->getField("hits");
		$hitsCookie_val=$_COOKIE[$hitCookie_var];
		if(!isset($hitsCookie_val)){
			if($items_mod->where("id=$id")->setField('hits',$old_hits+1)){
				setCookie($hitCookie_var,'true',time()+3600*10);
			}
		}
		
		//分享该商品的用户
		$user_share_id=$items_mod->where("status=1 and is_del=0 and id=$id")->getField("uid");
		$user_share_info=$user_mod->where("id=$user_share_id and is_del=0")->find();
		$this->assign("user_share_info",$user_share_info);

		//文章列表
		$article=M("Article");
		$join_ac=" ".C("db_prefix")."article_cate as ac on ac.id=".C("db_prefix")."article.cate_id";
		$where="ac.is_del=0 and ac.status=1 and ".C("db_prefix")."article.status=1 and ".C("db_prefix")."article.is_del=0";
		$field=" ".C("db_prefix")."article.id,".C("db_prefix")."article.ord,".C("db_prefix")."article.url,".C("db_prefix")."article.add_time,".C("db_prefix")."article.title";
		$article_list=$article->join($join_ac)->where($where)->order(" ".C("db_prefix")."article.ord desc,".C("db_prefix")."article.id desc")->field($field)->order('add_time desc')->limit(4)->select();
		$this->assign("article_list",$article_list);

		$this->display();
    }
	public function buy(){
		$id=isset($_GET['id'])?intval($_GET['id']):'';
		if(!$id){
			header("Location: ".C('site_domain')."");
		}
	    $items_mod= D('Items');	
    	$res=$items_mod->where("id=$id and is_del=0 and status=1")->getfield('url');
    	if($res){
        	header("Location: $res");
    	}else{
    		header("Location: ".C('site_domain')."");
    	}
    	exit;
	}
	public function add_comments(){
		$uid=$_COOKIE['id'];
		$user_mod=M("User");
		$items_comments_mod=M('Items_comments');
		//评论审核状态
		$comments_status=C("comments_status");
		if($uid){
			$user_info=$user_mod->where("id=$uid")->find();
			$data['items_id']=$_POST['items_id'];
			$data['uname']=$user_info['name'];
			$data['uid']=$_COOKIE['id'];
			$data['info']=$this->filter($_POST['info']);
			if($comments_status==0){
				$data['status']=1;
			}else{
				$data['status']=0;
			}
			$data['add_time']=time();
			$time=date("Y-m-d H:i:s",$data['add_time']);
			$items_id=$_POST['items_id'];
			$cookie_var="comments_".$items_id;
			$cookie_value=$_COOKIE[$cookie_var];
			if(isset($cookie_value)){
				echo "check_reComment";
			}else{
				if($items_comments_mod->add($data)){
					setCookie($cookie_var,"true",time()+60);
					$items_mod=M("Items");
					$items_mod->where("id=$items_id")->setInc('comments');
					if($comments_status==0){
						echo "ok";
					}else{
						echo "comm_not_show";
					}
				}	
			}
		}else{
			echo "notLogin";
		}
	}

	public function commDel(){
		$id=$_POST['comm_id'];//获取评论id
		$items_comments_mod=M('Items_comments');
		$items_mod=M("Items");
		$items_id=$items_comments_mod->where("id=$id")->getField("items_id");
		$item_comments=$items_mod->where("id=$items_id")->getField('comments');
		$commDel=$items_comments_mod->where("id=$id and uid=".$_COOKIE['id'])->setField('is_del',1);
		if($commDel){
			$items_mod->where("id=$items_id")->setField('comments',$item_comments-1);
			echo "delOK";
		}
	}
}