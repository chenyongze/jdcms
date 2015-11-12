<?php
class Taobao extends BaseAction{

	//淘宝收集数据并返回到页面
	public function item( $taobao_url ) {
		set_time_limit( 0 );
		$items = M( 'Items' );
		$items_cate = M( 'ItemsCate' );
		$items_site = M( 'ItemsSite' );
		$items_tags = M( 'ItemsTags' );
		$items_tags_item = M( 'ItemsTagsItem' );

		if ( strpos( $taobao_url, 'item.htm' ) ) {
			$url=$taobao_url;
			//获得商品ID
			$item_id = $this->match_item_id( $url );
			//获得移动端URL
			$domain=gain_domain($url);
			$url = 'http://a.m.'.$domain.'/i'.$item_id.'.htm';
		}else {
			$url = $taobao_url;
			//获得商品ID
			$item_id = $this->match_item_mid( $url );
		}

		$item = file_get_contents( $url );

		//  卖家id
		//   $seller_id=$this->match_seller_id($item);

		//获得商品名称
		$title=$this->match_title( $item );
		if ( $title == '' ) {
			return false;
			exit();
		}

		//获得商品图片
		$img=$this->match_image( $item );
		$type = end(explode( '.', $img ));
		$image = explode( '.'.$type, $img );
		$img=$image[0].'.'.$type;

		//获得商品来源
		$domain=gain_domain( $taobao_url );
		$site = $items_site->field( 'id,alias' )->where( "site_domain='".$domain."'" )->find();

		//获得商品价格
		$price=$this->match_price( $item );

		//获得标签
		$tags=$this->get_tags( $title );

		//获得item_key
		$item_key=$site['alias'].'_'.$item_id;

		//获得淘宝客跳转链接
		$url = $this->gain_url( $site['alias'], $item_id );
		if ( !$url ) {
			$url='http://detail.tmall.com/item.htm?id='.$item_id;
		}

		//Ajax返回数据
		$data['seller_id']='';
		$data['title']=strip_tags( $title );
		$data['url']=$url;
		$data['tags']=implode( ' ', $tags );
		$data['price']=$price;
		$data['item_key']=$item_key;
		$data['sid']=$site['id'];
		$data['alias']=$site['alias'];
		$data['cid']='';
		$data['img']=$img;
		return $data;
	}


	//淘宝批量收集数据并添加到数据库
	public function betchitem( $taobao_url, $cate_id, $seller_id ) {
		set_time_limit( 0 );
		$items = M( 'Items' );
		$items_cate = M( 'ItemsCate' );
		$items_site = M( 'ItemsSite' );
		$items_tags = M( 'ItemsTags' );
		$items_tags_item = M( 'ItemsTagsItem' );
		$user = M( 'User' );

		if ( strpos( $taobao_url, 'item.htm' ) ) {
			$url=$taobao_url;
			//获得商品ID
			$item_id = $this->match_item_id( $url );
			//获得移动端URL
			$domain=gain_domain($url);
			$url = 'http://a.m.'.$domain.'/i'.$item_id.'.htm';
		}else {
			$url = $taobao_url;
			//获得商品ID
			$item_id = $this->match_item_mid( $url );
		}

		$item = file_get_contents( $url );

		$add_time=time();
		$cid=$cate_id;

		//  卖家id
		//   $seller_id=$this->match_seller_id($item);

		//获得商品名称
		$title=$this->match_title( $item );
		if ( $title == '' ) {
			return false;
			exit();
		}

		//获得商品图片
		$img=$this->match_image( $item );
		$type = end(explode( '.', $img ));
		$image = explode( '.'.$type, $img );
		$img=$image[0].'.'.$type;

		//获得商品来源
		$domain=gain_domain( $taobao_url );
		$site = $items_site->field( 'id,alias' )->where( "site_domain='".$domain."'" )->find();

		//获得商品价格
		$price=$this->match_price( $item );

		//获得标签
		$tags=$this->get_tags( $title );

		//获得item_key
		$item_key=$site['alias'].'_'.$item_id;

		//下载远程图片
		if ( C( 'down_status' )==1 ) {
			$type = end(explode( '.', $img ));
			$img=$this->down_item($img, $item_key.'.'.$type);
		}

		//获得淘宝客跳转链接
		$url = $this->gain_url( $site['alias'], $item_id );
		if ( !$url ) {
			$url='http://detail.tmall.com/item.htm?id='.$item_id;
		}

		//获取随机uid
		$user_info=$user->field( id )->where( 'is_sys=1' )->order( 'rand()' )->find();

		//审核状态
		$status=( C( 'items_status' )+1 )%2;

		//数据
		$seller_id ? $data['seller_id']=$seller_id : $data['seller_id']='';
		$data['title']=strip_tags( $title );
		$data['url']=$url;
		$data['tag']=$tags;
		$data['price']=$price;
		$data['item_key']=$item_key;
		$data['sid']=$site['id'];
		$data['cid']=$cid;
		$data['img']=$img;
		$data['add_time']=$add_time;
		$data['uid']=$user_info['id'];
		$data['status']=$status;

		if ( $data['item_key'] != '' ) {
			$where['item_key']  = array( 'eq', $data['item_key'] );
		}else {
			$where['url']  = array( 'eq', $data['url'] );
		}
		$where['is_del']  = array( 'eq', 0 );
		//如果添加的商品存在，获得商品的id、cid
		$add_item = $items->field( 'id,cid' )->where( $where )->find();

		//商品存在则将分类中item_nums减1，不存在则添加，新的分类item_nums加1
		if ( $add_item ) {
			$items_cate->where( "id='".$add_item['cid']."'" )->setDec( 'item_nums' );
			$items->where( $where )->save( $data );
			$new_item_id=$add_item['id'];
		}else {
			$new_item_id=$items->add( $data );
		}
		if ( !$new_item_id ) {
			return false;
			exit();
		}
		$items_cate->where( "id='".$data['cid']."'" )->setInc( 'item_nums' ); //分类item_nums加1

		//处理标签
		if ( $add_item ) {
			//已存在商品，先将标签中item_nums减1,删除旧的标签和商品关系，添加新的标签和商品关系
			$old_tag=$items_tags_item->field( 'tag_id' )->where( "item_id='".$add_item['id']."'" )->select();
			foreach ( $old_tag as $tag ) {
				$items_tags->where( "id='".$tag['tag_id']."'" )->setDec( 'item_nums' );
			}
			//删除标签和商品关系
			$items_tags_item->where( "item_id='".$add_item['id']."'" )->delete();
		}

		if ( $tags ) {
			//标签不存在则添加
			foreach ( $tags as $tag ) {
				$isset_id = $items_tags->field( 'id' )->where( "name='".$tag."' and pid='".$cid."'" )->find();
				if ( $isset_id ) {
					$items_tags_item->add( array(
							'item_id' => $new_item_id,
							'tag_id' => $isset_id['id'],
						) );
					$items_tags->where( "id='".$isset_id['id']."'" )->setInc( 'item_nums' ); //标签item_nums加1
				} else {
					$tag_id = $items_tags->add( array( 'name' => $tag, 'pid' => $cid ) );
					$items_tags_item->add( array(
							'item_id' => $new_item_id,
							'tag_id' => $tag_id
						) );
					$items_tags->where( "id='".$tag_id."'" )->setInc( 'item_nums' ); //标签item_nums加1
				}
			}
		}
		return true;
	}

	//正则获取商品ID
	public function match_item_mid( $url ) {
		preg_match( '/\/i(\d+)/si', $url, $result );
		isset( $result ) ? $id = $result[1] : $id = '';
		return $id;
	}

	//正则获取商品ID
	public function match_item_id( $url ) {
		preg_match( '/id=(\d+)/si', $url, $result );
		isset( $result ) ? $id = $result[1] : $id = '';
		return $id;
	}

	//正则获取卖家ID
	public function match_seller_id( $content ) {
		preg_match( '/<div class="left-margin-5">(.*?)进入店铺/si', $content, $result );
		preg_match( '/href="(.*)">/si', $result[1], $re );
		isset( $re ) ? $shop_url = $re[1] : $shop_url = '';
		if ( $shop_url ) {
			unset( $content );
			unset( $result );
			$content = file_get_contents( $shop_url );
			preg_match( '/userId (\d+)/si', $content, $result );
			isset( $result ) ? $seller_id = $result[1] : $seller_id = '';
		}else {
			$seller_id = '';
		}
		return $seller_id;
	}

	//正则获取商品名称
	public function match_title( $content ) {
		preg_match( '/<div class=\"detail\">(.*?)<p>/si', $content, $result );
		isset( $result ) ? $title = trim( addslashes( $result[1] ) ) : $title = '';
		return $title;
	}

	//正则获取商品图片
	public function match_image( $content ) {
		preg_match( '/<img alt=\"(.*?)\" src=\"(.*?)\" \/>/si', $content, $result );
		isset( $result ) ? $image = $result[2] : $image = '';
		return $image;
	}

	//正则获取商品价格
	public function match_price( $content ) {
		preg_match( '/<strong class=\"oran\">(.*?)<\/strong>/si', $content, $result );
		isset( $result ) ? $price = $result[1] : $price = '';
		return $price;
	}

	//获得跳转链接
	public function gain_url( $alias, $item_id ) {

		if ( $alias=='taobao' || $alias=='tmall' ) {
			$result=$this->taobaokeItemsConvert( $item_id );
			$item_info=$result['taobaoke_item_details']['taobaoke_item_detail'];
			$url=$item_info['click_url'];
			return $url;
		}
	}


	/**
	 * //淘宝客商品链接转换
	 *
	 * @param unknown $id ID    商品id,可以多个, ","分隔开.最多40个
	 * @return  items  转换结果数组
	 */
	public function taobaokeItemsConvert( $id ) {

		Vendor( 'Taoapi.Taoapi' );
		header( "Content-type:text/html; charset=UTF-8" );
		//taoapi 初始化
		$Taoapi_Config = Taoapi_Config::Init();
		$Taoapi_Config->setCharset( 'UTF-8' );
		$Taoapi_Config->setAppKey( C( 'taobao_appkey' ) );
		$Taoapi_Config->setAppSecret( C( 'taobao_appsecret' ) );
		//调用api
		$Taoapi = new Taoapi;
		//淘客商品转换(taobao.taobaoke.items.convert)
		$Taoapi->method = 'taobao.taobaoke.items.detail.get';
		//$Taoapi->fields = 'num_iid,nick,title,price,item_location,seller_credit_score,click_url,shop_click_url,pic_url,commission_rate,commission,commission_num,commission_volume,volume';

		$Taoapi->fields = 'click_url';
		$Taoapi->num_iids = $id;
		$Taoapi->pid = C( 'taobaoke_pid' );
		$Taoapi->nick = C( 'taobaoke_nick' );
		if ( C( 'taobaoke_pid' )=='' && C( 'taobaoke_nick' )=='' ) {
			return false;
			//                  $this->error('淘宝客昵称，淘宝客PID未设置，请到系统设置 - App Key 设置。');
		}
		//提交请求
		$TaobaokeData = $Taoapi->Send( 'get', 'xml' )->getArrayData();
		//检测API是否遇到错误
		if ( $Taoapi->getErrorInfo() ) {
			return false;
			//                $message = $Taoapi->getErrorInfo();
			//                $this->error($message['sub_msg']);
		}

		//打印获取到的API数据结果
		return $TaobaokeData;
	}
	/**
	 * //淘宝客关键词搜索商品
	 *
	 * @param unknown $config 参数
	 * @return  items  转换结果数组
	 */
	public function taobaokeKeyword( $config = array() ) {
		if(!isset($config['keyword'])){
			return false;
		}
		Vendor( 'Taoapi.Taoapi' );
		header( "Content-type:text/html; charset=UTF-8" );
		//taoapi 初始化
		$Taoapi_Config = Taoapi_Config::Init();
		$Taoapi_Config->setCharset( 'UTF-8' );
		$Taoapi_Config->setAppKey( C( 'taobao_appkey' ) );
		$Taoapi_Config->setAppSecret( C( 'taobao_appsecret' ) );
		//调用api
		$Taoapi = new Taoapi;
		//淘客商品搜索(taobao.taobaoke.items.get)
		$Taoapi->method = 'taobao.taobaoke.items.get';
		$Taoapi->fields = 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume';
		$Taoapi->keyword = $config['keyword'];
		$Taoapi->cid = $config['cid'];
		if ( $config['start_price']!='' && $config['end_price']!='' ) {
			$Taoapi->start_price = $config['start_price'];
			$Taoapi->end_price = $config['end_price'];
		}
		if ( $config['start_credit']!='' && $config['end_credit']!='' ) {
			$Taoapi->start_credit = $config['start_credit'];
			$Taoapi->end_credit = $config['end_credit'];
		}
		$Taoapi->sort = $config['sort'];
		$Taoapi->guarantee = $config['guarantee'];
		if ( $config['start_commissionRate'] != '' && $config['end_commissionRate'] !='' ) {
			$Taoapi->start_commissionRate = $config['start_commissionRate'];
			$Taoapi->end_commissionRate = $config['end_commissionRate'];
		}
		if ( $config['start_commissionNum']!='' && $config['end_commissionNum'] != '' ) {
			$Taoapi->start_commissionNum = $config['start_commissionNum'];
			$Taoapi->end_commissionNum = $config['end_commissionNum'];
		}

		if ( $config['start_totalnum'] !='' && $config['end_totalnum'] !='' ) {
			$Taoapi->start_totalnum = $config['start_totalnum'];
			$Taoapi->end_totalnum = $config['end_totalnum'];
		}
		$Taoapi->cash_coupon = $config['cash_coupon'];
		$Taoapi->sevendays_return = $config['sevendays_return'];
		$Taoapi->real_describe = $config['real_describe'];
		$Taoapi->cash_ondelivery = $config['cash_ondelivery'];
		$Taoapi->mall_item = $config['mall_item'];

		$Taoapi->page_no = $config['page_no'];

		$Taoapi->pid = C( 'taobaoke_pid' );
		$Taoapi->nick = C( 'taobaoke_nick' );
		if ( C( 'taobaoke_pid' )=='' && C( 'taobaoke_nick' )=='' ) {
			return false;
			//                  $this->error('淘宝客昵称，淘宝客PID未设置，请到 [系统设置 - App Key 设置] 进行设置。');
		}
		//提交请求
		$TaobaokeData = $Taoapi->Send( 'get', 'xml' )->getArrayData();
		//检测API是否遇到错误
		if ( $Taoapi->getErrorInfo() ) {
			return false;
			//                $message = $Taoapi->getErrorInfo();
			//                $this->error($message['sub_msg']);
		}
		//打印获取到的API数据结果
		return $TaobaokeData;

	}
	
	/**
	 * //淘宝店铺信息
	 *
	 * @param unknown $id ID    商品id,可以多个, ","分隔开.最多40个
	 * @return  items  转换结果数组
	 */
	public function taobaoShopInfo( $nick ) {
	
		Vendor( 'Taoapi.Taoapi' );
		header( "Content-type:text/html; charset=UTF-8" );
		//taoapi 初始化
		$Taoapi_Config = Taoapi_Config::Init();
		$Taoapi_Config->setCharset( 'UTF-8' );
		$Taoapi_Config->setAppKey( C( 'taobao_appkey' ) );
		$Taoapi_Config->setAppSecret( C( 'taobao_appsecret' ) );
		//调用api
		$Taoapi = new Taoapi;

		$Taoapi->method = 'taobao.shop.get';

		$Taoapi->fields = 'sid,cid,title,pic_path';
		$Taoapi->nick = $nick;
		
		//提交请求
		$TaobaokeData = $Taoapi->Send( 'get', 'xml' )->getArrayData();
		//检测API是否遇到错误
		if ( $Taoapi->getErrorInfo() ) {
			return false;
			//                $message = $Taoapi->getErrorInfo();
			//                $this->error($message['sub_msg']);
		}
		
		//打印获取到的API数据结果
		return $TaobaokeData;
	}
	
	/**
	 * //淘宝分类信息
	 *
	 * @param unknown $id ID    商品id,可以多个, ","分隔开.最多40个
	 * @return  items  转换结果数组
	 */
	public function taobaoCateInfo( $cid ) {
	
		Vendor( 'Taoapi.Taoapi' );
		header( "Content-type:text/html; charset=UTF-8" );
		//taoapi 初始化
		$Taoapi_Config = Taoapi_Config::Init();
		$Taoapi_Config->setCharset( 'UTF-8' );
		$Taoapi_Config->setAppKey( C( 'taobao_appkey' ) );
		$Taoapi_Config->setAppSecret( C( 'taobao_appsecret' ) );
		//调用api
		$Taoapi = new Taoapi;
	
		$Taoapi->method = 'taobao.itemcats.get';
	
		$Taoapi->fields = 'cid,parent_cid,name,is_parent';
		$Taoapi->parent_cid = $cid;
	
		//提交请求
		$TaobaokeData = $Taoapi->Send( 'get', 'xml' )->getArrayData();
		//检测API是否遇到错误
		if ( $Taoapi->getErrorInfo() ) {
			return false;
			//                $message = $Taoapi->getErrorInfo();
			//                $this->error($message['sub_msg']);
		}
	
		//打印获取到的API数据结果
		return $TaobaokeData;
	}

}
