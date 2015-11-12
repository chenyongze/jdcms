$(function(){
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
	//分页图标
	var totalPages = parseInt($(".totalPages").text());
	if(!totalPages){
		$(".pageNav").removeClass("bgcnt");
	}
	
	$("#indexUname").focus(function(){
		if($(this).val()=='请输入用户名'){
			$(this).val("");
		}
	});
	$("#indexUname").blur(function(){
		if($(this).val()==''){
			$(this).val("请输入用户名");
		}
	});
	//首页登录
	$("#indexLogin").click(function(){
		var loginBtn_post_action=$(this).attr("loginBtn_post_action");
		var loginBtn_post_location=$(this).attr("loginBtn_post_location");
		var location_login=$(this).attr("location_login");
		var uname=$.trim($("#indexUname").val());
		var pwd=$.trim($("#indexPwd").val());
		var referer=$.trim($("#referer").val());
		if($("#remember").attr("checked")=="checked"){
			var cookie=true;
		}else{
			var cookie=false;
		}
		if($("#savestate").attr("checked")=="checked"){
			var cookie=true;
		}else{
			var cookie=false;
		}
		if(uname==''){
			$("#indexLgErr").html("请输入用户名").addClass("tips");
		}else if(pwd==''){
			$("#indexLgErr").html("请输入密码").addClass("tips");
		}else{
		$.post(loginBtn_post_action,{uname:uname,pwd:pwd,cookie:cookie,referer:referer},function(data){ 
			if(data.data=="yes"){
				$("head").append(data.status);
				location.href=loginBtn_post_location;
			}else if(data.info=='re'){
				$("head").append(data.status);
				location.href=data.data;
			}else if(data.data=='no'){
				$("#indexLgErr").html("该用户禁止登录").addClass("tips");
				return false;
			}else if(data.data=='verify'){
				$("#indexLgErr").html("该用户正处于审核状态").addClass("tips");
				return false;
			}else if(data.info=="loginErrRe"){
				$("#indexLgErr").html("用户名或密码错误").addClass("tips");
				location.href=location_login;
				return false;
			}else{
				$("#indexLgErr").html("用户名或密码错误").addClass("tips");
				return false;
			}
			$("#indexUname").val('');
			$("#indexPwd").val('');
			$("#indexLgErr").html("正在登录...").addClass("tipsnomal");
		},'json');
		}
	});
	
	//商品评论
	$(".next").html("下一页");
	$(".comment_btn").click(function(){
		var commentbtn_post_action=$(this).attr("commentbtn_post_action");
		var commentbtn_post_locaction=$(this).attr("commentbtn_post_locaction");
		var info=$.trim($('.comment_textarea').val());
		var items_id=$("#items_id").val();
		if(info==""){
			$("#commentsErrorNotice").html("请输入评论内容");
			return false;
		}
		if ($(".comment_textarea").val().length > 140) {
			$("#commentsErrorNotice").html("长度不得大于140个字符");
			return false;
		}
		$.post(commentbtn_post_action,{items_id:items_id,info:info},function(data){
				if(data=="ok"){
					page($("#pager_post_action").val());
					pageJump();
					$(".comment_textarea").val(" ");
					$("#commentsErrorNotice").html(" ");
				}else if(data=="notLogin"){
					alert("请登录后再进行评论！");
					location.href=commentbtn_post_locaction;
				}else if(data=="check_reComment"){
					$("#commentsErrorNotice").html("您已经留言过了,请晚点再来!");
				}else if(data=="comm_not_show"){
					$("#commentsErrorNotice").html("系统正在审核您的评论内容");
					$(".comment_textarea").val(" ");
				}
			});
		});
		//删除评论
		$(".twitter_comment_reply").live('click',function(){
			var web_path=$(this).attr("web_path");
			var commDel_post_action=$(this).attr("commDel_post_action");
			var comm_id=$(this).attr("comm_id");
			var currentPage=$(".current").html();
			var item_id=$("#items_id").val();
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
			var height=$('.meili_note_comment').height();
			var width=$('.meili_note_comment').width();
			$('.comments_loading').css({"height":height+"px","width":width+"px"});
			$('.comments_loading').show();
			$.post(url,function(data){
				$('.meili_note_comment').html(data.data.list);
				$('.twitter_comment_num').html(data.data.count);
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
   
		//鼠标放上显示喜欢和收藏，鼠标移开不显示
		$(".pic").hover(
			function(){
				$(this).find(".like_merge").show();
			},
			function(){
				$(this).find(".like_merge").hide();
			}
		);
		
		//点击喜欢	
		   $(".s1").unbind().bind('click',function() {
		   var like_post_location=$("#like_post_location").val();
		   var like_post_action=$("#like_post_action").val();
			   var logurl = like_post_location  ;
			   var uid = $.cookie('id');
			   var item_id = $(this).attr("item_id");
			   var tt = $(this).find(".sl");
			   var tk = $("#like_"+item_id);
			   var td = $("#"+item_id);
			   var val = $(this).html();
			   if(uid){
				   if(item_id!=0){ 
					    $.post(like_post_action, { items_id: item_id, val: val}, function(data){
							if(data.status==-2){
								location.href=logurl;			
							}else if(data.status==1){
								tk.attr('class','');
								tt.html("已喜欢");
								td.html(data.data);
							}else if(data.status==-1){
								tk.attr('class','lm_love2');
								tt.html("喜欢");
								td.html(data.data);
							}else{
								alert(data.info);
							}
						},"json"); 
				   }
			   }else{
					location.href=logurl;	   
			   }
			 });

		//用户中心加关注
			var login_location=$(".flw_msg").attr("login_location");
			var uid = $.cookie('id');

	function add_follow(){
		$(".addUserFollow").click(function(){
			var login_location=$(".flw_msg").attr("login_location");
			var uid = $.cookie('id');
			if(uid==null){
				location.href=login_location;	
			}else{
				var follow_post_action=$(this).attr("follow_post_action");
				$.post(follow_post_action,{action:"add"},function(data){
					if(data.info=='notFollowSelf'){
						alert("不能关注自己!");
						return false;
					}
					$(".flw_msg span").html("已关注").addClass("removeUserFollow pink_follow").removeClass("red_follow addUserFollow").unbind("click");
					$("#fans_num").html(data.data);
					del_follow();
				},"json");
			}
		});
	}
	function del_follow(){
		$(".removeUserFollow").hover(function(){
			$(this).html("取消关注");
		},function(){
			$(this).html("已关注");
		}).click(function(){
			if(uid==null){
				location.href=login_location;	
			}else{
				var follow_post_action=$(this).attr("follow_post_action");
				$.post(follow_post_action,{action:"del"},function(data){
					$(".flw_msg span").html("+ 加关注").addClass("red_follow addUserFollow").removeClass("removeUserFollow pink_follow").unbind();
					$("#fans_num").html(data.data);
					add_follow();
				},"json");
			}
		});
	}
	add_follow();
	del_follow();

		//账户设置之新用户第三方登录后设置
	$("#btn_sign_up").click(function(){
		var name=$.trim($("#nickname").val());
		var namePattern=/^([\u4E00-\u9FA5]|[a-zA-Z0-9]){4,20}$/.test(name);//验证用户名格式
		if(name==""){
			$("#updateNotice").css('color','red').html("用户名不能为空");
			return false;
		}else if(!namePattern){
			$("#updateNotice").css('color','red').html("用户名格式错误");
			return false;
		}
		
	});

		//账户设置之基本信息
	$(".ext_submit").click(function(){
		var btn_up_post_action=$(this).attr("btn_up_post_action");
		var id=$("#hiddenId").val();
		var name=$.trim($("#nickname").val());
		var sex=$("input[name='sex']:checked").val();
		var age=$("input[name='age']:checked").val();

		var city=$("#s_city").val();
		var province=$("#s_province").val();
		var county=$("#s_county").val();
		var info=$("#about_me").val();
		var namePattern=/(([\u4E00-\u9FA5]){2,10})|(([a-zA-Z0-9]){4,20})|(([\u4E00-\u9FA5-a-zA-Z0-9]){3,20})/.test(name);//验证用户名格式
		if(name==""){
			$("#updateNotice").html("用户名不能为空");
		}else if(!namePattern){
			$("#updateNotice").html("用户名格式错误");
		}else{
			$.post(btn_up_post_action,{name:name,sex:sex,age:age,province:province,county:county,city:city,info:info,id:id},function(data){
				if(data=="success"){
					$("#updateNotice").html("修改成功");
				}else if(data=="unameRepeat"){
					$("#updateNotice").html("该用户名已存在");
				}
			});
		}
		
	});
	//粉丝与关注页面加关注
	var login_location=$(".follow").attr("login_location");
	var uid = $.cookie('id');
	function af_add_follow(){
		$(".ex_follow").click(function(){
			if(uid==null){
				location.href=login_location;	
			}else{
				var follow_post_action=$(this).attr("follow_post_action");
				$.post(follow_post_action,{action:"add"},function(data){
					if(data.info=='notFollowSelf'){
						alert("不能关注自己!");
						return false;
					}
					$(".follow span").html("已关注").addClass("ex_notfollow").removeClass("ex_follow").unbind("click");
					$("#follower_num").html(data.data);
					af_del_follow();
				},"json");
			}
		});
	}
	function af_del_follow(){
		$(".ex_notfollow").hover(function(){
			$(this).html("取消关注");
		},function(){
			$(this).html("已关注");
		}).click(function(){
			if(uid==null){
				location.href=login_location;	
			}else{
				var follow_post_action=$(this).attr("follow_post_action");
				$.post(follow_post_action,{action:"del"},function(data){
					$(".follow span").html("+ 加关注").addClass("ex_follow").removeClass("ex_notfollow").unbind();
					$("#follower_num").html(data.data);
					af_add_follow();
				},"json");
			}
		});
	}
	af_add_follow();
	af_del_follow();
	//将商品加入专辑
	$(".creat_btn").click(function(){
		var items_id=$("#album_items_id").val();
		var album_id=$("#album_items_title").val();
		var info=$("#album_items_info").val();
		var albumBtn_post_action=$(this).attr("albumBtn_post_action");
		var albumBtn_post_location=$(this).attr("albumBtn_post_location");
		$.post(albumBtn_post_action,{items_id:items_id,album_id:album_id,info:info},function(data){
			if(data=="添加成功"){
				$(".error_icon").html(data);
				location.href=albumBtn_post_location;
				//$("#creat_btn").hide();
				//$("#loading").show();
			}else{
				$(".error_icon").html(data).show();
			}
		});
	})	
		//专辑信息（创建和编辑专辑）
	$(".AlbumInfoBtn").click(function(){
		var albumCateId=$("#albumCate").val();
		var albumTitle=$.trim($("#albumTitle").val());
		var albumInfo=$("#albumInfo").val();
		var hiddenaid=$("#hiddenaid").val();
		var AlbumInfoBtn_post_action=$(this).attr("AlbumInfoBtn_post_action");
		var AlbumInfoBtn_post_location=$(this).attr("AlbumInfoBtn_post_location");
		if(albumTitle==''){
			$("#createAlbumError").addClass("error_icon").html("请输入专辑名称").show();
			$("#cate_error").hide();
			return false;
		}
		if(albumCateId==0){
			$("#createAlbumError").html("").removeClass("error_icon");
			$("#cate_error").show();
		}else{
			$("#cate_error").hide();
			$.post(AlbumInfoBtn_post_action,{albumCateId:albumCateId,albumTitle:albumTitle,albumInfo:albumInfo,hiddenaid:hiddenaid},function(data){
				if(data=="successSave"){
					$("#createAlbumError").css("color","#FF89A7").html("专辑修改成功").removeClass("error_icon");
					location.href=AlbumInfoBtn_post_location;//{:u('Uc/album')}
				}else if(data=="titleRepeat"){
					$("#createAlbumError").html("该专辑名称已存在！").addClass("error_icon").show();
				}else if(data=="noAccess"){
					$("#createAlbumError").html("您无权修改该专辑").addClass("error_icon").show();
				}else{
					$("#createAlbumError").css("color","#FF89A7").html("专辑创建成功").removeClass("error_icon");
					location.href=AlbumInfoBtn_post_location;
				}
			});
		}
		
	}); 

	//账户设置之密码修改
	$("#pwdBtn").click(function(){
		var verification_changepwd=$.trim($("#verification_changepwd").val());
		var verify_post_action=$(this).attr("verify_post_action");
		var pwdBtn_post_action=$("#pwdBtn").attr("pwdBtn_post_action");
		var oldPwd=$.trim($("#oldPwd").val());
		var newPwd=$.trim($("#password").val());
		var newPwdSure=$.trim($("#newPwdSure").val());
		$.post(pwdBtn_post_action,{oldPwd:oldPwd,newPwd:newPwd,newPwdSure:newPwdSure},function(data){
			if(data=="oldPwdError"){
				$("#newPwdSureNotice").html("");
				$("#newPwdNotice").html("");
				$("#pwdNotice").html("原密码错误！");
				$("#verifyNotice").html("");
			}else if(data=="differ"){
				$("#pwdNotice").html("");
				$("#newPwdNotice").html("");
				$("#newPwdSureNotice").html("两次输入新密码不同");
				$("#verifyNotice").html("");
			}else if(data=="patternError"){
				$("#pwdNotice").html("");
				$("#newPwdSureNotice").html("");
				$("#newPwdNotice").html("新密码错误");
				$("#verifyNotice").html("");
			}else{
				$.post(verify_post_action,{verification:verification_changepwd},function(data){
					if(data=="true"){
						$("#pwdNotice").html("密码修改成功");
						$("#newPwdSureNotice").html("");
						$("#newPwdNotice").html("");
						$("#verifyNotice").html("");
						$("#oldPwd").val("");
						$("#password").val("");
						$("#newPwdSure").val("");
					}else if(data=="false"){
						$("#verifyNotice").html("验证码错误");
						$("#pwdNotice").html("");
						$("#newPwdSureNotice").html("");
						$("#newPwdNotice").html("");
					}
				});
			}
		});
	});
	//用户中心:光标悬浮在头像上显示修改头像
	$(".showChangeHead").hover(function(){
		$(".showChangeHead .change").show();
	},function(){
		$(".showChangeHead .change").hide();
	})
	$(".p_face").hover(function(){
		$(".p_face #changeFace").show();
	},function(){
		$(".p_face #changeFace").hide();
	})
	//确认是否删除商品
	$(".uc_items_del").click(function(){
		var t=confirm("确认要删除吗？");
		if(t){
			return true;
		}else{
			return false;
		}
	});
	//确认是否删除专辑
	$(".delMyAlbum").click(function(){
		var t=confirm("确认要删除吗？");
		if(t){
			return true;
		}else{
			return false;
		}
	})

})
//$(document).ready(function(){var doc=document,inputs=doc.getElementsByTagName('input'),supportPlaceholder='placeholder'in doc.createElement('input'),placeholder=function(input){var text=input.getAttribute('placeholder'),defaultValue=input.defaultValue;if(defaultValue==''){input.value=text}input.onfocus=function(){if(input.value===text){this.value=''}};input.onblur=function(){if(input.value===''){this.value=text}}};if(!supportPlaceholder){for(var i=0,len=inputs.length;i<len;i++){var input=inputs[i],text=input.getAttribute('placeholder');placeholder(input)}}});