<?php
class PushAction extends BaseAction{
	public function index(){
		$items_cate=M('ItemsCate');
		$cate=$items_cate->field('id,name')->where('pid = 0 and is_del=0')->order('ord desc')->select();
		foreach ($cate as $key=>$val){
			$cate_list[$key]=$val['name'].'-'.$val['id'];
		}
		if ($_POST['submit']){
			foreach ($cate_list as $val){
				if (in_array($val, $_POST['cate'])){
					$result[]=$val.'-1';
				}else {
					$result[]=$val.'-0';
				}
			}
			$push['cate']=implode(' ',$result);
			$push['cps']=$_POST['cps'];
			$push['price']=$_POST['price'];
			$push['nums']=$_POST['nums'];
			$push['auto_push']=$_POST['auto_push'];
			$file = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<push>
<cate></cate>
<cps></cps>
<price></price>
<nums></nums>
<auto_push></auto_push>
</push>
XML;
			$xml = simplexml_load_string($file);	
			$xml->cate=$push['cate'];
			$xml->cps=$push['cps'];
			$xml->price=$push['price'];
			$xml->nums=$push['nums'];
			$xml->auto_push=$push['auto_push'];
			$xml->asXML('./Public/statics/push.xml');
			
			$config['push_request']=json_encode($push);
			$this->updateconfig($config);
			
		}else {

			$result=json_decode(C('push_request'),true);
			$result['cate']=explode(' ', $result['cate']);
			$this->assign('push',$result);
			$this->assign('cate',$cate);
			$this->display();
		}
	}
	
}