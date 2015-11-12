$(function(){
	//注册信息验证
	var check_uname_email=$("#check_post_action").val();
	//邮箱地址验证
	$("#mlsEmail").focus(function(){
		if($(this).val()=='电子邮箱'){
			$(this).val("");
		}
	})
	$("#mlsEmail").blur(function(){
		if($(this).val()==''){
			$(this).val("电子邮箱");
		}
		var email=$.trim($(this).val());
		if(email=="" || email=='电子邮箱'){
			$("#err_emailml").addClass("bad").html("请填写邮件地址");
		}else{
			var emailbool=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]+){1,2})$/.test(email);
			if(!emailbool){
				$("#err_emailml").addClass("bad").html("邮件格式错误！");
			}else{
				$.post(check_uname_email,{email:email},function(data){
					if(data=="emailExist"){
						$("#err_emailml").addClass("bad").html("该邮件已存在！");
					}else{
						$("#err_emailml").addClass("good").removeClass("bad").html("");
						$("#msgemail").hide();
					}
				});
			}
		}
	});
	//用户名验证
	$("#mlsName").focus(function(){
		if($(this).val()=='用户名'){
			$(this).val("");
		}
	})
	$("#mlsName").blur(function(){
		if($(this).val()==''){
			$(this).val('用户名');
		}
		var uname=$.trim($("#mlsName").val());
		if(uname=="" || uname=='用户名'){
			$("#err_nameml").addClass("bad").html("请填写用户名！");
		}else{
			var uanmebool=/(([\u4E00-\u9FA5]){2,10})|(([a-zA-Z0-9]){4,20})|(([\u4E00-\u9FA5-a-zA-Z0-9]){3,20})/.test(uname);
			if(!uanmebool){
				$("#err_nameml").addClass("bad").html("4-20位中英文或数字!");
			}else{
				$.post(check_uname_email,{uname:uname},function(data){
					if(data=="nameExist"){
						$("#err_nameml").addClass("bad").html("该昵称已存在！");
					}else{
						$("#err_nameml").addClass("good").removeClass("bad").html("");
						$("#msgnickname").hide();
						//$(".err_ulike").html("<img src='/App/Tpl/Home/mogujie/Public/img/ok_icon.png'>");
					}
				});
			}
		}
		
	});
	$("#mlsPass").focus(function(){
		$(this).hide();
		$("#vmPass").css('display','inline').focus();
	})
	$("#vmPass").blur(function(){
		if($(this).val()==''){
			$(this).hide();
			$("#mlsPass").css('display','inline');
		}
		var pwd=$.trim($(this).val());
		if(pwd==""){
			$("#err_passwordml").addClass("bad").html("请输入密码！");
		}else{
			var pwdbool=/^[0-9a-zA-Z]{6,16}$/.test(pwd);
			if(!pwdbool){
				$("#err_passwordml").addClass("bad").html("6-16英文和数字！");
			}else{
				$("#err_passwordml").addClass("good").removeClass("bad").html("");
			}
			
		}
	});
	$("#vmConfirmPass").focus(function(){
		$(this).hide();
		$("#vmPassSure").css('display','inline').focus();
	})
	$("#vmPassSure").blur(function(){
		if($(this).val()==''){
			$(this).hide();
			$("#vmConfirmPass").css('display','inline');
		}
		var pwd=$.trim($("#vmPass").val());
		var pwdSure=$("#vmPassSure").val();
		if(pwdSure==''){
			$("#err_rstpasswordml").addClass("bad").html("请重复输入一次密码");
		}else if(pwdSure!=pwd){
			$("#err_rstpasswordml").addClass("bad").html("两次输入密码不同");
		}else{
			$("#err_rstpasswordml").addClass("good").removeClass("bad").html("");
		}
	});
	$("#checkcode").focus(function(){
		if($(this).val()=='验证码'){
			$(this).val('');
		}
	})
	$("#checkcode").blur(function(){
		if($(this).val()==''){
			$(this).val('验证码');
		}
		var verify_post_action=$(this).attr("verify_post_action");
		var verification=$.trim($(this).val());
		if(verification=='' || verification=='验证码'){
			$("#err_checkml").addClass("bad").html("请输入验证码！");
			return false;
		}
		$.post(verify_post_action,{verification:verification},function(data){
			if(data=="true"){
				$("#err_checkml").addClass("good").removeClass("bad").html("");
			}else if(data=="false"){
				$("#err_checkml").addClass("bad").html("验证码输入错误！");
			}
		});
	});
	//注册新用户
	$("#agreement").click(function(){
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

			var pwd=$.trim($("#vmPass").val());
			var uname=$.trim($("#mlsName").val());
			var email=$.trim($("#mlsEmail").val());
			var sex=$("input[name='sex']:checked").val();
			var age=$("input[name='age']:checked").val();

			var unameclass=$("#err_nameml").attr("class");
			var emailclass=$("#err_nameml").attr("class");
			var pwdclass=$("#err_passwordml").attr("class");
			var pwdSureclass=$("#err_rstpasswordml").attr("class");
			var verifyclass=$("#err_checkml").attr("class");
			if(unameclass=="good" && emailclass=="good" && pwdclass=="good" && pwdSureclass=="good" && verifyclass=="good"){
				$.post(registerbtn_post_action,{uname:uname,pwd:pwd,email:email,sex:sex,age:age},function(data){
					if(data=="registerOK"){
						$("#mlsName").val("");
						$("#mlsEmail").val("");
						$("#vmPass").val("");
						$("#vmPassSure").val("");
						$("#checkcode").val("");
						location.href=registerbtn_post_location;
						$(".registerBtn").hide();
						$("#loading").show();
					}
				});
			}
		});
	}
})