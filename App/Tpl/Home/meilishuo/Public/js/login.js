$(function(){
	//登录
	$(".loginBtn").click(function(){
		var verify_post_action=$("#checkcode").attr("verify_post_action");
		var loginBtn_post_action=$(this).attr("loginBtn_post_action");
		var loginBtn_post_location=$(this).attr("loginBtn_post_location");
		var uname=$.trim($("#mlsUser").val());
		var pwd=$.trim($("#mlsPass").val());
		var referer=$.trim($("#referer").val());
		var verification_login=$.trim($("#checkcode").val());
		
		if($("#savestate").attr("checked")=="checked"){
			var cookie=true;
		}else{
			var cookie=false;
		}
		if(uname==''){
			$(".loginErrorMessage").html("请输入用户名");
		}else if(pwd==''){
			$(".loginErrorMessage").html("请输入密码");
		}else if(verification_login==''){
			$(".loginErrorMessage").html("请输入验证码");
		}else{
			$.post(verify_post_action,{verification:verification_login},function(data){
				if(data=="true"){
					$(".err_chk").hide();
					$.post(loginBtn_post_action,{uname:uname,pwd:pwd,cookie:cookie,referer:referer},function(data){ 
						if(data.data=="yes"){
							location.href=loginBtn_post_location;
						}else if(data.info=='re'){
							$(".loginErrorMessage").html("");
							location.href=data.data;
						}else if(data.data=='no'){
							$(".loginErrorMessage").html("该用户禁止登录");
							return false;
						}else if(data.data=='verify'){
							$(".loginErrorMessage").html("该用户正处于审核状态");
							return false;
						}else{
							$(".loginErrorMessage").html("用户名或密码错误");
							return false;
						}
						$("#mlsUser").val('');
						$("#mlsPass").val('');
						$(".loginErrorMessage").html("");		
						$(".loginBtn").hide();
						$("#loading").show();
					},'json');
				}else if(data=="false"){
					$(".loginErrorMessage").html("验证码错误");
				}
			});
		}
	});
})