<?php
class ArticleAction extends BaseAction{

	public function index(){
		$article=M('Article');
		$article_cate=M('ArticleCate');
		//搜索
		$where = 'is_del=0';
		if (isset($_POST['keyword']) && trim($_POST['keyword'])) {
			$where .= " AND title LIKE '%".$_POST['keyword']."%'";
			$this->assign('keyword', $_POST['keyword']);
		}
		if (isset($_POST['time_start']) && trim($_POST['time_start'])) {
			$time_start = strtotime($_POST['time_start']);
			$where .= " AND add_time>='".$time_start."'";
			$this->assign('time_start', $_POST['time_start']);
		}
		if (isset($_POST['time_end']) && trim($_POST['time_end'])) {
			$time_end =strtotime($_POST['time_end']) ;
			$where .= " AND add_time<='".$time_end."'";
			$this->assign('time_end', $_POST['time_end']);
		}
		if (isset($_POST['cate_id']) && intval($_POST['cate_id'])) {
			$where .= " AND cate_id=".$_POST['cate_id'];
			$this->assign('cate_id', $_GET['cate_id']);
		}
	
		//分页
		import("ORG.Util.Page");
		$count=$article->where($where)->count();
		$page=new Page($count,10);
		$show=$page->show();
		$data=$article->where($where)->order('ord desc,id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$map['id']=$val['cate_id'];
			$map['is_del']=0;
			$articles[$i]=$val;
			$cate=$article_cate->field('name')->where($map)->find();
			$articles[$i]['cate_name']=$cate['name'];
			$articles[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		
		//分类
		$result=$article_cate->order('ord desc,id desc')->where("is_del=0")->select();
		$this->assign('articles',$articles);
		$this->assign('cate_list',$result);
		$this->assign('page',$show);
		$this->display();
	}


	//添加
	public function add(){
		$article=M('Article');
		$article_cate=M('ArticleCate');
	
		$result = $article_cate->order('ord desc,id desc')->where("is_del=0")->select();
		$this->assign('cate_list',$result);
		$this->display('edit');
	}
	
	//修改
	public function edit(){
		$article=M('Article');
		$article_cate=M('ArticleCate');	
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		
		if($_POST['submit']){
			if ($_POST['title']==''){
				$this->error('标题不能为空！');
			}
			$data=$article->create();
			
			if ($id){
				$article->save($data);
				$this->success('修改成功',U('Article/index'));
			}else {
				set_time_limit(0);
				$data['add_time']=time();
				
				$totle=C('article_tags_totle');
				$limit=C('article_tags_limit');
				$_tag=M('ItemsTags');
				$tag_list=$_tag->field('id,name')->where('is_del=0')->select();
				$nums=0;
				$tag_id_arr=array();
				foreach ($tag_list as $tag){
					if ($nums<$totle){
						if (false !== strpos($data['info'], $tag['name'])){
							$data['info']=str_replace('&lt;','<',$data['info']);
							$data['info']=str_replace('&gt;','>',$data['info']);
							preg_match_all('/<a([^>]*)>(.*)<\/a>/Usi',$data['info'],$result1);
							preg_match_all('/<(?!a|\/a\b)([^>]*)>/Usi',$data['info'],$result2);					
							$matchs=array_merge(array_unique($result1[0]),array_unique($result2[0]));
							foreach ($matchs as $k=>$v){
								$match=str_replace('/', '\\/', $v);
								$pattern='/'.$match.'/';
								$replace='#'.$k.'#';
								$data['info']=preg_replace($pattern,$replace,$data['info']);
							}
							$data['info']=preg_replace('/(?!#)'.$tag['name'].'(?!#)/si', '<a style="color:#5FAA92;" href="'.get_url('tag',$tag['id'],'cate').'" target="blank">'.$tag['name'].'</a>', $data['info'], $limit, $count);
							foreach ($matchs as $k=>$v){
								$pattern='/#'.$k.'#/';
								$replace=$v;
								$data['info']=preg_replace($pattern,$replace,$data['info']);
							}
							unset($result1);
							unset($result2);
							unset($matchs);
							if ($count){
								$tag_id_arr[]=$tag['id'];
								$nums++;
							}
						}
					}else {
						break;
					}
				}

				//下载本地图片
				$images=$this->_stripimages($data['info']);
				foreach ($images as $img){
					$image=$this->down_article($img, md5($img));
					$data['info']=str_replace($img, $image, $data['info']);
				}
				$article_id=$article->add($data);
				
				if($tag_id_arr){
					$_article_tags=M('ArticleTags');
					foreach($tag_id_arr as $tag_id){
						$a_tag['article_id']=$article_id;
						$a_tag['tag_id']=$tag_id;
						$_article_tags->add($a_tag);
					}
				}
				
				$cid=$article->where("id=$article_id")->getField("cate_id");
				$article_cate=$article_cate->where("id=$cid and is_del=0")->setInc("article_nums");
				$this->success('添加成功',U('Article/index'));
			}
			
		}else {

			$result = $article_cate->order('ord desc,id desc')->where("is_del=0")->select();
			$article_info = $article->where('id='.$id)->find();
			$this->assign('cate_list',$result);
			$this->assign('article',$article_info);
			$this->display();

		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$article = M('Article');
		$article_cate_mod=M('article_cate');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$article->where('id='.$id)->save($data);
			$cid=$article->where("id=$id")->getField("cate_id");
			$article_cate_mod->where("id=$cid and is_del=0")->setDec("article_nums");
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$article = M('Article');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ord'] = $ordid;
				$article->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}

	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$article = M('Article');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$article->where($data)->save($set);
		$val=$article->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
	