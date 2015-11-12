<?php
class HtmlAction extends BaseAction{

	//显示选择页面
	public function set(){

		$album_cate = M('AlbumCate');
		//专辑分类循环
		$albumCate_list = $album_cate->where('is_del=0')->order('ord desc')->select();
		$this->assign('albumCate_list',$albumCate_list);
		
		$items_cate = M('ItemsCate');		
		//商品分类循环
		$cate_list = $items_cate->where('pid=0 and is_del=0')->order('ord desc')->select();		
		$this->assign('cate_list',$cate_list);
		$this->display();
	}
	
	//生成首页静态页面
	public function index(){
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
		$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Index/index');
		$content = file_get_contents($url);
		$htmlfile=$this->build('index','./',$content);
		$htmlfile=ltrim($htmlfile,'./');
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成首页：'.$htmlfile.'</span><br />';
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';		
		$this->redirect('Html:set');
	}
	
	//生成店铺页静态页面
	public function shop(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
		$file='<script>document.body.scrollTop = document.body.scrollHeight;</script>';
		$shop = M('Shop');
		$result=$shop->field('id')->where('is_del=0')->select();
		foreach ($result as $key=>$val){
			$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Shop/index',array('id'=>$val['id']));
			$htmlfile=$this->gain_content($url,$val['id'],C('home.url_dir_shop'));
			$htmlfile=str_replace('./'.C('home.url_dir_shop').'/', '', $htmlfile);
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成店铺页：'.$htmlfile.'</span><br />'.$file;
		}
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}
	
	//生成文章页静态页面
	public function article(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
		$file='<script>document.body.scrollTop = document.body.scrollHeight;</script>';
		$articleCate = M('ArticleCate');
		$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Article/index');
		$htmlfile=$this->gain_content($url,0,C('home.url_dir_articleCate'),20);
		$htmlfile=str_replace('./'.C('home.url_dir_articleCate').'/', '', $htmlfile);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成文章分类页：'.$htmlfile.'</span><br />'.$file;
		$result=$articleCate->field('id')->where('status=1 and is_del=0')->select();
		foreach ($result as $key=>$val){
			$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Article/index',array('id'=>$val['id']));
			$htmlfile=$this->gain_content($url,$val['id'],C('home.url_dir_articleCate'),20);
			$htmlfile=str_replace('./'.C('home.url_dir_articleCate').'/', '', $htmlfile);
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成文章分类页：'.$htmlfile.'</span><br />'.$file;
		}
		unset($result);
		$article = M('Article');
		$result=$article->field('id')->where('status=1 and is_del=0')->select();
		foreach ($result as $key=>$val){
			$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Article/detail',array('id'=>$val['id']));
			$htmlfile=$this->gain_content($url,$val['id'],C('home.url_dir_article'));
			$htmlfile=str_replace('./'.C('home.url_dir_article').'/', '', $htmlfile);
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成文章页：'.$htmlfile.'</span><br />'.$file;
		}
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}
	
	//按专辑列表生成静态页面
	public function albumCate(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
		$file='<script>document.body.scrollTop = document.body.scrollHeight;</script>';
		$album_cate_mod=M("AlbumCate");
		$acid=$_REQUEST['acid'];
		$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Album/index');
		$htmlfile=$this->gain_content($url,0,C('home.url_dir_albumCate'),10);
		$htmlfile=str_replace('./'.C('home.url_dir_albumCate').'/', '', $htmlfile);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成专辑分类页：'.$htmlfile.'</span><br />'.$file;
		if ($acid==0){
			$result=$album_cate_mod->field('id')->where('is_del=0')->order('id asc')->select();
			foreach ($result as $key=>$val){
				$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Album/index',array('id'=>$val['id']));
				$htmlfile=$this->gain_content($url,$val['id'],C('home.url_dir_albumCate'),10);
				$htmlfile=str_replace('./'.C('home.url_dir_albumCate').'/', '', $htmlfile);
				echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成专辑分类页：'.$htmlfile.'</span><br />'.$file;
			}
		}else {
			$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Album/index',array('id'=>$acid));
			$htmlfile=$this->gain_content($url,$acid,C('home.url_dir_albumCate'),10);
			$htmlfile=str_replace('./'.C('home.url_dir_albumCate').'/', '', $htmlfile);
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成专辑分类页：'.$htmlfile.'</span><br />'.$file;
		}
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}
	
	//按专辑生成静态页面
	public function album(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
		$file='<script>document.body.scrollTop = document.body.scrollHeight;</script>';
		$album_mod=M("Album");
		$aid=$_REQUEST['aid'];
		$where['status']=1;
		$where['is_del']=0;
		if ($aid==0){
			$result=$album_mod->field('id')->where($where)->order('id asc')->select();
			foreach ($result as $key=>$val){
				$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Uc/albumDetail',array('id'=>$val['id']));
				$content=file_get_contents($url);
				$htmlfile=$this->build($val['id'],'./'.C('home.url_dir_album').'/',$content);
				$htmlfile=ltrim($htmlfile,'./'.C('home.url_dir_album'));
				echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成专辑页：'.$htmlfile.'</span><br />'.$file;
			}
		}else {
			$where['cid']=$aid;
			$result=$album_mod->field('id')->where($where)->order('id asc')->select();
			foreach ($result as $key=>$val){
				$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Uc/albumDetail',array('id'=>$val['id']));
				$content=file_get_contents($url);
				$htmlfile=$this->build($val['id'],'./'.C('home.url_dir_album').'/',$content);
				$htmlfile=ltrim($htmlfile,'./'.C('home.url_dir_album'));
				echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成专辑页：'.$htmlfile.'</span><br />'.$file;
			}
		}
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}
			
	//按分类生成静态页面
	public function cate(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
		$file='<script>document.body.scrollTop = document.body.scrollHeight;</script>';
		$items_cate_mod=M("Items_cate");
		$pid=$_REQUEST['pid'];
		if ($pid==0){
			$result=$items_cate_mod->field('id')->where('pid=0 and is_del=0')->order('id asc')->select();
			foreach ($result as $key=>$val){
				$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Cate/index',array('id'=>$val['id']));
				$htmlfile=$this->gain_content($url,$val['id'],C('home.url_dir_cate'),100);
				$htmlfile=str_replace('./'.C('home.url_dir_cate').'/', '', $htmlfile);
				echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成分类页：'.$htmlfile.'</span><br />'.$file;
			}
		}else {
			$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Cate/index',array('id'=>$pid));
			$htmlfile=$this->gain_content($url,$pid,C('home.url_dir_cate'),100);
			$htmlfile=str_replace('./'.C('home.url_dir_cate').'/', '', $htmlfile);
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成分类页：'.$htmlfile.'</span><br />'.$file;
		}
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}
	
	//按标签生成静态页面
	public function tag(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
		$file='<script>document.body.scrollTop = document.body.scrollHeight;</script>';
		$items_tags_mod=M("Items_tags");
		$tid=$_REQUEST['tid'];
		if ($tid==0){
			$where['is_del']=0;
			$result=$items_tags_mod->field('id')->where($where)->order('id asc')->select();
			foreach ($result as $key=>$val){
				$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Cate/tag',array('id'=>$val['id']));
				$htmlfile=$this->gain_content($url,$val['id'],C('home.url_dir_tag'),100);
				$htmlfile=str_replace('./'.C('home.url_dir_tag').'/', '', $htmlfile);
				echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成标签页：'.$htmlfile.'</span><br />'.$file;
			}
		}else {
			$where['pid']=$tid;
			$where['is_del']=0;
			$result=$items_tags_mod->field('id')->where($where)->order('id asc')->select();
			foreach ($result as $key=>$val){
				$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Cate/tag',array('id'=>$val['id']));
				$htmlfile=$this->gain_content($url,$val['id'],C('home.url_dir_tag'),100);
				$htmlfile=str_replace('./'.C('home.url_dir_tag').'/', '', $htmlfile);
				echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成标签页：'.$htmlfile.'</span><br />'.$file;
			}
		}
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}
	
	//生成商品静态页面按分类
	public function itemcate(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">正在生成...</span><br />';
		$items_mod=M('Items');
		$cid=$_REQUEST['cid'];
		$start=$_REQUEST['start'];		
		$where['status']=1;
		$where['is_del']=0;
		if ($cid != 0){
			$where['cid']=$cid;
		}
		$count = $items_mod->field('id')->where($where)->count();
		$result = $items_mod->field('id')->where($where)->limit($start.',20')->order('id asc')->select();//所有符合条件的商品信息
		foreach ($result as $key=>$val){
			$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Item/index',array('id'=>$val['id']));
			$content=file_get_contents($url);
			$htmlfile=$this->build($val['id'],'./'.C('home.url_dir_item').'/',$content);
			$htmlfile=ltrim($htmlfile,'./'.C('home.url_dir_item'));
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成商品页：'.$htmlfile.'</span><br />';
		}
		if (($start+20) < $count){
			sleep(1);
			$start +=20;
			$url = C('web_path')."index.php?a=itemcate&m=Html&g=Admin&start=$start&cid=$cid";
			$this->redirect($url);
		}
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}
	
	//生成商品静态页面按时间
	public function itemday(){
		set_time_limit(0);
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		ob_implicit_flush(true);
		echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">正在生成...</span><br />';
		$items_mod=M('Items');
		$day=$_REQUEST['day'];
		$start=$_REQUEST['start'];
		$time=date('Y-m-d',(time()-$day*60*60*24));
		$limit_time=strtotime($time);
		$where['add_time']=array('gt',$limit_time);
		$where['status']=1;
		$where['is_del']=0;
		$count = $items_mod->field('id')->where($where)->count();
		$result = $items_mod->field('id')->where($where)->limit($start.',20')->order('id asc')->select();//所有符合条件的商品信息
		foreach ($result as $key=>$val){
			$url='http://'.$_SERVER['HTTP_HOST'].U('Home/Item/index',array('id'=>$val['id']));
			$content=file_get_contents($url);
			$htmlfile=$this->build($val['id'],'./'.C('home.url_dir_item').'/',$content);
			$htmlfile=ltrim($htmlfile,'./'.C('home.url_dir_item'));
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成商品页：'.$htmlfile.'</span><br />';
		}
		if (($start+20) < $count){
			sleep(1);
			$start +=20;
			$url = C('web_path')."index.php?a=itemday&m=Html&g=Admin&start=$start&day=$day";
			$this->redirect($url);
		}
		echo '<span style="color:#0099CC;font-size:15px;line-height:20px;">任务完成</span>';
		$this->redirect('Html:set');
	}

	//生成静态页类
	private function build($htmlfile='',$htmlpath='',$content){
		
		$htmlpath   = !empty($htmlpath)?$htmlpath:HTML_PATH;
		$htmlfile =  $htmlpath.$htmlfile.C('home.html_file_suffix');
		if(!is_dir(dirname($htmlfile)))
			// 如果静态目录不存在 则创建
			mk_dir(dirname($htmlfile));
		if(false === file_put_contents($htmlfile,$content))
			throw_exception(L('_CACHE_WRITE_ERROR_').':'.$htmlfile);
		return $htmlfile;
	}
	
	//正则获取商品总数
	private function match_nums($content){
		preg_match('/<span class=\"totalRows\">(.*?)<\/span> 条记录 <span class=\"nowPage\">(.*?)<\/span>\/(.*?) 页/',$content,$result);
		isset($result) ? $nums = $result : $nums = '';
		return $nums;
	}
	
	//获取静态页内容并修改分页
	private function gain_content($url,$id,$dir,$limit){
		
		$content = file_get_contents($url);
		$nums = $this->match_nums($content);
		$totalRows = $nums[1];
		$totalPages = $nums[3];
		//如果没有内容,直接生成
		if ($totalPages == ''){
			return $this->build($id,'./'.$dir.'/',$content);		
		}
		//引入自定义分页类
		import("@.ORG.Page");
		$message='';
		for ($i=1; $i<=$totalPages; $i++) {
			$url=$url.'&p='.$i;
			$content = file_get_contents($url);
			$page=new page($limit,$totalRows,$i,5,'./',$id);
			$pages=$page->subPageCss();
			$content=preg_replace('/<div class="page">(.*)<\/div>/', '<div class="page">'.$pages.'</div>', $content);

			if ($i==1){
				$str = $this->build($id,'./'.$dir.'/',$content);
				$message .= $str."\r\n";
			}else{
				$str = $this->build($id.'-'.$i,'./'.$dir.'/',$content);
				$message .= $str."\r\n";
			}			
		}
		return $message;
	}

}