$(function(){
	//鼠标放上显示喜欢和收藏，鼠标移开不显示
	$(".pic").hover(
		function(){
			$(this).find(".op").show();
		},
		function(){
			$(this).find(".op").hide();
		}
	);
	$(".list_tuijian li").hover(function(){$(this).find("p a").show()},function(){$(this).find("p a").hide()});
	
	//jquery cookie
	jQuery.cookie = function(name, value, options) {
	    if (typeof value != 'undefined') { // name and value given, set cookie
	        options = options || {};
	        if (value === null) {
	            value = '';
	            options.expires = -1;
	        }
	        var expires = '';
	        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
	            var date;
	            if (typeof options.expires == 'number') {
	                date = new Date();
	                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
	            } else {
	                date = options.expires;
	            }
	            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
	        }
	        var path = options.path ? '; path=' + options.path : '';
	        var domain = options.domain ? '; domain=' + options.domain : '';
	        var secure = options.secure ? '; secure' : '';
	        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	    } else { // only name given, get cookie
	        var cookieValue = null;
	        if (document.cookie && document.cookie != '') {
	            var cookies = document.cookie.split(';');
	            for (var i = 0; i < cookies.length; i++) {
	                var cookie = jQuery.trim(cookies[i]);
	                // Does this cookie string begin with the name we want?
	                if (cookie.substring(0, name.length + 1) == (name + '=')) {
	                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
	                    break;
	                }
	            }
	        }
	        return cookieValue;
	    }
	};
	
   //点击喜欢
	
   $(".s1").unbind().bind('click',function() {
   var like_post_location=$("#like_post_location").val();
   var like_post_action=$("#like_post_action").val();
	   var logurl = like_post_location  ;
	   var uid = $.cookie('id');
	   var item_id = $(this).attr("item_id");
	   var tt = $(this);
	   var td = $("#"+item_id);
	   var val = $(this).html();
	   if(uid){
		   if(item_id!=0){ 
			    $.post(like_post_action, { items_id: item_id, val: val}, function(data){
					if(data.status==-2){
						location.href=logurl;			
					}else if(data.status==1){
						tt.html("已喜欢");
						td.html(data.data+'喜欢');
					}else if(data.status==-1){
						tt.html("喜欢一下");
						td.html(data.data+'喜欢');
					}else{
						alert(data.info);
					}
				},"json"); 
		   }
	   }else{
			location.href=logurl;	   
	   }
	 });
   
	//商品页面添加评论
	$(".next").html("下一页");
	$("#commentbtn").click(function(){
		var commentbtn_post_action=$(this).attr("commentbtn_post_action");
		var commentbtn_post_locaction=$(this).attr("commentbtn_post_locaction");
		var info=$.trim($('#info').val());
		var items_id=$("#items_id").val();
		if(info==""){
			$("#commentsErrorNotice").html("请输入评论内容");
			return false;
		}
		if ($("#info").val().length > 140) {
			$("#commentsErrorNotice").html("长度不得大于140个字符");
			return false;
		}
		$.post(commentbtn_post_action,{items_id:items_id,info:info},function(data){
			if(data=="ok"){
				page($("#pager_post_action").val());
				pageJump();
				$("#info").val(" ");
				$("#commentsErrorNotice").html(" ");
			}else if(data=="notLogin"){
				alert("请登录后再进行评论！");
				location.href=commentbtn_post_locaction;
			}else if(data=="check_reComment"){
				$("#commentsErrorNotice").html("您已经留言过了,请晚点再来!");
			}else if(data=="comm_not_show"){
				$("#commentsErrorNotice").html("系统正在审核您的评论内容");
				$("#info").val(" ");
			}
		});
	});
	//删除评论
	$(".comments_del").live('click',function(){
		var web_path=$(this).attr("web_path");
		var commDel_post_action=$(this).attr("commDel_post_action");
		var comm_id=$(this).attr("comm_id");
		var currentPage=$(".current").html();
		var item_id = $(".likeItems").attr("item_id");	
		if(currentPage==null){
			url=$("#pager_post_action").val();
		}else{
			url=web_path+"index.php?a=index&m=Item&id="+item_id+"&p="+currentPage;
		}
		$.post(commDel_post_action,{comm_id:comm_id},function(data){
			if(data=="delOK"){
				page(url);
				pageJump();
			}
		});
	});
	//评论分页
	function page(url){
		var height=$('#comment_list').height();
		var width=$('#comment_list').width();
		$('.comments_loading').css({"height":height+"px","width":width+"px"});
		$('.comments_loading').show();
		$.post(url,function(data){
			$('#comment_list').html(data.data.list);
			$('#comments_count').html(data.data.count+"人评价");
			$(".prev").html("上一页");
			$(".next").html("下一页");
			$(".comments_loading").hide();
			pageJump();
		},'json');
	}
	function pageJump(){ 
		$('#comments_page a').click(function(){
			$(this).click(function(){return false;});
			if($(this).attr("class")){
				return false;
			}
			page($(this).attr('href'));
			return false;
		});		
	}
	pageJump();

	//注册页面
	//对文本框的格式判断
	var check_uname_email=$("#check_post_action").val();
	$("#uname").blur(function(){
		var uname=$.trim($("#uname").val());
		if(uname==""){//判断用户名是否填写正确
			$("#unameNotice").css('color','red').html("用户名不能为空！");
		}else{
			var uanmebool=/(([\u4E00-\u9FA5]){2,10})|(([a-zA-Z0-9]){4,20})|(([\u4E00-\u9FA5-a-zA-Z0-9]){3,20})/.test(uname);
			if(!uanmebool){
				$("#unameNotice").css('color','red').html("用户名格式错误！");
			}else{
				$.post(check_uname_email,{uname:uname},function(data){
					if(data=="nameExist"){
						$("#unameNotice").css('color','red').html("用户名已存在！");
					}else{
						$("#unameNotice").css('color','blue').html("填写正确");
					}
				})
			}
		}
		
	});
	$("#pwd").blur(function(){
		var pwd=$.trim($(this).val());
		if(pwd==""){
			$("#pwdNotice").css('color','red').html("密码不能为空！");
		}else{
			var pwdbool=/^[0-9a-zA-Z]{6,16}$/.test(pwd);
			if(!pwdbool){
				$("#pwdNotice").css('color','red').html("密码格式错误");
			}else{
				$("#pwdNotice").css('color','blue').html("填写正确");
			}
			
		}
	});
	$("#pwdSure").blur(function(){
		var pwd=$.trim($("#pwd").val());
		var pwdSure=$("#pwdSure").val();
		if(pwdSure!=pwd){
			$("#pwdSureNotice").css('color','red').html("两次输入密码不同");
		}else{
			$("#pwdSureNotice").css('color','blue').html("填写正确");
		}
	});
	$("#email").blur(function(){
		var email=$.trim($(this).val());
		if(email==""){
			$("#emailNotice").css('color','red').html("邮件不能为空");
		}else{
			var emailbool=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]+){1,2})$/.test(email);
			if(!emailbool){
				$("#emailNotice").css('color','red').html("邮件格式不正确！");
			}else{
				$.post(check_uname_email,{email:email},function(data){
					if(data=="emailExist"){
						$("#emailNotice").css('color','red').html("该邮件已存在!");
					}else{
						$("#emailNotice").css('color','blue').html("填写正确");
					}
				})
			}
		}
	});
	$("#verification").blur(function(){
		var verify_post_action=$(this).attr("verify_post_action");
		var verification=$.trim($(this).val());
		if(verification==''){
			$("#verifyNotice").css('color','red').html("请输入验证码！");
			return false;
		}
		$.post(verify_post_action,{verification:verification},function(data){
			if(data=="true"){
				$("#verifyNotice").css('color','blue').html("填写正确");
			}else if(data=="false"){
				$("#verifyNotice").css('color','red').html("验证码输入错误！");
			}
		});
	});

	//注册新用户
	$("#checkbox").click(function(){
		if($(this).attr("checked")=="checked"){
			$("#registerbtn").attr('disabled',false);	
		}else{
			$("#registerbtn").attr('disabled','disabled');
		}
	});
	$("#registerbtn").attr('disabled',"");
	$("#registerbtn").click(function(){
		var registerbtn_post_action=$(this).attr("registerbtn_post_action");
		var registerbtn_post_location=$(this).attr("registerbtn_post_location");
		var pwd=$.trim($("#pwd").val());
		var age=$("input[name='age']:checked").val();
		var sex=$("input[name='sex']:checked").val();
		
		var uname=$.trim($("#uname").val());
		var email=$.trim($("#email").val());
		if($("#unameNotice").html()=="填写正确" && $("#pwdNotice").html()=="填写正确" && $("#pwdSureNotice").html()=="填写正确" && $("#emailNotice").html()=="填写正确" && $("#verifyNotice").html()=="填写正确"){
			$.post(registerbtn_post_action,{uname:uname,pwd:pwd,email:email,age:age,sex:sex},function(data){
				if(data=="registerOK"){
					$("#uname").val("");
					$("#email").val("");
					$("#pwd").val("");
					$("#pwdSure").val("");
					location.href=registerbtn_post_location;
					$("#registerbtn").hide();
					$("#loading").show();
				}
				
			});
		}
	});
	$('#uname').focus(function(){
		if($('#uname').val()=='输入用户名'){
			$('#uname').val('');
		}
	});
	$('#uname').blur(function(){
		if($('#uname').val()==''){
			$('#uname').val('输入用户名');
		}
	});	
	//首页登录
	$("#indexLoginBtn").click(function(){
		var loginBtn_post_action=$(this).attr("loginBtn_post_action");
		var loginBtn_post_location=$(this).attr("loginBtn_post_location");
		var location_login=$(this).attr("location_login");
		var uname=$.trim($("#uname").val());
		var pwd=$.trim($("#pwd").val());
		if($("#autologin_checkBox").attr("checked")==false){
			var cookie=false;
		}else{
			var cookie=true;
		}
		if(uname=='' || uname=="输入用户名"){
			$("#loginError").css('color','red').html("请输入用户名");
			return false;
		}
		if(pwd==''){
			$("#loginError").css('color','red').html("请输入密码");
			return false;
		}
		$.post(loginBtn_post_action,{uname:uname,pwd:pwd,cookie:cookie},function(data){ 
			if(data.data=="yes"){
				$("#uname").val('');
				$("#pwd").val('');
				$("head").append(data.status);
				location.href=loginBtn_post_location;
			}else if(data.data=='no'){
				$("#loginError").css('color','red').html("该用户禁止登录");
				return false;
			}else if(data.data=='verify'){
				$("#loginError").css('color','red').html("该用户正处于审核状态");
				return false;
			}else if(data.info=="loginErrRe"){
				$("#loginError").css('color','red').html("用户名或密码错误");
				location.href=location_login;
				return false;
			}else{
				$("#loginError").css('color','red').html("用户名或密码错误");
				return false;
			}
			$("#loginError").css('color','blue').html("登陆成功");
			$("#indexLoginBtn").hide();
			$("#loading").show();
		},'json');
	});
	//登录
	$("#loginBtn").click(function(){
		var verify_post_action=$("#verification_login").attr("verify_post_action");
		var loginBtn_post_action=$(this).attr("loginBtn_post_action");
		var loginBtn_post_location=$(this).attr("loginBtn_post_location");
		var uname=$.trim($("#uname").val());
		var pwd=$.trim($("#pwd").val());
		var referer=$.trim($("#referer").val());
		var verification_login=$.trim($("#verification_login").val());
		if($("#autologin_checkBox").attr("checked")==false){
			var cookie=false;
		}else{
			var cookie=true;
		}
		if(uname=='' || uname=="输入用户名"){
			$("#loginError").css('color','red').html("请输入用户名");
			return false;
		}
		if(pwd==''){
			$("#loginError").css('color','red').html("请输入密码");
			return false;
		}
		if(verification_login==''){
			$("#loginError").css('color','red').html("请输入验证码");
			return false;
		}
		$.post(verify_post_action,{verification:verification_login},function(data){
			if(data=="true"){
				$.post(loginBtn_post_action,{uname:uname,pwd:pwd,cookie:cookie,referer:referer},function(data){ 
					if(data.data=="yes"){
						$("#uname").val('');
						$("#pwd").val('');
						$("head").append(data.status);
						location.href=loginBtn_post_location;
					}else if(data.info=='re'){
						$("head").append(data.status);
						location.href=data.data;
					}else if(data.data=='no'){
						$("#loginError").css('color','red').html("该用户禁止登录");
						return false;
					}else if(data.data=='verify'){
						$("#loginError").css('color','red').html("该用户正处于审核状态");
						return false;
					}else{
						$("#loginError").css('color','red').html("用户名或密码错误");
						return false;
					}
					$("#loginError").css('color','blue').html("登陆成功");
					$("#loginBtn").hide();
					$("#loading").show();
				},'json');
			}else{
				$("#loginError").css('color','red').html("验证码错误");
			}
		});
	});
	//账户设置之基本信息
	$("#btn_up").click(function(){
		var btn_up_post_action=$(this).attr("btn_up_post_action");
		var id=$("#hiddenId").val();
		var name=$.trim($("#basicName").val());
		var sex=$("input[name='sex']:checked").val();
		var age=$("input[name='age']:checked").val();
		var address=$("#s_province").val()+"|"+$("#s_city").val()+"|"+$("#s_county").val();

		var city=$("#s_city").val();
		var province=$("#s_province").val();
		var county=$("#s_county").val();
		var info=$("#selfInfo").val();
		var namePattern=/(([\u4E00-\u9FA5]){2,10})|(([a-zA-Z0-9]){4,20})|(([\u4E00-\u9FA5-a-zA-Z0-9]){3,20})/.test(name);//验证用户名格式
		if(name==""){
			$("#updateNotice").css('color','red').html("用户名不能为空");
		}else if(!namePattern){
			$("#updateNotice").css('color','red').html("用户名格式错误");
		}else{
			$.post(btn_up_post_action,{name:name,sex:sex,age:age,province:province,county:county,city:city,info:info,id:id},function(data){
				if(data=="success"){
					$("#updateNotice").css('color','blue').html("修改成功");
				}else if(data=="unameRepeat"){
					$("#updateNotice").css('color','red').html("该用户名已存在");
				}
			});
		}
		
	});
	
	//账户设置之密码修改
	$("#pwdBtn").click(function(){
		var pwdBtn_post_action=$("#pwdBtn").attr("pwdBtn_post_action");
		var oldPwd=$.trim($("#oldPwd").val());
		var newPwd=$.trim($("#newPwd").val());
		var newPwdSure=$.trim($("#newPwdSure").val());
		$.post(pwdBtn_post_action,{oldPwd:oldPwd,newPwd:newPwd,newPwdSure:newPwdSure},function(data){
			if(data=="oldPwdError"){
				$("#pwdErrorNotice").css('color','red').html("原密码错误！");
			}else if(data=="differ"){
				$("#pwdErrorNotice").css('color','red').html("两次输入新密码不同");
			}else if(data=="patternError"){
				$("#pwdErrorNotice").css('color','red').html("新密码格式错误");
			}else{
				$("#pwdErrorNotice").css('color','blue').html("密码修改成功");
				$("#oldPwd").val("");
				$("#newPwd").val("");
				$("#newPwdSure").val("");
			}
			
		});
	});
	//账户设置之新用户第三方登录后设置
	$("#btn_sign_up").click(function(){
		var name=$.trim($("#basicName").val());
		var namePattern=/^([\u4E00-\u9FA5]|[a-zA-Z0-9]){4,20}$/.test(name);//验证用户名格式
		if(name==""){
			$("#updateNotice").css('color','red').html("用户名不能为空");
			return false;
		}else if(!namePattern){
			$("#updateNotice").css('color','red').html("用户名格式错误");
			return false;
		}
		
	});
	
	//添加商品到专辑
	$("#albumBtn").click(function(){
		var items_id=$("#album_items_id").val();
		var album_id=$("#album_items_title").val();
		var info=$("#album_items_info").val();
		var albumBtn_post_action=$(this).attr("albumBtn_post_action");
		var albumBtn_post_location=$(this).attr("albumBtn_post_location");
		$.post(albumBtn_post_action,{items_id:items_id,album_id:album_id,info:info},function(data){
			if(data=="添加成功"){
				$("#addAlbumItems").css('color','blue').html(data);
				location.href=albumBtn_post_location;
				$("#albumBtn").hide();
				$("#loading").show();
			}else{
				$("#addAlbumItems").css('color','red').html(data);
			}
		});
	});

	//专辑信息（创建和编辑专辑）
	$("#AlbumInfoBtn").click(function(){
		var albumCateId=$("#albumCate").val();
		var albumTitle=$("#albumTitle").val();
		var albumInfo=$("#albumInfo").val();
		var hiddenaid=$("#hiddenaid").val();
		var AlbumInfoBtn_post_action=$(this).attr("AlbumInfoBtn_post_action");
		var AlbumInfoBtn_post_location=$(this).attr("AlbumInfoBtn_post_location");
		if(albumTitle==''){
			$("#createAlbumError").css('color','red').html("专辑名不能为空");
			return false;
		}
		if(albumCateId==0){
			$("#createAlbumError").css('color','red').html("请选择专辑分类");
		}else{
			$.post(AlbumInfoBtn_post_action,{albumCateId:albumCateId,albumTitle:albumTitle,albumInfo:albumInfo,hiddenaid:hiddenaid},function(data){
				if(data=="successSave"){
					$("#createAlbumError").css('color','blue').html("专辑修改成功");
					location.href=AlbumInfoBtn_post_location;//{:u('Uc/album')}
					$("#AlbumInfoBtn").hide();
					$("#loading").show();
				}else if(data=="titleRepeat"){
					$("#createAlbumError").css('color','red').html("该专辑名称已存在！");
				}else if(data=="noAccess"){
					$("#createAlbumError").css('color','red').html("您无权修改该专辑");
				}else{
					$("#createAlbumError").css('color','blue').html("专辑创建成功");
					location.href=AlbumInfoBtn_post_location;
					$("#AlbumInfoBtn").hide();
					$("#loading").show();
				}
			});
		}
		
	}); 
	//判断是否确认删除
	$(".uc_items_del").click(function(){
		var t=confirm("确认要删除吗？");
		if(t){
			return true;
		}else{
			return false;
		}
	});

	//加关注
	var login_location=$(".guanzhu").attr("login_location");
	var uid = $.cookie('id');
	
	function add_follow(){
		$("#addguanzhu").click(function(){
			if(uid==null){
				location.href=login_location;	
			}else{
				var follow_post_action=$(this).attr("follow_post_action");
				$.post(follow_post_action,{action:"add"},function(data){
					if(data.info=='notFollowSelf'){
						alert("不能关注自己!");
						return false;
					}
					$(".guanzhu").html("已关注").attr('id',"delguanzhu").unbind("click");
					$("#fans_num").html(data.data);
					del_follow();
				},"json");
			}
		});
	}
	function del_follow(){
		$("#delguanzhu").hover(function(){
			$(this).html("取消关注");
		},function(){
			$(this).html("已关注");
		}).click(function(){
			if(uid==null){
				location.href=login_location;	
			}else{
				var follow_post_action=$(this).attr("follow_post_action");
				$.post(follow_post_action,{action:"del"},function(data){
					$(".guanzhu").unbind().attr('id',"addguanzhu").html("加关注");
					$("#fans_num").html(data.data);
					add_follow();
				},"json");
			}
		});
	}
	add_follow();
	del_follow();

	//选择某个商品图作为专辑封面
		var setCover_post_action=$(".cover").attr("setCover_post_action");
		function setCover(){
			$(".setCover").click(function(){
				var items_id=$(this).attr("items_id");
				var aid=$(this).attr("aid");
				$.post(setCover_post_action,{items_id:items_id,aid:aid,action:"setCover"},function(data){
					//alert(data.info);
					$(".cover").html("设为封面").addClass("setCover").removeClass("clearCover").unbind("click");
					$("#cover_"+items_id).html("专辑封面").addClass("clearCover").removeClass("setCover").unbind("click");
					setCover();
					clearCover();
				},'json');
			});
		}
		function clearCover(){
			$(".clearCover").click(function(){
				var items_id=$(this).attr("items_id");
				var aid=$(this).attr("aid");
				$.post(setCover_post_action,{items_id:items_id,aid:aid,action:"clearCover"},function(data){
					//alert(data.info);
					$(".cover").html("设为封面").addClass("setCover").removeClass("clearCover").unbind("click");
					setCover();
				},'json');
			})
		}
		setCover();
		clearCover();
});
	

$(function(){
	//关闭tips
	$('.close').live('click', function() {
		$('.welcome').hide();
	});
});