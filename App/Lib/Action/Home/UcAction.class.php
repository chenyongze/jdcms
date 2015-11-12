<?php
class UcAction extends BaseAction {
	public function check_login() {
		// 判断是否登录
		$user_mod = M ( "User" );
		$id = $_COOKIE ['id'];
		if ($id) {
			$user_info = $user_mod->where ( "id=$id" )->find ();
			// $this->assign("id",$id);
			$this->assign ( "user_info", $user_info );
		} else {
			$url = get_url ( 'login', '', 'user' );
			header ( 'location:' . $url );
		}
	}
	public function others() {
		$user_mod = M ( "User" );
		$oUid = intval($_GET ['id']);
		if ($oUid == $_COOKIE ['id']) {
			$oUid = '';
		}
		if ($oUid) {
			$id = $oUid;
			$user_info = $user_mod->where ( "id=$oUid and status=1 and is_del=0" )->find ();
			if (! $user_info) {
				get_404 ();
			}
			$user_follow_mod = M ( "User_follow" );
			$user_follow_info = $user_follow_mod->where ( "fans_id=$oUid and uid=" . $_COOKIE ['id'] )->find ();
			if ($user_follow_info) {
				$this->assign ( "followed", 1 );
			}
			$this->assign ( "oUid", $oUid );
			$this->assign ( "user_info", $user_info );
		} else {
			$this->check_login ();
			$id = $_COOKIE ['id'];
		}
		if ($_COOKIE ['id']) {
			$this->assign ( "cookieId", $_COOKIE ['id'] );
		}
		return $id;
	}
	public function index() {
		$this->assign ( "sty", array (
				'index',
				'style1',
				'usercenter' 
		) );
		$user_mod = M ( "User" );
		$this->items_paiHang ();
		$id = $this->others ();
		
		// 替换seo的值
		$uname = $user_mod->where ( "id=$id" )->getField ( 'name' );
		$seo ['title'] = $uname . "的首页_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$items_mod = M ( "Items" );
		$album_mod = M ( "Album" );
		$album_items_mod = M ( "Album_items" );
		// 首页显示的宝贝
		if ($id == $_COOKIE ['id']) {
			$index_share_where = "uid=$id and is_del=0";
		} else {
			$index_share_where = "uid=$id and status=1 and is_del=0";
		}
		$items_list = $items_mod->where ( $index_share_where )->field ( "id,title,add_time,img,comments,uid,likes,price" )->limit ( 10 )->order ( "add_time desc" )->select ();
		$count = $items_mod->where ( $index_share_where )->count ();
		$this->items_list ( $items_list );
		$this->assign ( "count", $count );
		// 首页显示的专辑
		if ($id == $_COOKIE ['id']) {
			$album_where = "ac.is_del=0 and " . C ( "db_prefix" ) . "album.uid=$id and " . C ( "db_prefix" ) . "album.is_del=0";
		} else {
			$album_where = "ac.is_del=0 and " . C ( "db_prefix" ) . "album.status=1 and " . C ( "db_prefix" ) . "album.uid=$id and " . C ( "db_prefix" ) . "album.is_del=0";
		}
		$join_ac = " " . C ( "db_prefix" ) . "album_cate as ac on " . C ( "db_prefix" ) . "album.cid=ac.id";
		
		$album_list = $album_mod->join ( $join_ac )->where ( $album_where )->field ( " " . C ( "db_prefix" ) . "album.id," . C ( "db_prefix" ) . "album.title," . C ( "db_prefix" ) . "album.add_time," . C ( "db_prefix" ) . "album.info" )->order ( " " . C ( "db_prefix" ) . "album.add_time desc" )->limit ( 3 )->select ();
		foreach ( $album_list as $key => $val ) {
			$album_id = $val ['id'];
			$album_items_list = $album_items_mod->where ( "pid=$album_id" )->select ();
			$items_id_arr = array ();
			foreach ( $album_items_list as $keys => $vals ) {
				$items_id_arr [] = $vals ['items_id'];
			}
			$items_id = implode ( ",", $items_id_arr );
			if (! $items_id) {
				$items_id = "-1";
			}
			// 专辑封面
			$album_items_cover = $album_items_mod->where ( "pid=$album_id and is_cover=1" )->find ();
			if ($album_items_cover) {
				$album_cover_item = $items_mod->where ( "id=" . $album_items_cover ['items_id'] . " and status=1 and is_del=0" )->field ( "id,img,title" )->find ();
			} else {
				$album_cover_item = $items_mod->where ( "id in ($items_id) and status=1 and is_del=0" )->field ( "id,img,title" )->order ( "add_time desc" )->find ();
			}
			$album_list [$key] ['cover'] = $album_cover_item;
			$items_lists = $items_mod->where ( "id in ($items_id) and status=1 and is_del=0 and id<>" . $album_cover_item ['id'] )->order ( "add_time desc" )->field ( "id,img,title" )->limit ( 8 )->select ();
			$album_list [$key] ["items"] = $items_lists;
			$items_count = $items_mod->where ( "id in ($items_id) and status=1 and is_del=0" )->count ();
			$album_list [$key] ["items_count"] = $items_count;
		}
		// dump($album_list);
		$aublm_count = $album_mod->join ( $join_ac )->where ( $album_where )->count ();
		$this->assign ( "aublm_count", $aublm_count );
		$this->assign ( "album_list", $album_list );
		$this->assign ( "curPage", "uc_index" );
		$this->display ();
	}
	
	// 显示注册页
	public function register() {
		if ($_COOKIE ['id']) {
			$url = C ( 'site_domain' );
			header ( "Location:" . $url );
		}
		$this->assign ( "sty", array (
				'index',
				'sign' 
		) );
		$seo ['title'] = "用户注册 - " . C ( "site_name" );
		$seo ['keys'] = C ( "site_name" ) . "注册，" . C ( "site_keyword" );
		$seo ['desc'] = C ( "site_name" ) . "注册，" . C ( "site_description" );
		$this->assign ( "seo", $seo );
		$this->display ();
	}
	public function registerAction() {
		// 注册新用户
		$user_mod = M ( "User" );
		$data ['name'] = $_POST ['uname'];
		$data ['passwd'] = md5 ( $_POST ['pwd'] );
		$data ['age'] = $_POST ['age'];
		$data ['email'] = $_POST ['email'];
		$data ['sex'] = $_POST ['sex'];
		$data ['add_time'] = time ();
		$data ['ip'] = getClientIp ();
		if (C ( 'home.uc_status' )) {
			$ucenter = $this->ucenter ();
			$ucresult = $ucenter->register ( $_POST ['uname'], $_POST ['pwd'], $_POST ['email'] );
			if (! $ucresult) {
				exit ();
			}
		}
		$result = $user_mod->add ( $data );
		if ($result) {
			echo "registerOK";
			setcookie ( 'id', $result, time () + 3600 * 24 * 7, '/' );
			setcookie ( 'name', $_POST ['uname'], time () + 3600 * 24 * 7, '/' );
		}
	}
	public function login() {
		session_start ();
		if ($_SESSION ['count']) {
			unset ( $_SESSION ['count'] );
		}
		$this->assign ( "sty", array (
				'index',
				'sign' 
		) );
		$pattern = '/' . $_SERVER ['SERVER_NAME'] . '/';
		preg_match ( $pattern, $_SERVER ['HTTP_REFERER'], $result );
		if ($_SERVER ['HTTP_REFERER'] != U ( 'Uc/register' ) && $result) {
			$this->assign ( 'referer', $_SERVER ['HTTP_REFERER'] );
		}
		$seo ['title'] = "用户登录 - " . C ( "site_name" );
		$seo ['keys'] = C ( "site_name" ) . "登录，" . C ( "site_keyword" );
		$seo ['desc'] = C ( "site_name" ) . "登录，" . C ( "site_description" );
		$this->assign ( "seo", $seo );
		$this->display ();
	}
	public function loginAction() {
		session_start ();
		$user_mod = M ( "User" );
		$name = $_POST ['uname'];
		$pwd = md5 ( $_POST ['pwd'] );
		$referer = $_POST ['referer'];
		if ($_SESSION ['count'] >= 2) {
			$this->ajaxReturn ( '', 'loginErrRe' );
		}
		$user_info = $user_mod->where ( "name='$name' and is_del=0 and passwd='$pwd'" )->find ();
		$cookie = $_POST ['cookie'];
		if (C ( 'home.uc_status' )) {
			$ucenter = $this->ucenter ();
			$ucresult = $ucenter->login ( $_POST ['uname'], $_POST ['pwd'] );
			$info = $ucenter->info ( $_POST ['uname'] );
			$data ['passwd'] = $pwd;
			$data ['email'] = $info [2];
			if ($ucresult != 'none' && $ucresult != 'error' && $user_info == null) {
				$data ['name'] = $name;
				$data ['add_time'] = time ();
				$data ['ip'] = getClientIp ();
				$user_mod->add ( $data );
			} elseif ($ucresult != 'none' && $ucresult != 'error' && $user_info) {
				$user_mod->where ( "name='$name' and is_del=0" )->save ( $data );
			}
			// preg_match_all('/http:\/\/[\w+\/]*\.php\?time=[\w+]*&code=[\w+|%]*/si',$ucresult,$result);
		}
		if ($user_info) {
			if ($_SESSION ['count']) {
				unset ( $_SESSION ['count'] );
			}
			if ($user_info ['is_sys'] == 1) {
				$this->ajaxReturn ( 'no' );
			} elseif ($user_info ['status'] == 0) {
				$this->ajaxReturn ( 'verify' );
			} else {
				$id = $user_mod->where ( "name='$name'" )->getField ( "id" );
				// 更新用户最后登录时间和最后登录的ip
				$user ['last_ip'] = getClientIp ();
				$user ['last_time'] = time ();
				$user_mod->where ( "id=$id and is_del=0" )->save ( $user );
				if ($cookie == true) {
					setcookie ( 'id', $user_info ['id'], time () + 3600 * 24 * 7, '/' );
					setcookie ( 'name', $user_info ['name'], time () + 3600 * 24 * 7, '/' );
				} else {
					setcookie ( 'id', $user_info ['id'], '/' );
					setcookie ( 'name', $user_info ['name'], '/' );
				}
				if (C ( 'home.uc_status' ) && $ucresult == 'error') {
				}
				if (C ( 'home.uc_status' ) && $ucresult == 'none') {
					$ucenter->register ( $_POST ['uname'], $_POST ['pwd'], $user_info ['email'] );
				}
				if (C ( 'home.uc_status' ) && $ucresult != 'none' && $ucresult != 'error') {
					if ($referer) {
						$this->ajaxReturn ( $referer, 're', $ucresult );
					} else {
						$this->ajaxReturn ( 'yes', '', $ucresult );
					}
				} else {
					if ($referer) {
						$this->ajaxReturn ( $referer, 're' );
					} else {
						$this->ajaxReturn ( 'yes' );
					}
				}
			}
		} else {
			$_SESSION ['count'] ++;
			$this->ajaxReturn ( '' );
		}
	}
	public function logout() {
		if (C ( 'home.uc_status' )) {
			$ucenter = $this->ucenter ();
			$ucresult = $ucenter->logout ();
		}
		setcookie ( 'id', null, time () - 1, '/' );
		setcookie ( 'name', null, time () - 1, '/' );
		$url = C ( 'site_domain' );
		if (C ( 'home.uc_status' )) {
			redirect($url,1,$ucresult);
		} else {
			redirect($url);
		}
	}
	public function account() {
		$this->assign ( "sty", array (
				'index',
				'usercenter' 
		) );
		$this->check_login ();
		$user_mod = M ( "User" );
		$id = $_COOKIE ['id'];
		$userInfo = $user_mod->where ( "id=$id and is_del=0" )->find ();
		$address = $userInfo ['address'];
		if ($address) {
			$adrArr = explode ( "|", $address );
			if (count ( $adrArr ) == 2) {
				$province = $adrArr [0];
				$city = $adrArr [1];
				$county = "请选择";
			} elseif (count ( $adrArr ) == 3) {
				$province = $adrArr [0];
				$city = $adrArr [1];
				$county = $adrArr [2];
			} else {
				$province = $address;
				$city = "请选择";
				$county = "请选择";
			}
		} else {
			$province = "请选择";
			$city = "请选择";
			$county = "请选择";
		}
		// 替换seo的值
		$seo ['title'] = $userInfo ['name'] . "基本信息_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		
		$this->assign ( "province", $province );
		$this->assign ( "city", $city );
		$this->assign ( "county", $county );
		$this->display ();
	}
	public function accountAction() {
		$user_mod = M ( "User" );
		$data ['name'] = $_POST ["name"];
		$data ['age'] = $_POST ["age"];
		$data ['sex'] = $_POST ["sex"];
		$data ['info'] = $_POST ["info"];
		$province = ($_POST ['province'] == "请选择") ? '' : $_POST ['province'];
		$city = ($_POST ['city'] == "请选择") ? '' : "|" . $_POST ['city'];
		;
		$county = ($_POST ['county'] == "请选择") ? '' : "|" . $_POST ['county'];
		;
		$data ['address'] = $province . $city . $county;
		
		$name = $_POST ["name"];
		$id = $_POST ["id"];
		$user_info = $user_mod->where ( "name='$name' and is_del=0" )->find ();
		$oldUname = $user_mod->where ( "id=$id and is_del=0" )->getField ( "name" );
		if ($oldUname == $_POST ["name"]) {
			if ($user_mod->where ( "id=$id and is_del=0" )->save ( $data )) {
				echo "success";
			}
		} elseif ($user_info) {
			echo "unameRepeat";
		} else {
			if ($user_mod->where ( "id=$id and is_del=0" )->save ( $data )) {
				echo "success";
				setcookie ( 'name', $_POST ["name"], time () + 3600 * 24 * 7, '/' );
			}
		}
		
		/*
		 * else{ }
		 */
	}
	public function pwd() {
		$this->assign ( "sty", array (
				'index',
				'usercenter' 
		) );
		// 判断是否登录
		$this->check_login ();
		// 替换seo的值
		$user_mod = M ( "User" );
		$id = $_COOKIE ['id'];
		$userInfo = $user_mod->where ( "id=$id and is_del=0" )->find ();
		$seo ['title'] = $userInfo ['name'] . "修改密码_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$this->display ();
	}
	public function pwdAction() {
		$user_mod = M ( "User" );
		$id = $_COOKIE ['id'];
		$oldPwd = md5 ( $_POST ['oldPwd'] );
		$newPwd = md5 ( $_POST ['newPwd'] );
		$newPwdSure = md5 ( $_POST ['newPwdSure'] );
		$uname = $user_mod->where ( "id=$id and is_del=0" )->getField ( 'name' );
		$pwd = $user_mod->where ( "id=$id and is_del=0" )->getField ( 'passwd' );
		if ($oldPwd != $pwd) {
			echo "oldPwdError";
		} else {
			$newPwdbool = preg_match ( "/^[0-9a-zA-Z]{6,16}$/", $_POST ['newPwd'] );
			if ($newPwdbool) {
				if ($newPwd != $newPwdSure) {
					echo "differ";
				} else {
					if (C ( 'home.uc_status' )) {
						$ucenter = $this->ucenter ();
						$ucresult = $ucenter->pwd ( $uname, $_POST ['oldPwd'], $_POST ['newPwd'] );
						if (! $ucresult) {
							echo "oldPwdError";
							exit ();
						}
					}
					if ($user_mod->where ( "id=$id and is_del=0" )->setField ( 'passwd', $newPwd )) {
						echo "success";
					}
				}
			} else {
				echo "patternError";
			}
		}
	}
	public function img() {
		session_start ();
		$this->assign ( "sty", array (
				'index',
				'usercenter' 
		) );
		// 判断是否登录
		$user_mod = M ( "User" );
		$this->check_login ();
		$id = $_COOKIE ['id'];
		$user_info = $user_mod->where ( "id=$id and is_del=0" )->find ();
		// 替换seo的值
		$seo ['title'] = $user_info ['name'] . "修改头像_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$this->assign ( "id", $id );
		$this->assign ( "user_info", $user_info );
		$user_img = $user_info ['img'];
		if ($user_img) {
			$this->assign ( "user_img", $user_img );
			$_SESSION ['user_img'] = $user_img;
		}
		$this->display ();
	}
	public function upload() {
		@header ( "Expires: 0" );
		@header ( "Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE );
		@header ( "Pragma: no-cache" );
		define ( 'SD_ROOT', dirname ( __FILE__ ) . '/' );
		$pic_id = time (); // 使用时间来模拟图片的ID.
		$pic_path = SD_ROOT . '../../../../Uploads/avatar_original/' . $pic_id . '.jpg';
		$pic_abs_path = C ( 'web_path' ) . 'Uploads/avatar_original/' . $pic_id . '.jpg';
		
		if (empty ( $_FILES ['Filedata'] )) {
			echo '<script type="text/javascript">alert("对不起, 图片未上传成功, 请再试一下");</script>';
			exit ();
		}
		if ($_FILES ['Filedata'] ['size'] > 2097152) {
			echo '<script type="text/javascript">alert("请上传小于2MB的文件!!");window.parent.hideLoading();</script>';
			return false;
		}
		
		$file = @$_FILES ['Filedata'] ['tmp_name'];
		file_exists ( $pic_path ) && @unlink ( $pic_path );
		if (copy ( $_FILES ['Filedata'] ['tmp_name'], $pic_path ) || move_uploaded_file ( $_FILES ['Filedata'] ['tmp_name'], $pic_path )) {
			@unlink ( $_FILES ['Filedata'] ['tmp_name'] );
		} else {
			@unlink ( $_FILES ['Filedata'] ['tmp_name'] );
			echo '<script type="text/javascript">alert("对不起, 上传失败!!' . $pic_path . '");window.parent.hideLoading();</script>';
			return false;
		}
		
		// 写新上传照片的ID.
		echo '<script type="text/javascript">window.parent.hideLoading();window.parent.buildAvatarEditor("' . $pic_id . '","' . $pic_abs_path . '","photo");</script>';
	}
	public function saveAvatar() {
		session_start ();
		define ( 'SD_ROOT', dirname ( __FILE__ ) . '/' );
		@header ( "Expires: 0" );
		@header ( "Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE );
		@header ( "Pragma: no-cache" );
		
		// 这里传过来会有两种类型，一先一后, big和small, 保存成功后返回一个json字串，客户端会再次post下一个.
		$type = isset ( $_GET ['type'] ) ? trim ( $_GET ['type'] ) : 'tupian';
		$orgin_pic_path = $_GET ['photoServer']; // 原始图片地址，备用.
		                                         // $from = $_GET['from'];
		                                         // //原始图片地址，备用.
		$_path = explode ( '/', $orgin_pic_path );
		$num = count ( $_path );
		$path = '/';
		foreach ( $_path as $k => $v ) {
			if (($k + 1) == $num) {
				$filename = $v;
			} else {
				$path .= $v . '/';
			}
		}
		if ($type == 'big') {
			$pic_path = '../../../../Uploads/avatar_big/' . $filename;
		} elseif ($type == 'small') {
			$pic_path = '../../../../Uploads/avatar_small/' . $filename;
		} else {
			$msg = json_encode ( 'error img!' );
			echo $msg;
			exit ();
		}
		$new_avatar_path = $pic_path;
		$len = file_put_contents ( SD_ROOT . $new_avatar_path, file_get_contents ( "php://input" ) );
		$avtar_img = imagecreatefromjpeg ( SD_ROOT . $new_avatar_path );
		imagejpeg ( $avtar_img, SD_ROOT . $new_avatar_path, 80 );
		
		// 输出新保存的图片位置, 测试时注意改一下域名路径, 后面的statusText是成功提示信息.
		// status 为1 是成功上传，否则为失败.
		$d = new pic_data ();
		// $d->data->urls[0] = 'http://sns.com/avatar_test/'.$new_avatar_path;
		$d->data->urls [0] = $new_avatar_path;
		$d->status = 1;
		$d->statusText = '上传成功!';
		
		$msg = json_encode ( $d );
		
		echo $msg;
		$user_mod = M ( "User" );
		$user_mod->where ( "is_del=0 and id=" . $_COOKIE ['id'] )->setField ( 'img', $filename );
		
		@unlink ( SD_ROOT . "../../../../Uploads/avatar_original/" . $_SESSION ['user_img'] );
		@unlink ( SD_ROOT . "../../../../Uploads/avatar_big/" . $_SESSION ['user_img'] );
		@unlink ( SD_ROOT . "../../../../Uploads/avatar_small/" . $_SESSION ['user_img'] );
	}
	public function sns() {
		$this->assign ( "sty", array (
				'index',
				'usercenter' 
		) );
		$this->check_login ();
		// 替换seo的值
		$user_mod = M ( "User" );
		$id = $_COOKIE ['id'];
		$userInfo = $user_mod->where ( "id=$id" )->find ();
		$seo ['title'] = $userInfo ['name'] . "账号绑定_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$user_openid_mod = M ( "user_openid" );
		$is_qq_bind = $user_openid_mod->where ( "uid=" . $id . " and type='qq'" )->find ();
		$is_sina_bind = $user_openid_mod->where ( "uid=" . $id . " and type='sina'" )->find ();
		$this->assign ( "is_qq_bind", $is_qq_bind );
		$this->assign ( "is_sina_bind", $is_sina_bind );
		$this->display ();
	}
	// 创建专辑
	public function albumInfo() {
		$this->assign ( "sty", array (
				'index',
				'usercenter' 
		) );
		$aid = $_GET ['id']; // 获取专辑id
		$this->check_login ();
		// 替换seo的值
		$user_mod = M ( "User" );
		$id = $_COOKIE ['id'];
		$userInfo = $user_mod->where ( "id=$id" )->find ();
		$seo ['title'] = $userInfo ['name'] . "创建新专辑_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$action = $_GET ['action'];
		$album_mod = M ( "Album" );
		$album_cate_mod = M ( "Album_cate" );
		$album_cate = $album_cate_mod->order ( 'ord desc' )->where ( "is_del=0" )->select ();
		$this->assign ( "album_cate", $album_cate );
		if ($aid) {
			$album_info = $album_mod->where ( "id=$aid and is_del=0" )->find ();
			if ($album_info) {
				$album_info1 = $album_mod->where ( "id=$aid and is_del=0 and uid=" . $_COOKIE ['id'] )->find ();
				if ($album_info1) {
					$album_cate_id = $album_cate_mod->where ( "id=" . $album_info ['cid'] )->getField ( 'id' );
					$this->assign ( "album_cate_id", $album_cate_id );
					$this->assign ( "album_title", $album_info ['title'] );
					$this->assign ( "aid", $album_info ['id'] );
					$this->assign ( "info", $album_info ['info'] );
				} else {
					$url = C ( 'site_domain' );
					header ( 'location:' . $url );
				}
			} else {
				get_404 ();
			}
		}
		$this->display ();
	}
	public function albumInfoAction() {
		$album_cate_mod = M ( "Album_cate" );
		$album_mod = M ( "Album" );
		$album ['title'] = $_POST ['albumTitle'];
		$album ['info'] = $_POST ['albumInfo'];
		$album ['cid'] = $_POST ['albumCateId'];
		$album ['add_time'] = time ();
		$album ['uid'] = $_COOKIE ['id'];
		$hiddenaid = $_POST ['hiddenaid'];
		$title = $_POST ['albumTitle'];
		$album_info = $album_mod->where ( "title='$title' and is_del=0" )->find ();
		$oldAlbumTitle = $album_mod->where ( "id=$hiddenaid and is_del=0" )->getField ( "title" );
		if ($hiddenaid) {
			$aUid = $album_mod->where ( "id=$hiddenaid and is_del=0" )->getField ( 'uid' );
			if ($aUid == $_COOKIE ['id']) {
				if ($oldAlbumTitle == $_POST ['albumTitle']) {
					if ($album_mod->where ( "id=$hiddenaid and is_del=0 and uid=" . $_COOKIE ['id'] )->save ( $album )) {
						echo "successSave";
					}
				} elseif ($album_info) {
					echo "titleRepeat";
				} else {
					if ($album_mod->where ( "id=$hiddenaid and is_del=0 and uid=" . $_COOKIE ['id'] )->save ( $album )) {
						echo "successSave";
					}
				}
			} else {
				echo "noAccess";
			}
		} else {
			if ($album_info) {
				echo "titleRepeat";
			} elseif ($album_mod->add ( $album )) {
				echo "successcreate";
			}
		}
	}
	public function albumDel() {
		$album_mod = M ( "Album" );
		$album_items_mod = D ( 'Album_items' );
		$uid = $_COOKIE ['id'];
		$aid = $_GET ['id'];
		$del = $album_mod->where ( "id=$aid and uid=$uid" )->setField ( "is_del", 1 );
		if ($del) {
			$album_items_mod->where ( "pid=$aid" )->delete ();
			$url = get_url ( 'album', '', 'user' );
			header ( 'location:' . $url );
		}
	}
	public function albumDetail() {
		$album_mod = M ( "Album" );
		$this->assign ( "sty", array (
				'index',
				'style1',
				'usercenter' 
		) );
		$user_mod = M ( "User" );
		$this->items_paiHang ();
		$id = $_GET ['id'];
		$album_uid = $album_mod->where ( "id=$id and is_del=0" )->getField ( "uid" );
		
		$join_ac = " " . C ( "db_prefix" ) . "album_cate as ac on " . C ( "db_prefix" ) . "album.cid=ac.id";
		if ($album_uid == $_COOKIE ['id']) {
			$where = "ac.is_del=0 and " . C ( "db_prefix" ) . "album.id=$id and " . C ( "db_prefix" ) . "album.is_del=0";
		} else {
			$where = "ac.is_del=0 and " . C ( "db_prefix" ) . "album.status=1 and " . C ( "db_prefix" ) . "album.id=$id and " . C ( "db_prefix" ) . "album.is_del=0";
		}
		$album_info = $album_mod->join ( $join_ac )->field ( " " . C ( "db_prefix" ) . "album.id," . C ( "db_prefix" ) . "album.title" )->where ( $where )->find ();
		if ($album_info) {
			$this->assign ( "album_info", $album_info );
		} else {
			get_404 ();
		}
		if ($album_uid != $_COOKIE ['id']) {
			$user_info = $user_mod->where ( "id=$album_uid" )->find ();
			
			$this->assign ( "oUid", $album_uid );
			$this->assign ( "user_info", $user_info );
			$this->album_items_list ();
		} else {
			$this->check_login ();
			$user_info = $user_mod->where ( "id=" . $_COOKIE ['id'] )->find ();
			$this->album_items_list ();
		}
		// 替换seo的值
		$seo ['title'] = "专辑《" . $album_info ['title'] . "》_" . $user_info ['name'] . "的专辑_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$this->assign ( "cookieId", $_COOKIE ['id'] );
		$this->assign ( "curPage", "uc_album" );
		$this->display ();
	}
	
	// 会员专辑页面
	public function album() {
		$this->assign ( "sty", array (
				'index',
				'style1',
				'usercenter' 
		) );
		$this->items_paiHang ();
		$user_mod = M ( "User" );
		$id = $this->others ();
		// 替换seo的值
		$uname = $user_mod->where ( "id=$id" )->getField ( 'name' );
		$seo ['title'] = $uname . "的专辑_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$album_mod = M ( "Album" );
		import ( "ORG.Util.Page" );
		if ($id == $_COOKIE ['id']) {
			$album_where = "ac.is_del=0 and " . C ( "db_prefix" ) . "album.uid=$id and " . C ( "db_prefix" ) . "album.is_del=0";
		} else {
			$album_where = "ac.is_del=0 and " . C ( "db_prefix" ) . "album.status=1 and " . C ( "db_prefix" ) . "album.uid=$id and " . C ( "db_prefix" ) . "album.is_del=0";
		}
		$join_ac = " " . C ( "db_prefix" ) . "album_cate as ac on " . C ( "db_prefix" ) . "album.cid=ac.id";
		$count = $album_mod->join ( $join_ac )->where ( $album_where )->count ();
		$Page = new Page ( $count, 10 );
		$show = $Page->show ();
		$album_list = $album_mod->join ( $join_ac )->where ( $album_where )->field ( " " . C ( "db_prefix" ) . "album.id," . C ( "db_prefix" ) . "album.title," . C ( "db_prefix" ) . "album.add_time," . C ( "db_prefix" ) . "album.info" )->order ( " " . C ( "db_prefix" ) . "album.add_time desc" )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
		$this->assign ( "count", $count );
		$this->album_list ( $album_list );
		$this->assign ( 'page', $show );
		$this->assign ( "curPage", "uc_album" );
		$this->display ();
	}
	public function like() {
		$this->assign ( "sty", array (
				'index',
				'style1',
				'usercenter' 
		) );
		$this->items_paiHang ();
		$user_mod = M ( "User" );
		$id = $this->others ();
		// 替换seo的值
		$uname = $user_mod->where ( "id=$id" )->getField ( 'name' );
		$seo ['title'] = $uname . "的喜欢_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$items_mod = M ( "Items" );
		$items_likes_mod = M ( "Items_likes" );
		$items_tags_item_mod = M ( 'Items_tags_item' );
		$items_tags_mod = M ( "Items_tags" );
		$re = $items_likes_mod->where ( "uid=$id" )->select ();
		$ids = array ();
		foreach ( $re as $val ) {
			$ids [] = $val ['items_id'];
		}
		if ($ids) {
			$items_id_str = implode ( ",", $ids );
		} else {
			$items_id_str = - 1;
		}
		
		$field = "id,title,add_time,img,comments,uid,likes,price";
		import ( "ORG.Util.Page" );
		$count = $items_mod->where ( "id in ($items_id_str) and status=1 and is_del=0" )->count ();
		$Page = new Page ( $count, 10 );
		$show = $Page->show ();
		$items_list = $items_mod->where ( "id in ($items_id_str) and status=1 and is_del=0" )->order ( "add_time desc" )->field ( $field )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
		foreach ( $items_list as $keys => $vals ) {
			$item_id = $vals ['id'];
			$items_tags_item = $items_tags_item_mod->where ( "item_id=$item_id" )->select ();
			$tag_id_arr = array ();
			foreach ( $items_tags_item as $valT ) {
				$tag_id_arr [] = $valT ['tag_id'];
			}
			if ($tag_id_arr) {
				$sql_tid = implode ( ",", $tag_id_arr );
			} else {
				$sql_tid = - 1;
			}
			$tags = $items_tags_mod->where ( "id IN ($sql_tid) and is_del=0" )->limit ( 4 )->select ();
			$items_list [$keys] ['tags'] = $tags;
		}
		$this->items_list ( $items_list );
		$this->assign ( "count", $count );
		$this->assign ( 'page', $show );
		$this->assign ( "curPage", "uc_like" );
		$this->display ();
	}
	public function createlike() {
		$uid = $_COOKIE ['id'];
		$items_id = isset ( $_POST ['items_id'] ) ? intval ( $_POST ['items_id'] ) : 0;
		$val = $_POST ['val'];
		if ($uid) {
			if ($items_id) {
				$data ['items_id'] = $items_id;
				$data ['uid'] = $uid;
				$items_likes_mod = M ( "Items_likes" );
				$items_mod = M ( "Items" );
				$user_mod = M ( 'User' );
				$result = $items_likes_mod->where ( "items_id=" . $items_id . " and uid=" . $uid )->find ();
				if ($result) {
					if ($val == '喜欢一下' || $val == '喜欢') {
						$item = $items_mod->field ( 'likes' )->where ( "id=$items_id" )->find ();
						$this->ajaxReturn ( $item ['likes'], '', 1 );
					}
					$items_likes_mod->where ( "items_id=" . $items_id . " and uid=" . $uid )->delete ();
					$items_mod->where ( "id=$items_id" )->setDec ( 'likes', 1 );
					$user_mod->where ( "id=$uid" )->setDec ( 'likes_num', 1 );
					$item = $items_mod->field ( 'likes' )->where ( "id=$items_id" )->find ();
					$this->ajaxReturn ( $item ['likes'], '', - 1 );
				} else {
					if ($items_likes_mod->add ( $data )) {
						$items_mod->where ( "id=$items_id" )->setInc ( 'likes', 1 );
						$user_mod->where ( "id=$uid" )->setInc ( 'likes_num', 1 );
						$item = $items_mod->field ( 'likes' )->where ( "id=$items_id" )->find ();
						$this->ajaxReturn ( $item ['likes'], '', 1 );
					} else {
						$this->ajaxReturn ( 0, "添加喜欢有误，请重试", 0 );
					}
				}
			} else {
				$this->ajaxReturn ( 0, "单品有误", 0 );
			}
		} else {
			$this->ajaxReturn ( - 2, "未登录", - 2 );
		}
	}
	public function albumItemsDel() {
		C ( 'TOKEN_ON', false );
		$items_id = $_GET ['id'];
		$aid = $_GET ['aid'];
		$items_mod = M ( "Items" );
		$album_mod = M ( "Album" );
		$album_items_mod = M ( "Album_items" );
		$uid = $album_mod->where ( "id=$aid and is_del=0" )->getField ( "uid" );
		if ($uid == $_COOKIE ['id']) {
			$del = $album_items_mod->where ( "pid=$aid and items_id=$items_id" )->delete ();
			if ($del) {
				$url = get_url ( 'albumDetail', $aid, 'user' );
				header ( 'location:' . $url );
			}
		}
	}
	public function likeItemsDel() {
		$items_mod = M ( "Items" );
		$user_mod = M ( "User" );
		$items_id = $_GET ['id'];
		$uid = $_COOKIE ['id'];
		$items_likes_mod = M ( "Items_likes" );
		$del = $items_likes_mod->where ( "items_id=$items_id and uid=$uid" )->delete ();
		if ($del) {
			$items_mod->where ( "id=$items_id" )->setDec ( 'likes', 1 ); // 商品的喜欢数减1
			$user_mod->where ( "id=$uid" )->setDec ( 'likes_num', 1 );
			$url = get_url ( 'like', '', 'user' );
			header ( 'location:' . $url );
		}
	}
	public function share() {
		$this->assign ( "sty", array (
				'index',
				'style1',
				'usercenter' 
		) );
		$this->items_paiHang ();
		$user_mod = M ( "User" );
		$id = $this->others ();
		// 替换seo的值
		if ($id == $_COOKIE ['id']) {
			$share_where = "uid=$id and is_del=0";
		} else {
			$share_where = "uid=$id and status=1 and is_del=0";
		}
		$uname = $user_mod->where ( "id=$id" )->getField ( 'name' );
		$seo ['title'] = $uname . "的分享_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		$items_mod = M ( 'Items' );
		import ( "ORG.Util.Page" ); // 导入分页类
		$count = $items_mod->where ( $share_where )->count (); // 查询满足要求的总记录数
		$Page = new Page ( $count, 40 ); // 实例化分页类传入总记录数和每页显示的记录数
		$show = $Page->show (); // 分页显示输出
		$items_list = $items_mod->where ( $share_where )->field ( "id,title,add_time,img,comments,uid,likes,price" )->order ( "add_time desc" )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
		$this->assign ( 'page', $show ); // 赋值分页输出
		$this->items_list ( $items_list );
		$this->assign ( "count", $count );
		$this->assign ( "curPage", "uc_share" );
		
		$this->display ();
	}
	function sinalogin() {
		vendor ( 'oAuth.sina' );
		$type = isset ( $_REQUEST ['type'] ) ? $_REQUEST ['type'] : 'callback';
		$domain = 'http://' . $_SERVER ['HTTP_HOST'];
		$redirect_uri = $domain . get_url ( 'sina' . $type, '', 'user' );
		$o = new SaeTOAuthV2 ( c ( 'sina_appkey' ), c ( 'sina_appsecret' ) );
		$login_url = $o->getAuthorizeURL ( $redirect_uri );
		
		header ( "Location:$login_url" );
	}
	function sinacallback() {
		vendor ( 'oAuth.sina' );
		$o = new SaeTOAuthV2 ( c ( 'sina_appkey' ), c ( 'sina_appsecret' ) );
		
		//
		if (isset ( $_REQUEST ['code'] )) {
			$keys = array ();
			$keys ['code'] = $_REQUEST ['code'];
			$keys ['redirect_uri'] = c ( 'site_domain' ) . get_url ( 'sinacallback', '', 'user' );
			
			try {
				$token = $o->getAccessToken ( 'code', $keys );
			} catch ( OAuthException $e ) {
			}
		}
		
		$c = new SaeTClientV2 ( c ( 'sina_appkey' ), c ( 'sina_appsecret' ), $token ['access_token'], '' );
		// 如果token空，结束。
		if (! $token) {
			$this->error ( '登录失败！', get_url('login','','user') );
			exit ();
		}
		// 存入session
		$_SESSION ['token'] = $token;
		$_SESSION ['openid'] = $token ['uid'];
		$_SESSION ['access_token'] = $token ['access_token'];
		$user_mod = M ( 'User' );
		$user_openid_mod = M ( "User_openid" );
		// 查找openid
		$condition = array (
				"openid" => $token ['uid'],
				"type" => "sina" 
		);
		
		$user_openid = $user_openid_mod->where ( $condition )->find ();
		$logedin = false;
		
		// 判断是否登录
		$uid = $_COOKIE ['id'];
		if ($uid) {
			$user_info = $user_mod->where ( "id=$uid" )->find ();
		}
		if ($uid && ! empty ( $user_info )) {
			$logedin = true;
		}
		
		if ($user_openid) {
			// 已绑定
			if ($logedin) {
				if (! $user_openid ['uid'] == $uid) {
					$this->error ( "该微博账号已经绑定其他站内账号，请绑定其他微博，或直接使用微博登陆。", get_url ( 'sns', '', 'user' ) );
				}
			} else {
				
				setcookie ( 'login_type', 'sina', time () + 3600 * 24 * 7, '/' );
				setcookie ( 'id', $user_openid ['uid'], time () + 3600 * 24 * 7, '/' );
				$user_info = $user_mod->where ( "id='" . $user_openid ['uid'] . "'" . " and is_del=0" )->find ();
				setcookie ( 'name', $user_info ['name'], time () + 3600 * 24 * 7, '/' );
				
				// 更新用户最后登录时间和最后登录的ip
				$user ['last_ip'] = getClientIp ();
				$user ['last_time'] = time ();
				$user_mod->where ( "id='" . $user_openid ['uid'] . "'" )->save ( $user );
				header ( 'Location:' . get_url ( 'index', '', 'user' ) );
			}
		} else {
			// 未绑定，执行绑定
			$ret = $c->show_user_by_id ( $token ['uid'] );
			if ($ret ['error_code'] == 21321) {
				$this->error ( '新浪微博网站接入操作超时，无法获取该账户资料！请重试' );
				exit ();
			}
			$sina_info = array (
					'user_info' => $ret 
			);
			$data = array (
					'type' => 'sina',
					'openid_username' => $ret ['screen_name'],
					'info' => serialize ( $sina_info ) 
			)
			;
			
			$_SESSION ['user_openid_info'] = $data;
			
			if ($logedin) {
				$data = array (
						'type' => $_SESSION ['user_openid_info'] ['type'],
						'uid' => $uid,
						'openid' => $_SESSION ['openid'],
						'info' => $_SESSION ['user_openid_info'] ['info'] 
				);
				$user_openid_mod->add ( $data );
				$this->success ( "绑定成功！", get_url ( 'sns', '', 'user' ) );
			} else {
				
				header ( 'Location:' . get_url ( 'sign', '', 'user' ) );
			}
		}
	}
	function qqlogin() {
		$type = isset ( $_REQUEST ['type'] ) ? $_REQUEST ['type'] : 'callback';
		$_SESSION ['state'] = md5 ( uniqid ( rand (), TRUE ) );
		$domain = 'http://' . $_SERVER ['HTTP_HOST'];
		$redirect_uri = $domain . get_url ( 'qq' . $type, '', 'user' );
		
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" . c ( 'qq_appkey' ) . "&redirect_uri=" . urlencode ( $redirect_uri ) . "&state=" . $_SESSION ['state'];
		
		header ( "Location:$login_url" );
	}
	function qqcallback() {
		if ($_REQUEST ['state'] == $_SESSION ['state']) { // csrf
			$token_url = "https://graph.qq.com/oauth2.0/token";
			$aGetParam = array (
					"grant_type" => "authorization_code",
					"client_id" => c ( 'qq_appkey' ),
					"client_secret" => c ( 'qq_appSecret' ),
					"code" => $_REQUEST ["code"],
					"redirect_uri" => c ( 'site_domain' ) . get_url ( 'qqcallback', '', 'user' ) 
			);
			
			$res = post ( $token_url, $aGetParam );
			
			if (trim ( $res ) == '') {
				header ( 'Content-Type: text/html; charset=UTF-8' );
				$this->error ( '无法获取认证！', get_url('login','','user') );
				eixt;
			}
			if (strpos ( $res, "callback" ) !== false) {
				$lpos = strpos ( $res, "(" );
				$rpos = strrpos ( $res, ")" );
				$res = substr ( $res, $lpos + 1, $rpos - $lpos - 1 );
				$msg = json_decode ( $res );
				if (isset ( $msg->error )) {
					$this->error ( 'QQ接入操作超时，无法获取该账户资料！请重试。' );
					// echo "<h3>error:</h3>" . $msg->error;
					// echo "<h3>msg :</h3>" . $msg->error_description;
					exit ();
				}
			}
			parse_str ( $res, $res );
			$_SESSION ["access_token"] = $res ['access_token'];
		}
		$url = "https://graph.qq.com/oauth2.0/me";
		
		$str = post ( $url, array (
				'access_token' => $_SESSION ['access_token'] 
		) );
		
		if (strpos ( $str, "callback" ) !== false) {
			$lpos = strpos ( $str, "(" );
			$rpos = strrpos ( $str, ")" );
			$str = substr ( $str, $lpos + 1, $rpos - $lpos - 1 );
		}
		$res = json_decode ( $str );
		
		$_SESSION ['openid'] = $res->openid;
		
		$user_mod = M ( 'User' );
		$user_openid_mod = M ( "User_openid" );
		// 查找openid
		$condition = array (
				"openid" => $res->openid,
				"type" => "qq" 
		);
		$user_openid = $user_openid_mod->where ( $condition )->find ();
		
		// 判断是否登录
		$uid = $_COOKIE ['id'];
		if ($uid) {
			$user_info = $user_mod->where ( "id=$uid" )->find ();
		}
		if ($uid && ! empty ( $user_info )) {
			$logedin = true;
		}
		
		if ($user_openid) {
			// 已绑定
			if ($logedin) {
				if (! $user_openid ['uid'] == $uid) {
					$this->error ( "该QQ账号已经绑定其他站内账号，请绑定其他QQ，或直接使用QQ号码登陆。", get_url ( 'sns', '', 'user' ) );
				}
			} else {
				
				setcookie ( 'login_type', 'qq', time () + 3600 * 24 * 7, '/' );
				setcookie ( 'id', $user_openid ['uid'], time () + 3600 * 24 * 7, '/' );
				$user_info = $user_mod->where ( "id='" . $user_openid ['uid'] . "'" . " and is_del=0" )->find ();
				setcookie ( 'name', $user_info ['name'], time () + 3600 * 24 * 7, '/' );
				// 更新用户最后登录时间和最后登录的ip
				$user ['last_ip'] = getClientIp ();
				$user ['last_time'] = time ();
				$user_mod->where ( "id='" . $user_openid ['uid'] . "'" )->save ( $user );
				header ( 'Location:' . get_url ( 'index', '', 'user' ) );
			}
		} else {
			// 未绑定，执行绑定
			$url = "https://graph.qq.com/user/get_user_info?" . "access_token=" . $_SESSION ['access_token'] . "&openid=" . $_SESSION ['openid'] . "&oauth_consumer_key=" . c ( 'qq_appkey' ) . "&format=json";
			$url = "https://graph.qq.com/user/get_user_info";
			$param = array (
					'access_token' => $_SESSION ['access_token'],
					"openid" => $_SESSION ['openid'],
					"oauth_consumer_key" => c ( 'qq_appkey' ),
					"format" => 'json' 
			);
			
			$res = post ( $url, $param );
			
			if ($res == false) {
				$this->error ( '获取用户信息失败！' );
				exit ();
			}
			
			$res = json_decode ( $res );
			$qq_info = array (
					'user_info' => $res 
			);
			
			$data = array (
					'type' => 'qq',
					'openid_username' => $res->nickname,
					'info' => serialize ( $qq_info ) 
			);
			
			$_SESSION ['user_openid_info'] = $data;
			if ($logedin) {
				$data = array (
						'type' => $_SESSION ['user_openid_info'] ['type'],
						'uid' => $uid,
						'openid' => $_SESSION ['openid'],
						'info' => $_SESSION ['user_openid_info'] ['info'] 
				);
				$user_openid_mod->add ( $data );
				$this->success ( "绑定成功！", get_url ( 'sns', '', 'user' ) );
			} else {
				
				header ( 'Location:' . get_url ( 'sign', '', 'user' ) );
			}
		}
	}
	function taobaologin() {
		$_SESSION ['state'] = md5 ( uniqid ( rand (), TRUE ) );
		
		$domain = 'http://' . $_SERVER ['HTTP_HOST'];
		$redirect_uri = $domain . get_url ( 'taobaocallback', '', 'user' );
		$login_url = "https://oauth.taobao.com/authorize?&response_type=code&client_id=" . c ( 'taobao_appkey' ) . "&redirect_uri=" . urlencode ( $redirect_uri ) . "&state=" . $_SESSION ['state'];
		header ( "Location:$login_url" );
	}
	function taobaocallback() {
		if ($_REQUEST ['state'] == $_SESSION ['state']) { // csrf
			$token_url = "https://oauth.taobao.com/token";
			$domain = 'http://' . $_SERVER ['HTTP_HOST'];
			$redirect_uri = $domain . get_url ( 'taobaocallback', '', 'user' );
			$param = array (
					"grant_type" => "authorization_code",
					"client_id" => c ( 'taobao_appkey' ),
					"client_secret" => c ( 'taobao_appSecret' ),
					"code" => $_REQUEST ["code"],
					"redirect_uri" => $redirect_uri 
			);
			
			$res = post ( $token_url, $param );
			if (trim ( $res ) == '') {
				header ( 'Content-Type: text/html; charset=UTF-8' );
				$this->error ( '无法获取认证！', get_url('login','','user') );
				exit ();
			}
			
			$res = json_decode ( $res, true );
			$_SESSION ["access_token"] = $res ['access_token'];
		}
		
		// 如果token空，结束。
		if (! isset ( $res ['access_token'] )) {
			$this->error ( '登录失败！' );
			exit ();
		}
		
		// 存入session
		$_SESSION ['token'] = $res;
		$_SESSION ['openid'] = $res ['taobao_user_id'];
		$_SESSION ['access_token'] = $res ['access_token'];
		$user_mod = M ( 'User' );
		$user_openid_mod = M ( "User_openid" );
		// 查找openid
		$condition = array (
				"openid" => $res ['taobao_user_id'],
				"type" => "taobao" 
		);
		
		$user_openid = $user_openid_mod->where ( $condition )->find ();
		$logedin = false;
		
		// 判断是否登录
		$uid = $_COOKIE ['id'];
		if ($uid) {
			$user_info = $user_mod->where ( "id=$uid" )->find ();
		}
		if ($uid && ! empty ( $user_info )) {
			$logedin = true;
		}
		if ($user_openid) {
			// 已绑定
			if ($logedin) {
				if (! $user_openid ['uid'] == $uid) {
					$this->error ( "该淘宝账号已经绑定其他站内账号，请绑定其他淘宝账号，或直接使用淘宝账号登陆。", get_url ( 'sns', '', 'user' ) );
				}
			} else {
				
				setcookie ( 'login_type', 'sina', time () + 3600 * 24 * 7, '/' );
				setcookie ( 'id', $user_openid ['uid'], time () + 3600 * 24 * 7, '/' );
				$user_info = $user_mod->where ( "id='" . $user_openid ['uid'] . "'" . " and is_del=0" )->find ();
				setcookie ( 'name', $user_info ['name'], time () + 3600 * 24 * 7, '/' );
				
				// 更新用户最后登录时间和最后登录的ip
				$user ['last_ip'] = getClientIp ();
				$user ['last_time'] = time ();
				$user_mod->where ( "id='" . $user_openid ['uid'] . "'" )->save ( $user );
				header ( 'Location:' . get_url ( 'index', '', 'user' ) );
			}
		} else {
			// 未绑定，执行绑定
			Vendor ( 'Taoapi.Taoapi' );
			$Taoapi_Config = Taoapi_Config::Init ();
			$Taoapi_Config->setCharset ( 'UTF-8' );
			$Taoapi_Config->setAppKey ( C ( 'taobao_appkey' ) );
			$Taoapi_Config->setAppSecret ( C ( 'taobao_appsecret' ) );
			// 调用api
			$Taoapi = new Taoapi ();
			// 淘客商品搜索(taobao.taobaoke.items.get)
			$Taoapi->method = 'taobao.user.buyer.get';
			$Taoapi->session = $res ['access_token'];
			$Taoapi->fields = 'user_id,nick,sex,buyer_credit,avatar,has_shop,vip_info';
			
			if (C ( 'taobaoke_pid' ) == '' && C ( 'taobaoke_nick' ) == '') {
				$this->error ( '淘宝客昵称，淘宝客PID未设置，请到 [系统设置 - App Key 设置] 进行设置。' );
			}
			// 提交请求
			$ret = $Taoapi->Send ( 'get', 'xml' )->getArrayData ();
			// 检测API是否遇到错误
			if ($Taoapi->getErrorInfo ()) {
				$message = $Taoapi->getErrorInfo ();
				$this->error ( $message ['sub_msg'] );
			}
			
			$taobao_info = array (
					'user_info' => $ret ['user'] 
			);
			$data = array (
					'type' => 'taobao',
					'openid_username' => $ret ['user'] ['nick'],
					'info' => serialize ( $taobao_info ) 
			);
			$_SESSION ['user_openid_info'] = $data;
			if ($logedin) {
				$data = array (
						'type' => $_SESSION ['user_openid_info'] ['type'],
						'uid' => $uid,
						'openid' => $_SESSION ['openid'],
						'info' => $_SESSION ['user_openid_info'] ['info'] 
				);
				$user_openid_mod->add ( $data );
				$this->success ( "绑定成功！", get_url ( 'sns', '', 'user' ) );
			} else {
				
				header ( 'Location:' . get_url ( 'sign', '', 'user' ) );
			}
		}
	}
	
	// 第三方账号登录后新用户设置
	public function sign() {
		$this->assign ( "sty", array (
				'index',
				'usercenter' 
		) );
		$user_mod = M ( "User" );
		$user_openid_mod = M ( "user_openid" );
		if (! empty ( $_POST )) {
			
			$count = $user_mod->where ( "name='" . trim ( $_POST ['name'] ) . "'" )->count ();
			if ($count) {
				$this->error ( '昵称已经存在!' );
			}
			
			$data ['name'] = trim ( $_POST ['name'] );
			$data ['age'] = "" . $_POST ["age"];
			$data ['sex'] = "" . $_POST ["sex"];
			$province = ($_POST ['province'] == "请选择") ? '' : $_POST ['province'];
			$city = ($_POST ['city'] == "请选择") ? '' : "|" . $_POST ['city'];
			;
			$county = ($_POST ['county'] == "请选择") ? '' : "|" . $_POST ['county'];
			;
			$data ['address'] = $province . $city . $county;
			$data ['info'] = "" . $_POST ["info"];
			$data ['add_time'] = time ();
			$data ['last_ip'] = getClientIp ();
			$data ['ip'] = getClientIp ();
			
			$user_id = $user_mod->add ( $data );
			
			$data = array (
					'type' => $_SESSION ['user_openid_info'] ['type'],
					'uid' => $user_id,
					'openid' => $_SESSION ['openid'],
					'info' => $_SESSION ['user_openid_info'] ['info'] 
			);
			$user_openid_mod->add ( $data );
			
			$user_info = $user_mod->where ( "id=" . $user_id . " and is_del=0" )->find ();
			
			setcookie ( 'login_type', $_SESSION ['user_openid_info'] ['type'], time () + 3600 * 24 * 7, '/' );
			setcookie ( 'id', $user_info ['id'], time () + 3600 * 24 * 7, '/' );
			setcookie ( 'name', $user_info ['name'], time () + 3600 * 24 * 7, '/' );
			
			// 更新用户最后登录时间和最后登录的ip
			$user ['last_ip'] = getClientIp ();
			$user ['last_time'] = time ();
			$user_mod->where ( "id='" . $user_id . "'" )->save ( $user );
			
			// echo($user_openid_mod->getlastsql());
			$this->success ( "绑定成功", get_url ( 'index', '', 'user' ) );
		} else {
			// print_r($_SESSION);
			$user_info = array (
					"name" => $_SESSION ['user_openid_info'] ['openid_username'] 
			);
			$this->assign ( "user_info", $user_info );
			$this->display ();
		}
	}
	
	// 宝贝信息
	public function release() {
		$this->assign ( "sty", array (
				'index',
				'usercenter' 
		) );
		$this->check_login ();
		// 替换seo的值
		$user_mod = M ( "User" );
		$id = $_COOKIE ['id'];
		$userInfo = $user_mod->where ( "id=$id" )->find ();
		$seo ['title'] = $userInfo ['name'] . "分享宝贝_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		
		$items = D ( 'Items' );
		$items_cate = D ( 'ItemsCate' );
		$items_site = D ( 'ItemsSite' );
		$items_tags = D ( 'ItemsTags' );
		$items_tags_item = M ( 'ItemsTagsItem' );
		
		if (isset ( $_POST ['url'] )) {
			
			header ( "Content-type: text/xml; charset=utf-8" );
			
			// 获取商品URL
			$url = trim ( $_POST ['url'] );
			$url = match_url ( $url );
			$fp = fopen ( $url, 'r' );
			if (! $fp) {
				$this->ajaxReturn ( false );
			}
			
			// 获得商品来源
			$domain = gain_domain ( $url );
			$site = $items_site->field ( 'id,alias' )->where ( "site_domain='" . $domain . "'" )->find ();
			if ($site) {
				if ($site ['alias'] == 'taobao' || $site ['alias'] == 'tmall') {
					import ( "@.ORG.Taobao" );
					$taobao = new Taobao ();
					$data = $taobao->item ( $url );
				} elseif ($site ['alias'] == 'paipai') {
					import ( "@.ORG.Paipai" );
					$paipai = new Paipai ();
					$data = $paipai->item ( $url );
				} elseif ($site ['alias'] == 'dangdang') {
					import ( "@.ORG.Dangdang" );
					$dangdang = new Dangdang ();
					$data = $dangdang->item ( $url );
				} elseif ($site ['alias'] == 'vancl') {
					import ( "@.ORG.Vancl" );
					$vancl = new Vancl ();
					$data = $vancl->item ( $url );
				} elseif ($site ['alias'] == '360buy') {
					import ( "@.ORG.Jingdong" );
					$jingdong = new Jingdong ();
					$data = $jingdong->item ( $url );
				} elseif ($site ['alias'] == 'caomeipai') {
					import ( "@.ORG.Caomeipai" );
					$caomeipai = new Caomeipai ();
					$data = $caomeipai->item ( $url );
				} elseif ($site ['alias'] == 'mbaobao') {
					import ( "@.ORG.Mbaobao" );
					$mbaobao = new Mbaobao ();
					$data = $mbaobao->item ( $url );
				} elseif ($site ['alias'] == 'nala') {
					import ( "@.ORG.Nala" );
					$nala = new Nala ();
					$data = $nala->item ( $url );
				}
				
				if ($data ['item_key'] != '') {
					$where ['item_key'] = array (
							'eq',
							$data ['item_key'] 
					);
				} else {
					$where ['url'] = array (
							'eq',
							$data ['url'] 
					);
				}
				$where ['is_del'] = array (
						'eq',
						0 
				);
				// 如果添加的商品存在，获得商品的id、cid
				$add_item = $items->field ( 'id,status' )->where ( $where )->find ();
				if ($add_item) {
					$link = get_url ( 'index', $add_item ['id'], 'item' );
					if ($add_item ['status'] == 1) {
						$this->ajaxReturn ( $link, 'itemExist' );
					} else {
						$this->ajaxReturn ( '1', 'itemCheck' );
					}
				} else {
					$this->ajaxReturn ( $data );
				}
			}
		} else {
			
			$url = $_POST ['collect_url'];
			
			// 分类循环
			$result = $items_cate->where ( 'is_del=0' )->order ( 'ord desc' )->select ();
			$cate_list = array ();
			foreach ( $result as $val ) {
				if ($val ['pid'] == 0) {
					$cate_list ['parent'] [$val ['id']] = $val;
				} else {
					$cate_list ['sub'] [$val ['pid']] [] = $val;
				}
			}
			
			// 获得来源
			$site_list = $items_site->field ( 'id,name,alias' )->select ();
			$this->assign ( 'url', $url );
			$this->assign ( 'site_list', $site_list );
			$this->assign ( 'cate_list', $cate_list );
			$this->display ();
		}
	}
	
	// 发布分享
	public function add() {
		set_time_limit ( 0 );
		if (isset ( $_POST ['submit'] )) {
			$user_mod = M ( "User" );
			$items = D ( 'Items' );
			$items_cate = D ( 'ItemsCate' );
			$items_site = D ( 'ItemsSite' );
			$items_tags = D ( 'ItemsTags' );
			$items_tags_item = M ( 'ItemsTagsItem' );
			
			if ($_POST ['title'] == '') {
				$this->error ( '标题不能为空！' );
			}
			
			$data = $items->create ();
			if (! $data) {
				$this->error ( $items->getError () );
			}
			
			// 下载远程图片
			if (C ( 'down_status' ) == 1) {
				$type = end(explode( '.', $data['img'] ));
				$data['img']=$this->down_item($data['img'], $data['item_key'].'.'.$type);
			}
			
			// 保存上传图片
			if ($_FILES ['upload_img'] ['name'] != '') {				
				$dir=date("Ymd");
				mkdir('./Uploads/LocalItems/'.$dir);
				$upload_info = $this->_upload('./Uploads/LocalItems/'.$dir.'/');
				$this->upload_item($upload_info[0]['savepath'].$upload_info[0]['savename'], $upload_info[0]['savename']);
				$data['img'] = C('web_path').'Uploads/LocalItems/'.$dir.'/'.$upload_info['0']['savename'];
			}
			
			// 添加商品时间
			$data ['add_time'] = time ();
			
			// 添加用户
			$data ['uid'] = cookie ( 'id' );
			
			// 审核
			$data ['status'] = (C ( 'items_status' ) + 1) % 2;
			
			// 分享介绍关键词过滤
			$data ['info'] = $this->filter ( $data ['info'] );
			
			if ($data ['item_key'] != '') {
				$where ['item_key'] = array (
						'eq',
						$data ['item_key'] 
				);
			} else {
				$where ['url'] = array (
						'eq',
						$data ['url'] 
				);
			}
			$where ['is_del'] = array (
					'eq',
					0 
			);
			// 如果添加的商品存在，获得商品的id、cid
			$add_item = $items->field ( 'id,cid' )->where ( $where )->find ();
			// 商品不存在则添加，新的分类item_nums加1
			if (! $add_item) {
				$new_item_id = $result = $items->add ( $data );
				// 用户的分享数share_num加1
				$userId = $items->where ( "id=$new_item_id and is_del=0" )->getField ( "uid" );
				$user_mod->where ( "id=$userId and is_del=0" )->setInc ( "share_num" );
			} else {
				echo "<script>alert('该商品已存在！');</script>";
				echo "<script>history.back();</script>";
				return false;
			}
			$items_cate->where ( "id='" . $data ['cid'] . "'" )->setInc ( 'item_nums' );
			
			$tags = isset ( $_POST ['tags'] ) && trim ( $_POST ['tags'] ) ? trim ( $_POST ['tags'] ) : '';
			if ($tags) {
				// 标签不存在则添加，更新标签和商品关系
				$tags_arr = explode ( ' ', $tags );
				$tags_arr = array_unique ( $tags_arr );
				foreach ( $tags_arr as $tag ) {
					$isset_id = $items_tags->field ( 'id' )->where ( "name='" . $tag . "' and pid='" . $data ['cid'] . "'" )->find ();
					if ($isset_id) {
						$items_tags_item->add ( array (
								'item_id' => $new_item_id,
								'tag_id' => $isset_id ['id'] 
						) );
						$items_tags->where ( "id='" . $isset_id ['id'] . "'" )->setInc ( 'item_nums' ); // 标签item_nums加1
					} else {
						$tag_id = $items_tags->add ( array (
								'name' => $tag,
								'pid' => $data ['cid'] 
						) );
						$items_tags_item->add ( array (
								'item_id' => $new_item_id,
								'tag_id' => $tag_id 
						) );
						$items_tags->where ( "id='" . $tag_id . "'" )->setInc ( 'item_nums' ); // 标签item_nums加1
					}
				}
			}
			$url = get_url ( 'index', '', 'user' );
			header ( 'location:' . $url );
		}
	}
		
	public function add_follow() {
		$user_follow_mod = M ( 'User_follow' );
		$user_mod = M ( 'User' );
		$oUid = $_GET ['id'];
		if (! $oUid || $oUid == $_COOKIE ['id']) {
			$this->ajaxReturn ( "", "notFollowSelf" );
		} else {
			$action = $_POST ['action'];
			if ($action == 'add') {
				$follow ['fans_id'] = $oUid;
				$follow ['uid'] = $_COOKIE ['id'];
				$follow ['add_time'] = time ();
				$user_follow_info = $user_follow_mod->where ( "fans_id=$oUid and uid=" . $_COOKIE ['id'] )->find ();
				if ($user_follow_info) {
					$fans_num = $user_mod->where ( "id=$oUid" )->getField ( 'fans_num' );
					$this->ajaxReturn ( $fans_num, "followRepeat" );
				} else {
					if ($user_follow_mod->add ( $follow )) {
						$user_mod->where ( "id=$oUid" )->setInc ( 'fans_num' );
						$user_mod->where ( "id=" . $_COOKIE ['id'] )->setInc ( 'follow_num' );
						$fans_num = $user_mod->where ( "id=$oUid" )->getField ( 'fans_num' );
						$this->ajaxReturn ( $fans_num );
					}
				}
			} elseif ($action == 'del') {
				$user_follow_info = $user_follow_mod->where ( "fans_id=$oUid and uid=" . $_COOKIE ['id'] )->find ();
				if (! $user_follow_info) {
					$fans_num = $user_mod->where ( "id=$oUid" )->getField ( 'fans_num' );
					$this->ajaxReturn ( $fans_num, "clearFollowRepeat" );
				} else {
					$delFollow = $user_follow_mod->where ( "fans_id=$oUid and uid=" . $_COOKIE ['id'] )->delete ();
					if ($delFollow) {
						$user_mod->where ( "id=$oUid" )->setDec ( 'fans_num' );
						$user_mod->where ( "id=" . $_COOKIE ['id'] )->setDec ( 'follow_num' );
						$fans_num = $user_mod->where ( "id=$oUid" )->getField ( 'fans_num' );
						$this->ajaxReturn ( $fans_num );
					}
				}
			}
		}
	}
	public function fans() {
		$this->assign ( "sty", array (
				'index',
				'style1',
				'usercenter' 
		) );
		$this->items_paiHang ();
		$user_mod = M ( "User" );
		$id = $this->others ();
		
		// 替换seo的值
		$uname = $user_mod->where ( "id=$id" )->getField ( 'name' );
		$seo ['title'] = $uname . "的粉丝_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		
		$sqlid = "fans_id";
		$this->user_list ( $sqlid );
		$this->assign ( "curPage", "uc_fans" );
		$this->display ();
	}
	public function follow() {
		$this->assign ( "sty", array (
				'index',
				'style1',
				'usercenter' 
		) );
		$this->items_paiHang ();
		$user_mod = M ( "User" );
		$id = $this->others ();
		
		// 替换seo的值
		$uname = $user_mod->where ( "id=$id" )->getField ( 'name' );
		$seo ['title'] = $uname . "的关注_" . C ( "site_name" );
		;
		$seo ['keys'] = C ( "site_keyword" );
		$seo ['desc'] = C ( "site_description" );
		$this->assign ( "seo", $seo );
		
		// 获取关注列表
		$sqlid = "uid";
		$this->user_list ( $sqlid );
		$this->assign ( "curPage", "uc_follow" );
		$this->display ();
	}
	public function user_list($sqlid) {
		$id = $this->others ();
		$user_mod = M ( "User" );
		$user_follow_mod = M ( "User_follow" );
		$user_follow_list = $user_follow_mod->where ( $sqlid . "=$id" )->select ();
		$user_id_arr = array ();
		foreach ( $user_follow_list as $key => $val ) {
			if ($sqlid == "uid") {
				$user_id_arr [] = $val ['fans_id'];
			} elseif ($sqlid == "fans_id") {
				$user_id_arr [] = $val ['uid'];
			}
		}
		if ($user_id_arr) {
			$user_id_str = implode ( ",", $user_id_arr );
		} else {
			$user_id_str = - 1;
		}
		import ( "ORG.Util.Page" );
		$count = $user_mod->where ( "is_del=0 and id in ($user_id_str)" )->count ();
		$Page = new Page ( $count, 10 );
		$show = $Page->show ();
		$user_list = $user_mod->where ( "is_del=0 and id in ($user_id_str)" )->field ( "id,name,img,fans_num,follow_num,likes_num,address,info,share_num" )->order ( "id desc" )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
		$this->assign ( 'page', $show );
		$this->assign ( 'count', $count );
		$this->assign ( "user_list", $user_list );
	}
	public function shareItemsDel() {
		$item_id = $_GET ['id'];
		$items_mod = M ( "Items" );
		$user_mod = M ( "User" );
		$userId = $items_mod->where ( "id=$item_id and is_del=0" )->getField ( "uid" );
		$items_mod->where ( "id=$item_id and uid=" . $_COOKIE ['id'] )->setField ( "is_del", 1 );
		$this->delete_items_about ( $item_id );
		// 用户的sare_num减1
		
		$user_mod->where ( "id=$userId and is_del=0" )->setDec ( "share_num" );
		echo $user_mod->getLastSql ();
		$url = get_url ( 'share', '', 'user' );
		header ( 'location:' . $url );
	}
	public function delete_items_about($id) {
		$items_mod = M ( 'Items' );
		$items_cate_mod = M ( 'Items_cate' );
		$items_site_mod = M ( 'Items_site' );
		$items_tags_mod = M ( 'Items_tags' );
		$items_tags_item_mod = M ( 'Items_tags_item' );
		// 分类中item_nums减1
		$cid = $items_mod->field ( 'cid' )->where ( "id='" . $id . "'" )->find ();
		$items_cate_mod->where ( "id='" . $cid ['cid'] . "'" )->setDec ( 'item_nums' );
		// 标签中item_nums减1
		$data ['item_id'] = $id;
		$old_tag = $items_tags_item_mod->field ( 'tag_id' )->where ( $data )->select ();
		foreach ( $old_tag as $tag ) {
			$items_tags_mod->where ( "id='" . $tag ['tag_id'] . "'" )->setDec ( 'item_nums' );
		}
		// 用户的likes_num减1，删除商品和用户喜欢关系
		$user_mod = M ( "User" );
		$items_likes_mod = M ( "Items_likes" );
		$items_likes_list = $items_likes_mod->field ( "uid" )->where ( "items_id=$id" )->select ();
		foreach ( $items_likes_list as $val ) {
			$uid = $val ['uid'];
			$user_mod->where ( "id=$uid and is_del=0" )->setDec ( "likes_num" );
		}
		$items_likes_mod->where ( "items_id=$id" )->delete ();
		// 删除商品信息及商品和标签关系
		$items_tags_item_mod->where ( $data )->delete ();
		// 删除用户评论信息
		$itemscommengs = M ( 'ItemsComments' );
		$itemscommengs->where ( "items_id=$id and is_del=0" )->setField ( "is_del", 1 );
		// 删除商品与专辑的对应关系
		$album_items_mod = M ( "Album_items" );
		$album_items_mod->where ( "items_id=$id" )->delete ();
	}
	public function verify() {
		import ( "ORG.Util.Image" );
		import ( "ORG.Util.String" );
		Image::buildImageVerify ( 4, 3 );
	}
	// 验证码
	public function verify_test() {
		header ( 'Content-type:text/html;charset=utf-8' );
		session_start ();
		$verify = md5 ( $_POST ['verification'] );
		if ($_SESSION ['verify'] == $verify) {
			echo "true";
		} else {
			echo "false";
		}
	}
	public function check_uname_email() {
		$user_mod = M ( "User" );
		$emailq = $_POST ['email'];
		$nameq = $_POST ['uname'];
		$name = $user_mod->where ( "name='$nameq' and is_del=0" )->getField ( "name" );
		$email = $user_mod->where ( "is_del=0 and email='$emailq'" )->getField ( "email" );
		// echo $user_mod->getLastSql();
		if ($email) {
			echo "emailExist"; // 邮箱已存在
		}
		if ($name) {
			echo "nameExist"; // 昵称已存在
		}
	}
	// 用户协议
	public function protocol() {
		$this->assign ( "sty", array (
				'index',
				'style1' 
		) );
		$this->display ();
	}
	// 选择某个商品图作为专辑封面
	public function setAlbumCover() {
		$album_mod = M ( "Album" );
		$album_items_mod = M ( "Album_items" );
		$items_id = $_POST ['items_id'];
		$pid = $_POST ['aid'];
		$action = $_POST ['action'];
		$uid = $album_mod->where ( "id=$pid and is_del=0" )->getField ( "uid" );
		if ($uid == $_COOKIE ['id']) {
			if ($action == "setCover") {
				$album_items_mod->where ( "items_id=$items_id and pid=$pid" )->setField ( "is_cover", 1 );
				$album_items_mod->where ( "items_id<>$items_id and pid=$pid and is_cover=1" )->setField ( "is_cover", 0 );
				$this->ajaxReturn ( '', '设为封面' );
			} elseif ($action == "clearCover") {
				$album_items_mod->where ( "items_id=$items_id and pid=$pid" )->setField ( "is_cover", 0 );
				$this->ajaxReturn ( '', '取消封面！' );
			}
		}
	}
}
// 修改头像类
class pic_data {
	public $data;
	public $status;
	public $statusText;
	public function __construct() {
		$this->data->urls = array ();
	}
}
