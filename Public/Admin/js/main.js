
/* 左侧菜单展开状态 */
MLB_W=0
/* 左侧菜单子菜单展开状态 */
MLB_W_R=0

/*左侧菜单变化状态*/
Menu_status = 0
/*点击控件ID*/
Menu_status_ID = 0


$(document).ready(function(){
	//设置导航菜单第一个A为选中状态
	
	$("#header_nav a:first").addClass('selected');
	var menu_nav_id=$("#header_nav a:first").attr('id');
	$("#"+menu_nav_id+"_box").addClass('nav_selected');
	$("#"+menu_nav_id+"_box .left_box a:first li").addClass('selected');
	$("#"+menu_nav_id+"_box .right_box dl:first").show();

	initTab();

	function initTab(){
		//设置全部A为非选中
		$('#header_nav a').removeClass('selected');
		var first = $("#header_nav a:first");
		//设置当前A为选中
		first.addClass('selected');
		//获取当前A的ID
		var menu_nav_id=first.attr('id');
		var menu_nav_type=first.attr('menuType');
		var menu_nav_title=first.attr('menuTitle');
		var menu_nav_url=first.attr('menuUrl');
		var menu_nav_mc=first.attr('menuMC');
		var menu_nav_icon=first.attr('menuIcon');


		if( 1 == menu_nav_type){
			//设置实时监控按钮界面
			//修改左侧菜单的大小
			if(Menu_status_ID != menu_nav_id)
			{
				Menu_status = 0;
				Menu_status_ID = menu_nav_id;
			}
			if(Menu_status==0){
				$('#Main_Layout_West').hide();
				$('#Main_Layout_West').panel('minimize').panel('refresh');
				$('#Main_Layout_Box').layout('panel', 'west').panel('resize',{width:0});
				//重载布局结构
				$('#Main_Layout_Box').layout('resize');
				//隐藏子菜单
				UpdateTabs(menu_nav_mc,menu_nav_title,menu_nav_url, menu_nav_icon);
				Menu_status=1;
			}
		}
		else{
			Menu_status=0;
			$('#Main_Layout_West').show();
			$('#Main_Layout_West').panel('maximize').panel('refresh');
			$('#Main_Layout_West').panel('restore').panel('refresh');
			$('#Main_Layout_Box').layout('panel', 'west').panel('resize',{width:240});

			//重载布局结构
			$('#Main_Layout_Box').layout('resize');



			//$("#Main_Layout_Box").layout('add',temp);

			//移除全部nav_box的nav_selected
			$(".nav_box").removeClass('nav_selected');
			//添加关联nav_box的class
			$("#"+menu_nav_id+"_box").addClass('nav_selected');
			//移除全部left_box中的li的selected
			$('.left_box li').removeClass('selected');
			//给第一个li赋值
			$("#"+menu_nav_id+"_box .left_box a:first li").addClass('selected');
			//移除全部dl显示
			$(".right_box dl").hide();
			//让第一个dl显示
			$("#"+menu_nav_id+"_box .right_box dl:first").show();
		}
	}
	
	//当顶部导航菜单被点击时触发
	$('#header_nav a').click(function(){
		//设置全部A为非选中
		$('#header_nav a').removeClass('selected');
		//设置当前A为选中
		$(this).addClass('selected');
		//获取当前A的ID
		var menu_nav_id=$(this).attr('id');
		var menu_nav_type=$(this).attr('menuType');
		var menu_nav_title=$(this).attr('menuTitle');
		var menu_nav_url=$(this).attr('menuUrl');
		var menu_nav_mc=$(this).attr('menuMC');
		var menu_nav_icon=$(this).attr('menuIcon');
        //	1.需要隐藏全部的左侧菜单
		if( 1 == menu_nav_type){
			//设置实时监控按钮界面
			//修改左侧菜单的大小
			if(Menu_status_ID != menu_nav_id)
			{
				Menu_status = 0;
				Menu_status_ID = menu_nav_id;
			}

			if(Menu_status==0){
				$('#Main_Layout_West').hide();
				$('#Main_Layout_West').panel('minimize').panel('refresh');
				$('#Main_Layout_Box').layout('panel', 'west').panel('resize',{width:0});
				//重载布局结构
				$('#Main_Layout_Box').layout('resize');
				//隐藏子菜单
				UpdateTabs(menu_nav_mc,menu_nav_title,menu_nav_url, menu_nav_icon);
				Menu_status=1;
			}
		}
		else{
			// menu_nav_type =0菜单显示部分或全部





			//$("#Main_Layout_Box").layout('add',temp);

			//移除全部nav_box的nav_selected
			$(".nav_box").removeClass('nav_selected');
			//添加关联nav_box的class
			$("#"+menu_nav_id+"_box").addClass('nav_selected');
			//移除全部left_box中的li的selected
			$('.left_box li').removeClass('selected');
			//给第一个li赋值
			$("#"+menu_nav_id+"_box .left_box a:first li").addClass('selected');
			//移除全部dl显示
			$(".right_box dl").hide();
			//让第一个dl显示
			$("#"+menu_nav_id+"_box .right_box dl:first").show();

			//1.获取leftbox下菜单数量

			var length = $("#"+menu_nav_id+"_box .left_box a").length
			console.log(length,"+",menu_nav_id);
			if(length!= 1){
				//2.为1时，隐藏leftbox
				$('.left_box').show();
				$('.right_box').css('left',80);
				Menu_status=0;
				$('#Main_Layout_West').show();
				$('#Main_Layout_West').panel('maximize').panel('refresh');
				$('#Main_Layout_West').panel('restore').panel('refresh');
				$('#Main_Layout_Box').layout('panel', 'west').panel('resize',{width:240});

				//重载布局结构
				$('#Main_Layout_Box').layout('resize');
			}else{
				//2.为1时，隐藏leftbox
				$('.left_box').hide();
				$('.right_box').css('left',0);

				Menu_status=0;
				$('#Main_Layout_West').show();
				$('#Main_Layout_West').panel('maximize').panel('refresh');
				$('#Main_Layout_West').panel('restore').panel('refresh');
				$('#Main_Layout_Box').layout('panel', 'west').panel('resize',{width:160});




				//重载布局结构
				$('#Main_Layout_Box').layout('resize');
			}



		}

	});
	/*当顶部的收缩按钮被点击以后*/
	// $('.justify').click(function(){
	// 	//如果收缩状态为0
	// 	if(MLB_W==0){
	// 		//修改左侧菜单的大小
	// 		$('#Main_Layout_Box').layout('panel', 'west').panel('resize',{width:80});
	// 		//重载布局结构
	// 		$('#Main_Layout_Box').layout('resize');
	// 		//隐藏子菜单
	// 		$('.right_box').hide()
	// 		//设置菜单状态为收缩
	// 		MLB_W=1
	// 	}else{
	// 	//如果收缩状态为1
	// 		//修改左侧菜单的大小
	// 		$('#Main_Layout_Box').layout('panel', 'west').panel('resize',{width:300});
	// 		//重载布局结构
	// 		$('#Main_Layout_Box').layout('resize');
	// 		//显示子菜单
	// 		$('.right_box').show()
	// 		//设置菜单状态为显示
	// 		MLB_W=0
	// 	}
	// });

	$('#userinfo').click(function (e) {

		$('#div_dialog_set').window('refresh',user_info_url);
		$('#div_dialog_set').window({
			title:"用户信息",
			width:750,
			height:420,
			top:(screen.height-750)/2,
			left:(screen.width-700)/2,
			resizable:false,
			collapsible:false,
			minimizable:false,
			maximizable:false
		});
		$('#div_dialog_set').window('open');
	});

	$('#setting').click(function (e) {

		$('#div_dialog_set').window('refresh',setting_url);
		$('#div_dialog_set').window({
			title:"设置",
			width:400,
			height:340,
			top:(screen.height)/6,
			left:(screen.width-400)/2,
			resizable:false,
			collapsible:false,
			minimizable:false,
			maximizable:false
		});
		$('#div_dialog_set').window('open');
	});
	
})


//存在BUG

//左侧选项卡点击后
function LeftMenu(id) {
	//如果左侧菜单被收缩
	if(MLB_W==1){
		//判断当前左侧菜单的ID是否为上次点击的ID
		if(MLB_W_R==id){
		//如果是
			//隐藏子菜单
			$('.right_box').hide()
			//设置点击状态为初始状态
			MLB_W_R=0;
		}else{
		//如果不是
			//显示子菜单
			$('.right_box').show()
			//设置点击状态为当前子菜单的ID
			MLB_W_R=id;
		}
	}
	//移除全部dl显示
	$(".right_box dl").hide();
	//让当前dl显示
	$("#right_"+id+"_box_menu").show();
	//移除全部dl显示
	$('.left_box li').removeClass('selected');
	//给当前选中的li增加class
	$("#left_"+id+"_nav_menu li").addClass("selected");
}

//右侧tabs的添加，跳转
function UpdateTabs(name,tit,url,icon) {
	//设置链接的icon
	if(icon!=''||icon!=null){
		icon='iconfont '+icon
	}
	//新建数组
	var strs= new Array();
	//将name分解为三段（添加链接时注意，第一个为模块，第二个为控制器，第三个为操作，例如Admin/Index/index）
	strs=name.split("/"); //字符分割 
	//设置控制器名称
	var model_name = strs[1];
	//判断选项卡是否存在
	if ($('#tabs_'+model_name).length>0) {
		//如果存在根据控制器名称获取选项卡
		index = $('#MainTabs').tabs('getTabIndex',$('#tabs_'+model_name));
		//选中上一步获取的选项卡
		$('#MainTabs').tabs('select',index)
		//获取选中选项卡的属性
		Selected_tabs=$('#MainTabs').tabs('getSelected')
		//创建一个空的对象，作为选项卡属性的添加对象
		options_s={}
		//设置选项卡的内容（iframe框架的url来自与传递的url参数）
		options_s.content='<iframe scrolling="yes" frameborder="0" src="'+url+'" style="width:100%;height:100%;"></iframe>';
		//设置选项卡样式
		options_s.bodyCls="tabs_box"
		//如果有tit存在，设置选项卡名称为tit的值
		if(tit!=''){
			options_s.title=tit
		}
		//如果有icon存在，设置选项卡图表为前面设置的icon的值
		if(icon!=''){
			options_s.iconCls=icon
		}
		//根据前面的属性设置当前选项卡的参数
		$('#MainTabs').tabs('update', {
			tab:Selected_tabs,//选中选项卡的对象
			options: options_s//前面的参数
		});
		//设置属性完成后更新当前选中的选项卡
		Selected_tabs.panel('refresh');
	} else {
		//创建一个空的对象，作为选项卡属性的添加对象
		options_s={}
		//根据控制器名称，设置选项卡的ID
		options_s.id='tabs_'+model_name
		//如果有tit存在，设置选项卡名称为tit的值
		if(tit!=''){
			options_s.title=tit
		}else{
			options_s.title='未知控制器'//如果tit不存在，设置选项卡title为未知控制器
		}
		//设置选项卡的内容（iframe框架的url来自与传递的url参数）
		options_s.content='<iframe scrolling="yes" frameborder="0" src="'+url+'" style="width:100%;height:100%;"></iframe>';
		//设置选项卡为可以关闭
		options_s.closable=true
		//设置选项卡样式
		options_s.bodyCls="tabs_box"
		//如果有icon存在，设置选项卡图表为前面设置的icon的值，如果不存在设置为默认值
		if(icon!=null){
			options_s.iconCls=icon
		}else{
			options_s.iconCls='iconfont icon-viewlist'
		}
		//设置属性完成后，新建一个选项卡
		$('#MainTabs').tabs('add', options_s);
	}
	
}


function Data_Remove(Data_from_url,Datagrid_data){
	$.messager.confirm('确定操作', '您正在要删除所选的记录吗？', function (flag) {
		if (flag){
			$.post(Data_from_url,{},function(res){
				if(!res.status){
					$.messager.show({title:'错误提示',msg:res.info,timeout:2000,showType:'slide'});
				}else{
					$.messager.show({title:'成功提示',msg:res.info,timeout:1000,showType:'slide'});
					$('#'+Datagrid_data).datagrid('reload');
					$('#'+Datagrid_data).treegrid('reload');
				}
			})
		}
	})
}
/* 刷新页面 */
function Data_Reload(Data_Box){
	$('#'+Data_Box).datagrid('reload');
	$('#'+Data_Box).treegrid('reload');
}



