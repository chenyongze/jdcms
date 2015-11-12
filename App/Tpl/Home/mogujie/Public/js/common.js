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
	//注册信息验证
	var check_uname_email=$("#check_post_action").val();
	//邮箱地址验证
	$("#email").blur(function(){
		$(".err_email").removeClass("nomal");
		var email=$.trim($(this).val());
		if(email==""){
			$(".err_email").addClass("error").removeClass("correct").html("请填写邮件地址!");
		}else{
			var emailbool=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]+){1,2})$/.test(email);
			if(!emailbool){
				$(".err_email").addClass("error").removeClass("correct").html("邮件格式不正确！");
			}else{
				$.post(check_uname_email,{email:email},function(data){
					if(data=="emailExist"){
						$(".err_email").addClass("error").removeClass("correct").html("该邮件已存在!");
					}else{
						$(".err_email").addClass("correct").removeClass("error").html("&nbsp;");
					}
				});
			}
		}
	});
	//昵称验证
	$("#uname").blur(function(){
		$(".err_ulike").removeClass("nomal");
		var uname=$.trim($("#uname").val());
		if(uname==""){
			$(".err_ulike").addClass("error").removeClass("correct").html("请填写昵称！");
		}else{
			var uanmebool=/(([\u4E00-\u9FA5]){2,10})|(([a-zA-Z0-9]){4,20})|(([\u4E00-\u9FA5-a-zA-Z0-9]){3,20})/.test(uname);
			if(!uanmebool){
				$(".err_ulike").addClass("error").removeClass("correct").html("昵称格式错误！");
			}else{
				$.post(check_uname_email,{uname:uname},function(data){
					if(data=="nameExist"){
						$(".err_ulike").addClass("error").removeClass("correct").html("该昵称已存在！");
					}else{
						//$(".err_ulike").html("<img src='/App/Tpl/Home/mogujie/Public/img/ok_icon.png'>");
						$(".err_ulike").addClass("correct").removeClass("error").html("&nbsp;");
						//$(".err_ulike").addClass("correct").removeClass("error").html("");
					}
				});
			}
		}
		
	});
	$("#pwd").blur(function(){
		$(".err_password").removeClass("nomal");
		var pwd=$.trim($(this).val());
		if(pwd==""){
			$(".err_password").addClass("error").removeClass("correct").html("请输入密码！");
		}else{
			var pwdbool=/^[0-9a-zA-Z]{6,16}$/.test(pwd);
			if(!pwdbool){
				$(".err_password").addClass("error").removeClass("correct").html("密码格式错误!");
			}else{
				$(".err_password").addClass("correct").removeClass("error").html("&nbsp;");
			}
			
		}
	});
	$("#pwdSure").blur(function(){
		$(".err_rstpassword").removeClass("nomal");
		var pwd=$.trim($("#pwd").val());
		var pwdSure=$("#pwdSure").val();
		if(pwdSure==''){
			$(".err_rstpassword").addClass("error").removeClass("correct").html("请重复输入一次密码");
		}else if(pwdSure!=pwd){
			$(".err_rstpassword").addClass("error").removeClass("correct").html("两次输入密码不同!");
		}else{
			$(".err_rstpassword").addClass("correct").removeClass("error").html("&nbsp;");
		}
	});
	$("#verification").blur(function(){
		$(".err_check").removeClass("nomal");
		var verify_post_action=$(this).attr("verify_post_action");
		var verification=$.trim($(this).val());
		if(verification==''){
			$(".err_check").addClass("error").removeClass("correct").html("请输入验证码！");
			return false;
		}
		$.post(verify_post_action,{verification:verification},function(data){
			if(data=="true"){
				$(".err_check").addClass("correct").removeClass("error").html("&nbsp;");
			}else if(data=="false"){
				$(".err_check").addClass("error").removeClass("correct").html("验证码输入错误，请重新输入！");
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
	if($("#registerbtn").attr('disabled')!=false){
		$("#registerbtn").click(function(){
			var registerbtn_post_action=$(this).attr("registerbtn_post_action");
			var registerbtn_post_location=$(this).attr("registerbtn_post_location");
			var pwd=$.trim($("#pwd").val());
			var uname=$.trim($("#uname").val());
			var email=$.trim($("#email").val());
			var sex=$("input[name='sex']:checked").val();
			var age=$("input[name='age']:checked").val();
			if($(".err_email").html()=='&nbsp;' && $(".err_password").html()=='&nbsp;' && $(".err_ulike").html()=='&nbsp;' && $(".err_rstpassword").html()=='&nbsp;' && $(".err_check").html()=='&nbsp;'){
				$.post(registerbtn_post_action,{uname:uname,pwd:pwd,email:email,sex:sex,age:age},function(data){
					if(data=="registerOK"){
						$("#uname").val("");
						$("#email").val("");
						$("#pwd").val("");
						$("#pwdSure").val("");
						$("#verification").val("");
						location.href=registerbtn_post_location;
						$("#registerbtn").hide();
						$("#loading").show();
					}
				});
			}
		});
	}
	//登录
	$("#loginBtn").click(function(){
		var verify_post_action=$("#verification_login").attr("verify_post_action");
		var loginBtn_post_action=$(this).attr("loginBtn_post_action");
		var loginBtn_post_location=$(this).attr("loginBtn_post_location");
		var uname=$.trim($("#uname").val());
		var pwd=$.trim($("#pwd").val());
		var referer=$.trim($("#referer").val());
		var verification_login=$.trim($("#verification_login").val());
		if($("#autologin_checkBox").attr("checked")=="checked"){
			var cookie=true;
		}else{
			var cookie=false;
		}
		if(uname==''){
			$(".err_name").show();
			$(".err_chk").hide();
			$(".err_pass").hide();
			return false;
		}else if(pwd==''){
			$(".err_name").hide();
			$(".err_chk").hide();
			$(".err_pass").show();
			return false;
		}else if(verification_login==''){
			$(".err_pass").hide();
			$(".err_name").hide();
			$(".err_chk").html("请输入验证码！").show();
			return false;
		}else{
			$(".err_pass").hide();
			$(".err_name").hide();
			$(".err_chk").hide();	
			$.post(verify_post_action,{verification:verification_login},function(data){
				if(data=="true"){
					$(".err_chk").hide();
					$.post(loginBtn_post_action,{uname:uname,pwd:pwd,cookie:cookie,referer:referer},function(data){ 
						if(data.data=="yes"){
							$("head").append(data.status);
							location.href=loginBtn_post_location;
						}else if(data.info=='re'){
							$("head").append(data.status);
							location.href=data.data;
						}else if(data.data=='no'){
							$(".err_name").html("该用户禁止登录").show();
							return false;
						}else if(data.data=='verify'){
							$(".err_name").html("该用户正处于审核状态").show();
							return false;
						}else{
							$(".err_name").html("用户名或密码错误").show();
							return false;
						}
						$("#uname").val('');
						$("#pwd").val('');
						$(".err_pass").hide();
						$(".err_name").hide();
						$(".err_chk").hide();			
						$("#loginBtn").hide();
						$("#loading").show();
					},'json');
				}else if(data=="false"){
					$(".err_chk").html("验证码错误").show();
				}
			});
		}
	});
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
	$("#indexlLoginBtn").click(function(){
		var loginBtn_post_action=$(this).attr("loginBtn_post_action");
		var loginBtn_post_location=$(this).attr("loginBtn_post_location");
		var location_login=$(this).attr("location_login");
		var uname=$.trim($("#indexUname").val());
		var pwd=$.trim($("#indexPwd").val());
		if($("#remember").attr("checked")=="checked"){
			var cookie=true;
		}else{
			var cookie=false;
		}
		if(uname==''){
			$(".tips").html("请输入用户名！");
		}else if(pwd==''){
			$(".tips").html("请输入密码！");
		}else{
			$.post(loginBtn_post_action,{uname:uname,pwd:pwd,cookie:cookie},function(data){ 
				if(data.data=="yes"){
					$("head").append(data.status);
					location.href=loginBtn_post_location;
				}else if(data.data=='no'){
					$(".tips").html("该用户禁止登录!");
					return false;
				}else if(data.data=='verify'){
					$(".tips").html("该用户正处于审核状态!");
					return false;
				}else if(data.info=="loginErrRe"){
					$(".tips").html("用户名或密码错误!");
					location.href=location_login;
					return false;
				}else{
					$(".tips").html("用户名或密码错误!");
					return false;
				}
				$(".tips").html("正在登录...");
				$("#uname").val('');
				$("#pwd").val('');
			},'json');	
		}
	});
	//商品页面评论
	$('#pub_content').focus(function(){
		if($('#pub_content').val()=='你也可以顺便说点什么 O(∩_∩)O'){
			$('#pub_content').val('');
		}
	});
	$('#pub_content').blur(function(){
		if($('#pub_content').val()==''){
			$('#pub_content').val('你也可以顺便说点什么 O(∩_∩)O');
		}
	});	
	$(".next").html("下一页");
	$("#pub_submit").click(function(){
		var commentbtn_post_action=$(this).attr("commentbtn_post_action");
		var commentbtn_post_locaction=$(this).attr("commentbtn_post_locaction");
		var info=$.trim($('#pub_content').val());
		var items_id=$("#items_id").val();
		if(info=="" || info=="你也可以顺便说点什么 O(∩_∩)O"){
			$("#commentsErrorNotice").html("请输入评论内容");
			return false;
		}
		if ($("#pub_content").val().length > 140) {
			$("#commentsErrorNotice").html("长度不得大于140个字符");
			return false;
		}
		$.post(commentbtn_post_action,{items_id:items_id,info:info},function(data){
				if(data=="ok"){
					page($("#pager_post_action").val());
					pageJump();
					$("#pub_content").val(" ");
					$("#commentsErrorNotice").html(" ");
				}else if(data=="notLogin"){
					alert("请登录后再进行评论！");
					location.href=commentbtn_post_locaction;
				}else if(data=="check_reComment"){
					$("#commentsErrorNotice").html("您已经留言过了,请晚点再来!");
				}else if(data=="comm_not_show"){
					$("#commentsErrorNotice").html("系统正在审核您的评论内容");
					$("#pub_content").val(" ");
				}
			});
		});
		//鼠标悬浮显示删除
		$(".c_f").hover(function(){
			$(this).find('div').css("visibility","visible");
		},function(){
			$(this).find('div').css("visibility","hidden");
		})
		//删除评论
		$(".comments_del").live('click',function(){
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
			var height=$('.note_comment').height();
			var width=$('.note_comment').width();
			$('.comments_loading').css({"height":height+"px","width":width+"px"});
			$('.comments_loading').show();
			$.post(url,function(data){
				$('.note_comment').html(data.data.list);
				$('#comments_count').html(data.data.count+"人评价");
				$(".comments_loading").hide();
				pageJump();
				$(".c_f").hover(function(){
					$(this).find('div').css("visibility","visible");
				},function(){
					$(this).find('div').css("visibility","hidden");
				})
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
   
		//鼠标放上显示删除和加入专辑，鼠标移开不显示
		$(".pic").hover(
			function(){
				$(this).find(".add_to_album").show();
				$(this).find(".delete_form_album").show();
			},
			function(){
				$(this).find(".add_to_album").hide();
				$(this).find(".delete_form_album").hide();
			}
		);
		//确认是否删除
		$(".delete_form_album").click(function(){
			var t=confirm("确认要删除吗？");
			if(t){
				return true;
			}else{
				return false;
			}
		});
		$(".edit_album .delete").click(function(){
			var t=confirm("确认要删除吗？");
			if(t){
				return true;
			}else{
				return false;
			}
		});
		
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
								tt.addClass('favored');
								td.html(data.data);
							}else if(data.status==-1){
								tt.html("喜欢一下");
								tt.removeClass('favored');
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

	//专辑滤镜效果
	$(".album_link").hover(
		function(){
			$(this).closest("div").find(".mask").css('display','none');
		},
		function(){
			$(this).closest("div").find(".mask").css('display','block');
		}
	);
	//加关注
	var follow_post_action=$(".name_fav").attr("follow_post_action");
	var login_location=$(".name_fav").attr("login_location");
	var uid = $.cookie('id');
	$(".addfo").click(function(){
		if(uid==null){
			location.href=login_location;	
		}else{
			$.post(follow_post_action,{action:"add"},function(data){
				if(data.info=='notFollowSelf'){
					alert("不能关注自己!");
					return false;
				}
				$("#addfollow").hide();
				$("#delfollow").show();
				$("#fans_num").html(data.data);
			},"json");
		}
	});
	//取消关注
	$(".unfollow").click(function(){
		if(uid==null){
			location.href=login_location;	
		}else{
			$.post(follow_post_action,{action:"del"},function(data){
				$("#addfollow").show();
				$("#delfollow").hide();
				$("#fans_num").html(data.data);
			},"json");
		}
	});
	//粉丝和关注列表加关注
	/*$(".addfo_t").click(function(){
		
	})*/
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
			$("#updateNotice").css('color','#ff89a7').html("<span>*</span>用户名不能为空");
		}else if(!namePattern){
			$("#updateNotice").css('color','#ff89a7').html("<span>*</span>用户名格式错误");
		}else{
			$.post(btn_up_post_action,{name:name,sex:sex,age:age,province:province,county:county,city:city,info:info,id:id},function(data){
				if(data=="success"){
					$("#updateNotice").html("修改成功");
				}else if(data=="unameRepeat"){
					$("#updateNotice").css('color','#ff89a7').html("<span>*</span>该用户名已存在");
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
				$("#pwdNotice").css('color','#ff89a7').html("原密码错误！");
				$("#verifyNotice").html("");
			}else if(data=="differ"){
				$("#pwdNotice").html("");
				$("#newPwdNotice").html("");
				$("#newPwdSureNotice").css('color','#ff89a7').html("两次输入新密码不同");
				$("#verifyNotice").html("");
			}else if(data=="patternError"){
				$("#pwdNotice").html("");
				$("#newPwdSureNotice").html("");
				$("#newPwdNotice").css('color','#ff89a7').html("新密码错误");
				$("#verifyNotice").html("");
			}else{
				$.post(verify_post_action,{verification:verification_changepwd},function(data){
					if(data=="true"){
						$("#pwdNotice").css('color','#ff89a7').html("密码修改成功");
						$("#newPwdSureNotice").html("");
						$("#newPwdNotice").html("");
						$("#verifyNotice").html("");
						$("#oldPwd").val("");
						$("#password").val("");
						$("#newPwdSure").val("");
					}else if(data=="false"){
						$("#verifyNotice").css('color','#ff89a7').html("验证码错误");
						$("#pwdNotice").html("");
						$("#newPwdSureNotice").html("");
						$("#newPwdNotice").html("");
					}
				});
			}
		});
	});
	//选择某个商品图作为专辑封面
	var setCover_post_action=$(".cover").attr("setCover_post_action");
	function setCover(){
		$(".setCover").click(function(){
			var items_id=$(this).attr("items_id");
			var aid=$(this).attr("aid");
			$.post(setCover_post_action,{items_id:items_id,aid:aid,action:"setCover"},function(data){
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
				$(".cover").html("设为封面").addClass("setCover").removeClass("clearCover").unbind("click");
				setCover();
			},'json');
		})
	}
	setCover();
	clearCover();
})
