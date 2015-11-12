<?php
class ArticleCollectAction extends BaseAction{

	//列表显示
	public function index(){
		$_articleCollect = M('ArticleCollect');
		import("ORG.Util.Page");
		$count=$_articleCollect->where('is_del = 0')->count();
		$page=new Page($count,20);
		$show=$page->show();
		$list=$_articleCollect->field('id,name,add_time,last_time,char_code,ord')->where('is_del = 0')->order('ord desc')->limit($page->firstRow.','.$page->listRows)->select();
		foreach ($list as $key=>$val){
			$list[$key]['key']= ++$page->firstRow;
		}
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	
	//采集
	public function collect(){
		set_time_limit(0);
		$_articleCollect = M('ArticleCollect');
		$_articleCate = M('ArticleCate');
		$_article = M('Article');
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$where['id']=$id;
		$where['is_del']=0;
		$info=$_articleCollect->where($where)->find();
		foreach ($info as $k => $v){
			$$k=$v;
		}
		
		//分页匹配
		$page_rule=preg_replace('/\(\*\)/', '(.*?)', $page_rule);
		$page_rule=str_replace('/', '\\/', $page_rule);
		$page_pattern='/'.$page_rule.'/si';
		//标题匹配
		$title_rule=preg_replace('/\(\*\)/', '(.*?)', $title_rule);
		$title_rule=str_replace('/', '\\/', $title_rule);
		$title_pattern='/'.$title_rule.'/si';
		//文章匹配
		$content_rule=preg_replace('/\(\*\)/', '(.*?)', $content_rule);
		$content_rule=str_replace('/', '\\/', $content_rule);
		$content_pattern='/'.$content_rule.'/si';
		//标题过滤
		$title_filter_pattern=array();
		$title_filter_arr=explode("\r\n", $title_filter);
		foreach ($title_filter_arr as $filter){
			$filter=str_replace('/', '\\/', $filter);
			$pattern='/'.$filter.'/si';
			$title_filter_pattern[]=$pattern;
		}
		//文章过滤
		$content_filter_pattern=array();
		$content_filter_arr=explode("\r\n", $content_filter);
		foreach ($content_filter_arr as $filter){
			$filter=str_replace('/', '\\/', $filter);
			$pattern='/'.$filter.'/si';
			$content_filter_pattern[]=$pattern;
		}

		$result=$this->_gainlinks($info);
		$article_urls=$result['article_urls'];
		$total=0;
		foreach ($article_urls as $article_url){
			$is_set=$_article->where("abst='".$article_url."'")->find();
			if ($is_set){
				unset($is_set);
				continue;
			}
			unset($is_set);
			//找出文章分页
			$page_urls=array();
			$page_urls=array($article_url);
			if ($s_page == 'all'){
				$links=$this->_formatlinks($article_url,$page_pattern,$char_code);
				if ($links){
					$page_urls=array_merge_recursive($page_urls,$links);
				}
			}elseif ($s_page == 'next'){
				$nums=0;
				do {
					$links=$this->_formatlinks($page_urls[$nums],$page_pattern,$char_code);
					if ($links){
						$page_urls=array_merge_recursive($page_urls,$links);
					}
					$count=count($page_urls);
					$nums++;
				}while ($count==($nums+1));
			}
			$page_urls=array_unique($page_urls);
			//找出文章标题
			$title='';
			$content=mb_convert_encoding(file_get_contents($article_url), 'UTF-8', $char_code);
			preg_match($title_pattern, $content, $result);
			if ($result){
				$title=$result[1];
			}
			unset($result);
			foreach ($title_filter_pattern as $pattern){
				$title=preg_replace($pattern, '', $title);
			}
			if (!$title){
				continue;
			}
			//找出文章内容
			$article_content='';
			foreach ($page_urls as $page_url){
				$content=mb_convert_encoding(file_get_contents($page_url), 'UTF-8', $char_code);
				preg_match($content_pattern, $content, $result);
				if ($result){
					$article_content .=$result[1];
				}
				unset($result);
			}
			foreach ($content_filter_pattern as $pattern){
				$article_content=preg_replace($pattern, '', $article_content);
			}
			if (!$article_content){
				continue;
			}
			 //下载图片到本地
			$images=$this->_stripimages($article_content);
			foreach ($images as $img){
				$image=$this->down_article($img, md5($img));
				$article_content=str_replace($img, $image, $article_content);
			}
			$data['abst']=$article_url;
			$data['title']=$title;
			$data['info']=$article_content;
			$data['add_time']=time();
			$data['status']=1;
			if ($cate_id == 0){
				$cate_info=$_articleCate->field('id')->where('status=1 and is_del=0')->order('rand()')->find();
				$data['cate_id']=$cate_info['id'];
			}else {
				$data['cate_id']=$cate_id;
			}

			
			$totle=C('article_tags_totle') ? C('article_tags_totle') : 10;
			$limit=C('article_tags_limit') ? C('article_tags_limit') : 1;
			
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
			$article_id=$_article->add($data);
			if($tag_id_arr){
				$_article_tags=M('ArticleTags');
				foreach($tag_id_arr as $tag_id){
					$a_tag['article_id']=$article_id;
					$a_tag['tag_id']=$tag_id;
					$_article_tags->add($a_tag);
				}
			}
			if ($article_id){
				$_articleCate->where("id=".$data['cate_id'])->setInc("article_nums");
				$total++;
			}
		}
		$map['last_time']=time();
		$_articleCollect->where($where)->save($map);
		$this->ajaxReturn($total);
	}
	
	//添加
	public function add(){
		$_articleCate = M('ArticleCate');
		$cate_info=$_articleCate->field('id,name')->where('status=1 and is_del=0')->order('ord desc')->select();
		if(!$cate_info){
			$this->error('请先添加文章分类',U('ArticleCollect/index'));
		}
		$this->assign('cate_info',$cate_info);
		$this->display('edit');
	}
	
	//修改
	public function edit(){
		set_time_limit(0);
		$_articleCollect = M('ArticleCollect');
		$_articleCate = M('ArticleCate');
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$step=isset($_REQUEST['step'])?$_REQUEST['step']:1;
		$this->assign('step',$step);
		if ($step==1){
			if ($_POST['submit']){
				$data=$_articleCollect->create();
				if ($id){
					$_articleCollect->where('id='.$id)->save($data);
					$this->redirect('Admin/ArticleCollect/edit',array('id'=>$id,'step'=>2));
				}else {
					$data['add_time']=time();
					$id=$_articleCollect->add($data);
					$this->redirect('Admin/ArticleCollect/edit',array('id'=>$id,'step'=>2));
				}
			}else {
				$where['id']=$id;
				$where['is_del']=0;
				$info=$_articleCollect->where($where)->order('id desc')->find();
				$this->assign('info',$info);
				$cate_info=$_articleCate->field('id,name')->where('status=1 and is_del=0')->order('ord desc')->select();
				if(!$cate_info){
					$this->error('请先添加文章分类',U('ArticleCollect/index'));
				}
				$this->assign('cate_info',$cate_info);
				$this->display();
			}
		}elseif ($step==2){
			$this->assign('id',$id);
			if ($_POST['submit']){
				$this->redirect('Admin/ArticleCollect/edit',array('id'=>$id,'step'=>3));
			}else {
				$where['id']=$id;
				$where['is_del']=0;
				$info=$_articleCollect->where($where)->find();
				$result=$this->_gainlinks($info);
				$this->assign('test_list_url',$result['list_urls']);
				$this->assign('test_article_urls',$result['article_urls']);
				$this->display('edit_2');
			}
		}elseif ($step==3){
			if ($_POST['submit']){
				$data=$_articleCollect->create();
				if ($id){
					$_articleCollect->where('id='.$id)->save($data);
					$this->redirect('Admin/ArticleCollect/edit',array('id'=>$id,'step'=>4,'url'=>$_POST['test_article_url']));
				}else {
					$this->error('错误，请重新操作',U('ArticleCollect/index'));
				}
			}else {
				$where['id']=$id;
				$where['is_del']=0;
				$info=$_articleCollect->where($where)->find();
				$result=$this->_gainlinks($info);
				$this->assign('test_article_url',array_shift($result['article_urls']));
				$this->assign('info',$info);
				$this->display('edit_3');
			}
		}elseif ($step==4){
			if ($_POST['submit']){
				$this->redirect('Admin/ArticleCollect/index');
			}else {
				$test_article_url=$_GET['url'];
				$where['id']=$id;
				$where['is_del']=0;
				$info=$_articleCollect->where($where)->find();
				foreach ($info as $k => $v){
					$$k=$v;
				}
					
				//找出文章分页
				$page_rule=preg_replace('/\(\*\)/', '(.*?)', $page_rule);
				$page_rule=str_replace('/', '\\/', $page_rule);
				$pattern='/'.$page_rule.'/si';
				$page_urls=array($test_article_url);
				if ($s_page == 'all'){
					$links=$this->_formatlinks($test_article_url,$pattern,$char_code);
					if ($links){
						$page_urls=array_merge_recursive($page_urls,$links);
					}
				}elseif ($s_page == 'next'){
					$nums=0;
					do {
						$links=$this->_formatlinks($page_urls[$nums],$pattern,$char_code);
						if ($links){
							$page_urls=array_merge_recursive($page_urls,$links);
						}
						$count=count($page_urls);
						$nums++;
					}while ($count==($nums+1));
				}
				$page_urls=array_unique($page_urls);
				//找出文章标题
				$title_rule=preg_replace('/\(\*\)/', '(.*?)', $title_rule);
				$title_rule=str_replace('/', '\\/', $title_rule);
				$pattern='/'.$title_rule.'/si';
				$content=mb_convert_encoding(file_get_contents($test_article_url), 'UTF-8', $char_code);
				preg_match($pattern, $content, $result);
				if ($result){
					$title=$result[1];
				}
				unset($result);
				$title_filter_arr=explode("\r\n", $title_filter);
				foreach ($title_filter_arr as $filter){
					$filter=str_replace('/', '\\/', $filter);
					$pattern='/'.$filter.'/si';
					$title=preg_replace($pattern, '', $title);
				}
				//找出文章内容
				$content_rule=preg_replace('/\(\*\)/', '(.*?)', $content_rule);
				$content_rule=str_replace('/', '\\/', $content_rule);
				$pattern='/'.$content_rule.'/si';
				foreach ($page_urls as $page_url){
					$content=mb_convert_encoding(file_get_contents($page_url), 'UTF-8', $char_code);
					preg_match($pattern, $content, $result);
					if ($result){
						$article_content .=$result[1];
					}
					unset($result);
				}
				$content_filter_arr=explode("\r\n", $content_filter);
				foreach ($content_filter_arr as $filter){
					$filter=str_replace('/', '\\/', $filter);
					$pattern='/'.$filter.'/si';
					$article_content=preg_replace($pattern, '', $article_content);
				}

//				下载图片到本地
// 				$images=$this->_stripimages($article_content);
// 				foreach ($images as $img){
// 					$image=$this->down_article($img, md5($img));
// 					$article_content=str_replace($img, $image, $article_content);
// 				}

				$article_info="文章标题：\r\n".$title."\r\n\r\n文章内容：\r\n".$article_content;
					
				$this->assign('article_info',$article_info);
				$this->assign('test_article_url',$test_article_url);
				$this->assign('info',$info);
				$this->display('edit_4');
				}
			}		
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$_articleCollect = M('ArticleCollect');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$_articleCollect->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	

	//排序
	public function order(){
		if ($_POST['order']){
			$_articleCollect = M('ArticleCollect');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$_articleCollect->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	
	//列表页链接
	public function _gainlinks($info){
		foreach ($info as $k => $v){
			$$k=$v;
		}
		$list_urls=array();
		if ($s_url=='auto' && isset($start_match_nums) && $end_match_nums && $inc_nums){
			for ($i=$start_match_nums;$i<=$end_match_nums;$i=$i+$inc_nums){
				$list_urls[]=preg_replace('/\(\*\)/', $i, $match_urls);
			}
		}
		if ($urls){
			$urls = str_replace("\r\n", "\n", $urls);
			$urls = trim(str_replace("\r", "\n", $urls));
			$list_urls=array_merge($list_urls,explode("\n", $urls));
		}
		$start_html=str_replace('/', '\\/', $start_html);
		$end_html=str_replace('/', '\\/', $end_html);
		$pattern='/'.$start_html.'(.*)'.$end_html.'/si';
		$article_urls=array();
		foreach ($list_urls as $url){
			$links=$this->_formatlinks($url,$pattern,$char_code);
			if ($links){
				$article_urls=array_merge($article_urls,$links);
			}
		}
		$article_urls=array_unique($article_urls);
		$must=explode(',', $include);
		$never=explode(',', $no_include);
		foreach ($article_urls as $k => $v){
			foreach ($must as $val){
				$val=trim($val);
				if ($val){
					if (false === strpos($v, $val)){
						unset($article_urls[$k]);
					}
				}
			}	
			foreach ($never as $val){
				$val=trim($val);
				if ($val){
					if (!false === strpos($v, $val)){
						unset($article_urls[$k]);
					}
				}
			}
		}
		$links['list_urls']=$list_urls;
		$links['article_urls']=$article_urls;
		return $links;
	}
	
	public function _formatlinks($url,$pattern,$char_code){
		$u=parse_url($url);
		$host=$u['scheme'].'://'.$u['host'].'/';
		$content=mb_convert_encoding(file_get_contents($url), 'UTF-8', $char_code);
		preg_match($pattern, $content, $result);
		if ($result){
			$re=$this->_striplinks($result[1]);
			foreach ($re as $k => $v){
				if (false === strpos($v, 'http')){
					$re[$k]=$host.$v;
				}
			}
			return $re;
		}
	}
	
}