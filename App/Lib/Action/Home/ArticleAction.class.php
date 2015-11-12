<?php
class ArticleAction extends BaseAction{
	public function right() {
		Load('extend');//中文字串截取扩展
		$article=M("Article");
		$article_cate=M("Article_cate");
		
		//文章分类列表
		$article_cate_list=$article_cate->where("status=1 and is_del=0")->field("id,name,article_nums")->limit(20)->select();
		//推荐文章
		$join_ac=" ".C("db_prefix")."article_cate as ac on ac.id=".C("db_prefix")."article.cate_id";
		$where_at="ac.is_del=0 and ac.status=1 and ".C("db_prefix")."article.status=1 and ".C("db_prefix")."article.is_del=0";
		$field_at=" ".C("db_prefix")."article.id,".C("db_prefix")."article.url,".C("db_prefix")."article.add_time,".C("db_prefix")."article.title";
		$article_tuijian=$article->join($join_ac)->where($where_at)->limit(10)->order(" ".C("db_prefix")."article.is_best desc,".C("db_prefix")."article.ord desc,".C("db_prefix")."article.id desc")->field($field_at)->select();
		$this->assign("article_tuijian",$article_tuijian);
		$this->assign("article_cate_list",$article_cate_list);
	}
	public function index(){
		$this->assign("sty",array('index','style1','usercenter'));//引入需要的样式
		$this->right();
		$article_cate=M("Article_cate");
		$article=M("Article");
		$id=intval($_GET['id']);
		if($id){
			$cate_info=$article_cate->where("status=1 and is_del=0 and id=$id")->find();
			if(!$cate_info){
				get_404();
			}
			$seo['title']=!empty($cate_info['seo_title']) ? $cate_info['seo_title'] : $cate_info['name'];
			$seo['title'].="-".C("site_name");
			$seo['keys']=!empty($cate_info['seo_keys']) ? $cate_info['seo_keys'] : C("site_keyword");
			$seo['desc']=!empty($cate_info['seo_desc']) ? $cate_info['seo_desc'] : C("site_description");
			$this->assign("cate_info",$cate_info);
			$article_where="ac.is_del=0 and ac.status=1 and ".C("db_prefix")."article.status=1 and ".C("db_prefix")."article.is_del=0 and ".C("db_prefix")."article.cate_id=".$id;
		}else{
			$seo['title'].="咨询 - ".C("site_name");
			$seo['keys']=C("site_name")."咨询，".C("site_keyword");
			$seo['desc']=C("site_name")."咨询，".C("site_description");
			$article_where="ac.is_del=0 and ac.status=1 and ".C("db_prefix")."article.status=1 and ".C("db_prefix")."article.is_del=0";
		}
		$join_ac=" ".C("db_prefix")."article_cate as ac on ac.id=".C("db_prefix")."article.cate_id";
		$article_field=" ".C("db_prefix")."article.id,".C("db_prefix")."article.url,".C("db_prefix")."article.img,".C("db_prefix")."article.add_time,".C("db_prefix")."article.title,ac.name,ac.id as cid";
		import("@.ORG.FallPage");
		$count=$article->join($join_ac)->where($article_where)->count(); // 查询满足要求的总记录数
		$Page= new Page($count,4); // 实例化分页类传入总记录数和每页显示的记录数
		$show =$Page->show(); // 分页显示输出
		$this->assign('page',$show); // 赋值分页输出
		$article_list=$article->join($join_ac)->where($article_where)->order(" ".C("db_prefix")."article.ord desc,".C("db_prefix")."article.id desc")->field($article_field)->limit(($Page->firstRow*5).','.($Page->listRows*5))->select();
		
		$this->assign("article_list",$article_list);
		$this->assign("seo",$seo);
		$this->display();
	}
	public function detail(){
		$this->assign("sty",array('index','style1'));//引入需要的样式
		$this->right();
		$article=M("Article");
		$article_cate=M("Article_cate");
		$id=intval($_GET['id']);
		$join_ac=" ".C("db_prefix")."article_cate as ac on ac.id=".C("db_prefix")."article.cate_id";
		$where="ac.is_del=0 and ac.status=1 and ".C("db_prefix")."article.status=1 and ".C("db_prefix")."article.is_del=0 and ".C("db_prefix")."article.id=$id";
		$field=" ".C("db_prefix")."article.info,".C("db_prefix")."article.url,".C("db_prefix")."article.add_time,".C("db_prefix")."article.seo_title,".C("db_prefix")."article.seo_keys,".C("db_prefix")."article.seo_desc,".C("db_prefix")."article.title,ac.id as cid,ac.name";
		//文章详细信息及所属分类
		$article_info=$article->join($join_ac)->where($where)->field($field)->find();
		if(!$article_info){
			get_404();
		}
		
		$this->assign("article_info",$article_info);
		
		//文章标题拆分标签
		Vendor('pscws4.pscws4', '' ,'.class.php');
		$pscws = new PSCWS4();
		$pscws->set_dict('./Public/statics/js/scws/dict.utf8.xdb');
		$pscws->set_rule('./Public/statics/js/scws/rules.utf8.ini');
		$pscws->set_ignore(true);
		$pscws->send_text($article_info['title']);
		$words = $pscws->get_tops(10);
		$tags = array();
		foreach ($words as $val) {
			$tags[] = $val['word'];
		}
		$pscws->close();
		if(is_array($tags)){
			$likei = 1;
			foreach($tags as $valt){
				$tags_str.=$valt." ";
				if($likei<=3){ 
					$tags_like.=$valt["id"].",";
					$likei++;
				}
			}
		}else{
			$tags_str = '';
		}
		$desc=strip_tags($article_info['info']);
		$desc=mb_substr($desc, 0, 80, 'utf-8');

		$seo['title']=!empty($article_info['seo_title']) ? $article_info['seo_title'] : $article_info['title'];
		$seo['title'].="-".$article_info['name']."-".C("site_name");
		$seo['keys']=!empty($article_info['seo_keys']) ? $article_info['seo_keys'] : $tags_str ;
		$seo['desc']=!empty($article_info['seo_desc']) ? $article_info['seo_desc'] : $desc;
		$this->assign("seo",$seo);
		$this->display();
	}

}
?>