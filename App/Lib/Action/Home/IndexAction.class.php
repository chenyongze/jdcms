<?php
//首页
class IndexAction extends BaseAction {
    public function index(){
		$this->assign("sty",array('index','style1'));//引入首页样式
		Load('extend');//中文字串截取扩展
		$items_tags_mod=M("Items_tags");//创建标签模型
		$items_mod=M("Items");
		$items_tags_item_mod=M("Items_tags_item");
		$items_cate_mod=M("Items_cate");//创建分类模型
//		$Model = new Model();
		//单品统计
		/*$itemCount = $items_mod->where("status=1 and is_del=0")->count('ID'); 
		$this->assign("itemCount",number_format($itemCount));*/

		$arr_style=array("left:0;top:0","left:101px;top:0","left:302px;top:0","left:403px;top:0","left:504px;top:0","left:605px;top:0","left:706px;top:0","left:807px;top:0","left:0;top:101px","left:504px;top:101px","left:605px;top:101px","left:303px;top:102px","left:707px;top:102px","left:0;top:202px","left:101px;top:202px","left:202px;top:202px","left:504px;top:202px","left:605px;top:202px");
		$arr_class=array("t1","t2","t1","t1","t1","t1","t1","t1","t1","t1","t1","t2","t2","t1","t1","t1","t1","t1");
        
		$userAll_arr=array();
		//获取首页列表
		if(is_array($this->p_cate_index_list)){
			$igci = 0;
			foreach ($this->p_cate_index_list as $kp => $vp) {
				//分类信息
				$index_group_cates[$kp]['name']=$vp['name'];
				$index_group_cates[$kp]['id']=$vp['id'];
				//首页显示的标签
				$tagsList = $items_tags_mod->field('id,name')->where("is_del=0 and is_index=1 and pid=".$vp["id"]."")->limit(18)->order('ord desc')->select();
				$index_group_cates[$kp]['s']=$tagsList;
				//首页标签下商品列表
				$newtagsList=array();
				$hasid_arr=array(-1);
				$ids=0;
				foreach($tagsList as $kyta=>$vlta){
					$hasid=implode(",",$hasid_arr);
					$tag_id=$vlta['id'];
					$tag_name=$vlta['name'];
					$join_it=" ".C("db_prefix")."items_tags_item as it on it.item_id=".C("db_prefix")."items.id";
					$join_tag=" ".C("db_prefix")."items_tags as t on it.tag_id=t.id";
					$whereIt=" ".C("db_prefix")."items.status=1 and ".C("db_prefix")."items.is_del=0 and t.id=$tag_id and ".C("db_prefix")."items.id not in ($hasid)";
					$tag_items=$items_mod->join($join_it)->join($join_tag)->group(" ".C("db_prefix")."items.id")->where($whereIt)->field(" ".C("db_prefix")."items.id,".C("db_prefix")."items.img,".C("db_prefix")."items.title,t.id as tid,t.name")->limit(9)->order(" ".C("db_prefix")."items.ord desc,".C("db_prefix")."items.add_time desc")->select();
					foreach($tag_items as $kyti=>$vlti){
						$hasid_arr[]=$vlti['id'];
					}
					if($tag_items){
						$newtagsList[$ids]['id']=$vlta['id'];
						$newtagsList[$ids]['name']=$vlta['name'];
						$newtagsList[$ids]['t_items']=$tag_items;
						$newtagsList[$ids]['style']=$arr_style[$ids];
						$newtagsList[$ids]['class']=$arr_class[$ids];
						$ids++;
					}
				}
				$index_group_cates[$kp]['s_items']=$newtagsList;

				//分享该分类的用户信息
				$joinu = " ".C("db_prefix")."user as u on ".C("db_prefix")."items.uid=u.id";
				$whereu = " ".C("db_prefix")."items.cid = ".$vp['id']." and ".C("db_prefix")."items.uid != 0 AND ".C("db_prefix")."items.is_del=0 AND ".C("db_prefix")."items.status=1 and u.is_del=0 and u.status=1";

				$userAll = $items_mod->join($joinu)->group("uid")->order("".C("db_prefix")."items.id desc")->where($whereu)->field("u.id as uid,u.name,u.img as uimg")->limit(10)->select();

				$index_group_cates[$kp]['user']=$userAll;

				
				//蘑菇街分类下二级标签和三级标签
				$scate_list=$items_cate_mod->where("pid=".$vp['id']." and is_del=0 and is_index=1")->order("ord desc")->limit(4)->field("id,name")->select();
				foreach($scate_list as $keys=>$vals){
					$scate_list[$keys]['key']=$keys;
					$sid=$vals['id'];
					if($keys==0){
						$class=array("l","","l","","","","l","","l","","");
					}elseif($keys==1){
						$class=array("l","","l","","","","l","l","","l","");
					}elseif($keys==2){
						$class=array("","l","","l","","l","l","","","l","l");
					}elseif($keys==3){
						$class=array("l","","l","","l","","","","l","l","");
					}
					$scate_tags_list=$items_tags_mod->where("sid=$sid and is_del=0")->order("ord desc")->field("id,name")->limit(11)->select();
					foreach($scate_tags_list as $key=>$val){
						$scate_tags_list[$key]['class']=$class[$key];
					}
					$scate_list[$keys]['scate_tags']=$scate_tags_list;
				}
				$index_group_cates[$kp]['scate']=$scate_list;
	        }

	        $this->assign("index_gropu_count",count($index_group_cates)-1); //首页显示的总列别数
			$this->assign("index_group_cates",$index_group_cates);			
		}
		//焦点图
		$joinu = " ".C("db_prefix")."user as u on ".C("db_prefix")."items.uid=u.id";
		$shareItem_where= " ".C("db_prefix")."items.uid != 0 AND ".C("db_prefix")."items.is_del=0 AND ".C("db_prefix")."items.status=1 and u.is_del=0 and u.status=1";
		$shareItem_field=" ".C("db_prefix")."items.id,".C("db_prefix")."items.title,".C("db_prefix")."items.img,".C("db_prefix")."items.remark1,u.id as uid,u.name,u.img as uimg";
		$shareItem=$items_mod->join($joinu)->where($shareItem_where)->order(" ".C("db_prefix")."items.is_focus desc,".C("db_prefix")."items.id desc")->field($shareItem_field)->limit(4)->select();
		$this->assign('focus', $shareItem[0]);
		$this->assign("index_share_item",$shareItem);


		//蘑菇街首页专辑列表
		$album_mod=M("Album");
		$album_items_mod=M("Album_items");
		$join_ac=" ".C("db_prefix")."album_cate as ac on ".C("db_prefix")."album.cid=ac.id";
		$join_ai=" ".C("db_prefix")."album_items as ai on ".C("db_prefix")."album.id=ai.pid";
		$join_items=" ".C("db_prefix")."items as i on ai.items_id=i.id";
		$join=array($join_ac,$join_ai,$join_items);
		$where="ac.is_del=0 and ".C("db_prefix")."album.status=1 and ".C("db_prefix")."album.is_del=0 and i.is_del=0 and i.status=1";
		$field=C("db_prefix")."album.id,".C("db_prefix")."album.title";
		$album_list=$album_mod->join($join)->group("id")->where($where)->order(" ".C("db_prefix")."album.add_time desc,i.add_time desc")->field($field)->limit(9)->select();
		foreach($album_list as $kyAl=>$vlAl){
			$album_items_cover=$album_items_mod->where("pid=".$vlAl['id']." and is_cover=1")->find();
			if($album_items_cover){
				$items_img=$items_mod->where("is_del=0 and status=1 and id=".$album_items_cover['items_id'])->getField("img");
			}else{
				$album_items=$album_items_mod->where("pid=".$vlAl['id'])->select();
				$ai_items_id=array();
				foreach($album_items as $kyAi=>$vlai){
					$ai_items_id[]=$vlai['items_id'];
				}
				if($ai_items_id){
					$ai_items_id_str=implode(",",$ai_items_id);
				}else{
					$ai_items_id_str=-1;
				}
				$items=$items_mod->where("id in ($ai_items_id_str) and is_del=0 and status=1")->order("add_time desc")->find();
				$items_img=$items['img'];
			}
			$album_list[$kyAl]['items_img']=$items_img;
		}
		$this->assign("album_list",$album_list);

		//店铺信息
		$shop_mod=M("Shop");
		$shop_list=$shop_mod->where("is_del=0")->order("ord desc")->field("id,name,img,url")->limit(30)->select();
		//dump($shop_list);
		$this->assign("shop_list",$shop_list);

		$this->assign('referer',$_SERVER['HTTP_REFERER']);

		//导航位置
		$this->assign("curpage","index");
		//文章列表
		$article=M("Article");
		$join_ac=" ".C("db_prefix")."article_cate as ac on ac.id=".C("db_prefix")."article.cate_id";
		$where="ac.is_del=0 and ac.status=1 and ".C("db_prefix")."article.status=1 and ".C("db_prefix")."article.is_del=0";
		$field=" ".C("db_prefix")."article.id,".C("db_prefix")."article.ord,".C("db_prefix")."article.url,".C("db_prefix")."article.add_time,".C("db_prefix")."article.title";
		$article_list=$article->join($join_ac)->where($where)->order(" ".C("db_prefix")."article.ord desc,".C("db_prefix")."article.id desc")->field($field)->limit(4)->select();
// 		dump($article_list);
		$this->assign("article_list",$article_list);
    	$this->display();
    }
    
    public function sitemap(){
    	$this->assign("sty",array('index','style1'));//引入首页样式
    	$items_cate = M('ItemsCate');
    	$items_tags = M('ItemsTags');
    	$cate_info=$items_cate->field('id')->where('pid=0 and is_del=0')->select();
    	foreach ($cate_info as $key=>$val){
			$cate_id[]=$val['id'];
    	}
    	$where['pid']=array('in',$cate_id);
    	$where['is_del']=0;
    	$cate_list=$items_cate->field('id,name')->where($where)->select();
    	foreach ($cate_list as $key=>$val){
    		$map['sid']=$val['id'];
    		$map['is_del']=0;
			$tags_list[$val['id']] = $items_tags->field('id,name')->where($map)->order('item_nums desc')->limit('10')->select();
    	}
		$this->assign('cate_list',$cate_list);
		$this->assign('tags_list',$tags_list);
    	$this->display();
    }
}