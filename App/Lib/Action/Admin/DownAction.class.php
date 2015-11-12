<?php
class DownAction extends BaseAction{
	public function index(){
		set_time_limit(0);
		$items_mod = M('Items');
		$start=$_GET['start'];
		$where['img']=array('like','%http://%');
		$count=$items_mod->count();
		$items_info=$items_mod->field('id,img,item_key')->where($where)->limit('0,100')->select();
		if ($items_info){
			$dir=date("Ymd");
			mkdir('./Uploads/LocalItems/'.$dir);
			foreach ($items_info as $item){
				preg_match('/^(http:\/\/)/si',$item['img'],$result);
				if ($result){
					$type = end(explode( '.', $item['img'] ));
					import("ORG.Net.Http");
					$http=new Http();
					$http->curlDownload($item['img'].'_100x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_100x1000.'.$type);
					$http->curlDownload($item['img'].'_210x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_210x1000.'.$type);
					$http->curlDownload($item['img'].'_350x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_350x1000.'.$type);
					$http->curlDownload($item['img'].'_500x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_500x1000.'.$type);
					$data['img']=C('web_path').'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type;
					$items_mod->where('id='.$item['id'])->save($data);
				}
			}
			echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';		
			if (($start+100) < 900){
				sleep(1);
				$start +=100;
				$url = C('web_path')."index.php?a=index&m=Down&g=Admin&start=$start";
				$this->redirect($url);
			}
		}else {
			$this->error('所有远程图片均已下载到本地');
		}

		$this->success('生成本地图片成功');
	}
	
	public function item(){
		$items_mod = M('Items');
		$where['id'] = $_GET['id'];
		$item=$items_mod->field('id,img,item_key')->where($where)->find();
		preg_match('/^(http:\/\/)/si',$item['img'],$result);
		if ($result){
			$dir=date("Ymd");
			mkdir('./Uploads/LocalItems/'.$dir);
			$type = end(explode( '.', $item['img'] ));
			import("ORG.Net.Http");
			$http=new Http();
			if(strpos($item['img'], 'taobao')!==false|| strpos($item['img'], 'tmall') !==false){
				$http->curlDownload($item['img'].'_100x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_100x1000.'.$type);
				$http->curlDownload($item['img'].'_210x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_210x1000.'.$type);
				$http->curlDownload($item['img'].'_350x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_350x1000.'.$type);
				$http->curlDownload($item['img'].'_500x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_500x1000.'.$type);
			}elseif(strpos($item['img'], 'paipaiimg')!==false){
				$type = end(explode( '.', $item['img']));
				$img_arr=explode( '.', $item['img']);
				unset($img_arr[count($img_arr)-1]);
				$newimg_pre=implode(".",$img_arr);
				$http->curlDownload($newimg_pre.".160x160.".$type,'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_100x1000.'.$type);
				$http->curlDownload($newimg_pre.".200x200.".$type,'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_210x1000.'.$type);
				$http->curlDownload($newimg_pre.".300x300.".$type,'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_350x1000.'.$type);
				$http->curlDownload($newimg_pre.".".$type,'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type.'_500x1000.'.$type);
			}
			$data['img']=C('web_path').'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.'.$type;
			$items_mod->where('id='.$item['id'])->save($data);
			$this->success('生成本地图片成功');
		}else {
			$this->error('图片已下载到本地');
		}
	}
}