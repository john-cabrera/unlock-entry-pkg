<script type="text/javascript">

/*
// ================================================================================
// 	Main Content
// ================================================================================
*/
	function CreateContent(){
		//left page
		go_App_Left = new sap.m.App({});
		go_App_Left.addPage(Page_Menu());

		//right page
		go_App_Right = new sap.m.App({});
		go_App_Right.addPage(Page_Right_LockEntry());
		go_App_Right.addPage(Page_Blank());

		

		go_Shell = new sap.m.Shell({ showLogout : false });

		go_SplitContainer = new sap.ui.unified.SplitContainer({ content: [go_App_Right], secondaryContent: [go_App_Left]});
		go_SplitContainer.setSecondaryContentWidth("230px");
		go_SplitContainer.setShowSecondaryContent(false);

		go_App = new sap.m.App({
			pages : [go_SplitContainer]
		});


		go_Shell.setApp(go_App);
		go_Shell.setAppWidthLimited(false);
		go_Shell.placeAt("content");


		$(document).ready(function () {
			$.get( "/admin/event/trace", { app: SelectedAppID, fn: "LOCK_ENT" ,at: "FN_ACCESS", evt: "Access Unlock Entries"});
			fn_get_lock_entries();
					
			//	fn_get_left_panel(SelectedAppID, function(data){
			//		//construct array
			//		for(var i=0; i < data.length; i++){
			//			gt_list.push({
			//				title           : data[i].FUNCTION_DESC,
			//				icon            : (data[i].ICON || '') ? data[i].ICON : "",
			//				funct           : data[i].FUNCTION,
			//				visible         : false
			//			});
			//		}
			//	
			//		fn_get_authorization(gt_list);
			//	
			//	});
			//	fn_get_invite_conf_valuehelp();
			//	
			//	$(window).resize(function() {
			//		fn_freeze_table_header();
			//	});

		});

	}


/*
// ================================================================================
// 	FREEZE HEADER FUNCTION
// ================================================================================
*/

	function fn_freeze_table_header(){

		var width = $(window).width();
		var height = $(window).height();

		//	gv_ScrWidth = width;
		//	gv_ScrHeight = height;
		//	
		//	//======================================================
		//	//9. LOCK ENTRIES
		//	//======================================================
		//	
		//	// change height of table container
		//		$('#LOCK_ENTRY_SCROLLCONTAINER').height(gv_ScrHeight - 140+"px");
		//	
		//	// create freeze header
		//		fn_freeze_headers_v2("LOCK_ENTRY_TABLE-listUl", "LOCK_ENTRY_SCROLLCONTAINER");
		//	
		//	
		//	fn_reflow_table_header("no_delay");

	}

	function fn_reflow_table_header(){

		var lv_delay_milli_seconds;
		if(typeof(lv_delay) == "string" && lv_delay == "no_delay"){
			lv_delay_milli_seconds = 0;
		} else {
			lv_delay_milli_seconds = 500;
		}

		setTimeout(function(){



			var lo_table = $('#LOCK_ENTRY_TABLE-listUl');
				lo_table.floatThead('reflow');

	

		},lv_delay_milli_seconds);
	}



	function fn_goto_initial_page(){

		go_SplitContainer.setShowSecondaryContent(false);
		go_App_Left.backToPage("PAGE_MENU");

		
		if(gv_from_account_upload == "X"){
			gv_default_menu = "AM_ACCMAIN";
			fn_prep_data_to_set_account_details('', gv_selected_user_id);
		}
		// if authorized on Account Maintenance then set page as default
		else if(fn_check_authorization("AM_ACCMAIN")){
			ui("LEFT_MENU_TEMPLATE-lv_menu_list-0").firePress();
			gv_default_menu = "AM_ACCMAIN";

			//start - 222369
			var lv_user_id = null;
			if ((lv_user_id = window.localStorage.getItem('USER_ROLE_USER_ID')) != null) {
				window.localStorage.removeItem('USER_ROLE_USER_ID')
				fn_prep_data_to_set_account_details('', lv_user_id);
			}
			//end

		}
		// else show whatever is the first menu item as default page
		else {
			var lt_items_with_custom_leftpanel = ['AM_ROLEMAIN','AM_CLNTADM','AM_CATALOG'];
			var lt_list_items = ui("lv_menu_list").getItems();
			for(var i=0; i < lt_list_items.length; i++){
				if(lt_list_items[i].getVisible() == true){
					// at first load the value of gv_default_menu is blank
					if(gv_selected_menu != "" && gv_selected_menu == gv_default_menu){
						window.location.href =MainPageLink;
					}

					gv_default_menu = ui("lv_menu_list").getItems()[i].getBindingContext().getProperty('funct');

					// if default page is a custom one - show blank page
					if(lt_items_with_custom_leftpanel.indexOf(gv_default_menu)){
						go_App_Right.to("PAGE_BLANK");
					} else {
						ui("LEFT_MENU_TEMPLATE-lv_menu_list-"+i).firePress();
					}
					break;
				}
			}

		}

	}

/*
// ================================================================================
// 	ROLE ASSIGNMENT- RIGHT PAGE
// ================================================================================
*/
	function Page_Menu(){

		var lv_menu_list = new sap.m.List("lv_menu_list",{});

		var lv_list_template = new sap.m.StandardListItem("LEFT_MENU_TEMPLATE",{
			title:"{title}",
			icon:"{icon}",
			//visible:true,
			visible:"{visible}",
			type: sap.m.ListType.Active,
			press:function(oEvent){

				gv_selected_menu = oEvent.getSource().getBindingContext().getProperty('funct');
				gv_selected_menu_id = oEvent.getSource();
				var lt_list_items = oEvent.getSource().getParent().getItems();
				for(var i=0 ; i < lt_list_items.length ; i++){
					lt_list_items[i].removeStyleClass('class_selected_list_item');
					//$('#LEFT_MENU_TEMPLATE-lv_menu_list-3').removeClass('class_selected_list_item');
				}


			}
		});

		var lv_Page  = new sap.m.Page("PAGE_MENU",{}).addStyleClass('sapUiSizeCompact');

		var lv_header  = new sap.m.Bar({
				enableFlexBox: false,
				contentLeft:[ new sap.m.Label({text:"Action"})]

		});


		lv_Page.setCustomHeader(lv_header);
		lv_Page.addContent(lv_menu_list);
		return lv_Page;

	}

/*
// ================================================================================
// 	Acct MAINTENACE- RIGHT PAGE
// ================================================================================
*/

	function Page_Blank(){

		var lv_crumbs = new sap.m.HBox({
			items: [
				new sap.m.Label({text:"Home" }).addStyleClass("CRUMBS_PREVIOUS_PAGE"),
				new sap.m.Label({text:">" }).addStyleClass("CRUMBS_PREVIOUS_PAGE"),
				new sap.m.Label({text:"Unlock Entries" }).addStyleClass("CRUMBS_CURRENT_PAGE"),
			]

		});

		var lv_page = new sap.m.Page("PAGE_BLANK", {
				customHeader:[
					new sap.m.Bar({
					contentLeft:  [

					//new sap.m.Image({ src:"../image/logo/logo.png", width:"100px"})
					new sap.m.Button({ icon:"sap-icon://nav-back",
					press:function(oEvt){
						window.location.href =MainPageLink;
					} }),
					new sap.m.Button({icon:"sap-icon://menu2",
						press:function(){
							go_SplitContainer.setSecondaryContentWidth("230px");
							if(!go_SplitContainer.getShowSecondaryContent()){
								go_SplitContainer.setShowSecondaryContent(true);
							} else {
								go_SplitContainer.setShowSecondaryContent(false);
							}
						}
					}),
					new sap.m.Image({src: logo_path}),
					],
					contentMiddle:[new sap.m.Label({text:"Account Management"})],
					contentRight:[
						fn_help_button(SelectedAppID,"AM_ACCMAIN"),
						new sap.m.Button({
							 icon: "sap-icon://home",
							 tooltip: "Home",
							 press: function(){
							   window.location.href = MainPageLink;
							 }
						})
					]
				})
				]
			}).addStyleClass('sapUiSizeCompact');

			lv_page.addContent(lv_crumbs);


			return lv_page;

	}

/*
// ================================================================================
// Lock Entry
// ================================================================================
*/

	function Page_Right_LockEntry(){

		var lv_Page  = new sap.m.Page('LOCK_ENTRY_PAGE',{}).addStyleClass('sapUiSizeCompact');

		var lv_header = new sap.m.Bar({
			enableFlexBox: false,
			contentLeft:[
				new sap.m.Button({ icon:"sap-icon://nav-back",
					press:function(oEvt){
						window.location.href = MainPageLink;
					} 
				}),
				/*
				new sap.m.Button({icon:"sap-icon://menu2",
					press:function(){
						go_SplitContainer.setSecondaryContentWidth("250px");
						if(!go_SplitContainer.getShowSecondaryContent()){
							go_SplitContainer.setShowSecondaryContent(true);
						} else {
							go_SplitContainer.setShowSecondaryContent(false);
						}

						fn_reflow_table_header();
					}
				}),
				*/
				new sap.m.Image({src: logo_path}),
				],
			contentMiddle:[gv_Lbl_NewPrdPage_Title = new sap.m.Label({text:"My Locked Entries"})],

			contentRight:[

				fn_help_button(SelectedAppID,"LOCK_EDITOR"),
				new sap.m.Button({
					 icon: "sap-icon://home",
					 tooltip: "Home",
					 press: function(){

					 	window.location.href = MainPageLink;
					 }
				})
			]

		});

		var lv_crumbs = new sap.m.Breadcrumbs({
			currentLocationText:"My Locked Entries",
			links:[
				new sap.m.Link({
					text:"Home",
					press:function(oEvt){
						fn_click_breadcrumbs("HOME");
					}
				}),
				new sap.m.Link({
					text:"Unlock Entries",
					press:function(oEvt){

					}
				})
			]
		}).addStyleClass('breadcrumbs-padding');


		var lv_Lbl_Width = "100px";
		var lv_Input_Width= "220px";

		var lo_SubHeader= new sap.m.Bar({
			contentLeft:[
				new sap.m.Label({text:"Class:", width:lv_Lbl_Width}),
				new sap.m.SearchField("LOCK_ENTRY_SEARCH_LOCK",{
					showSearchButton :false,
					placeholder:"Search...",
					width:lv_Input_Width,
					liveChange:function(oEvt){

						//var lv_value = oEvt.getSource().getValue().trim();

						//fn_search_lockentries(lv_value, "LOCK_CLASS")
						fn_search_lockentries();

					},
					search:function(oEvt){

						//var lv_value = oEvt.getSource().getValue().trim();
                        //
						//fn_search_lockentries(lv_value, "LOCK_CLASS")
						fn_search_lockentries();

					}
				}),
				new sap.m.Label({text:"Lock Mode:", width:lv_Lbl_Width}),
				new sap.m.SearchField("LOCK_ENTRY_SEARCH_MODE",{
					showSearchButton:false,
					placeholder:"Search...",
					width:lv_Input_Width,
					liveChange:function(oEvt){

						//var lv_value = oEvt.getSource().getValue().trim();
                        //
						//fn_search_lockentries(lv_value, "MODE")

						fn_search_lockentries();

					},
					search:function(oEvt){

						//var lv_value = oEvt.getSource().getValue().trim();
                        //
						//fn_search_lockentries(lv_value, "MODE")

						fn_search_lockentries();

					}
				}),
				new sap.m.Label({text:"Username:", width:lv_Lbl_Width}),
				new sap.m.SearchField("LOCK_ENTRY_SEARCH_USERNAME",{
					showSearchButton:false,
					placeholder:"Search...",
					width:lv_Input_Width,
					liveChange:function(oEvt){

						var lv_value = oEvt.getSource().getValue().trim();

						fn_search_lockentries(lv_value, "USERNAME")

					},
					search:function(oEvt){

						var lv_value = oEvt.getSource().getValue().trim();

						fn_search_lockentries(lv_value, "USERNAME")

					}
				})
			]
		}).addStyleClass('class_transparent_bar box_shadow_hide');

		var lv_dialog_confirm= new sap.m.Dialog("CONFIRM_LOCKENTRY_DIALOG",{
			title: "Confirmation",
			beginButton: new sap.m.Button({
				text:"Ok",
				type:"Accept",
				icon:"sap-icon://accept",
				press:function(oEvt){

					var lt_selecteditems = ui('LOCK_ENTRY_TABLE').getModel().getData();

					var lt_data = [];

					//for(var i=0; i < lt_selecteditems.length; i++){

						lt_data.push({

							OBJID 			:lt_selecteditems[gvv_index].OBJID,
							LOCK_CLASS 		:lt_selecteditems[gvv_index].LOCK_CLASS,
						});
					//}

					fn_remove_lockentry_multiple(lt_data);

					oEvt.getSource().getParent().close();
				}

			}),
			endButton:new sap.m.Button({
				text:"Cancel",
				type:"Reject",
				icon:"sap-icon://decline",
				press:function(oEvt){

					oEvt.getSource().getParent().close();
				}

			}),
			content:[

					new sap.m.HBox({
						items:[
							new sap.m.Label({text:"Confirm to delete lock entries?"})
						]

					})

					]

		}).addStyleClass('sapUiSizeCompact');



		var lv_scrollcontainer = new sap.ui.table.Table("LOCK_ENTRY_TABLE", {
					visibleRowCountMode:"Auto",
					selectionMode:"None",
					enableCellFilter: true,
					toolbar:[
						new sap.m.Toolbar({
							//design:"Solid",
							content:[
								new sap.m.Label("LOCK_ENTRY_LABEL",{text:"My Locked Entries"}),
								new sap.m.ToolbarSpacer(),
								new sap.m.Button({
									icon:"sap-icon://refresh",
									press:function(){
										fn_get_lock_entries();
									}
								}),
							]
						}),
					],
					columns:[
						new sap.ui.table.Column("",{
							hAlign:"Left",
							width:"250px",
							label 	: new sap.m.Label({text:"Username"}),
							template: new sap.m.Text({text:"{USERNAME}",maxLines:1,tooltip:"{USERNAME}"}),
							sortProperty:"USERNAME",
							filterProperty:"USERNAME",
							autoResizable:true
						}),
						new sap.ui.table.Column("",{
							hAlign:"Left",
							//width:"200px",
							label 	: new sap.m.Label({text:"Class"}),
							template: new sap.m.Text({text:"{LOCK_CLASS}",maxLines:1,tooltip:"{LOCK_CLASS}"}),
							sortProperty:"LOCK_CLASS",
							filterProperty:"LOCK_CLASS",
							autoResizable:true
						}),
						new sap.ui.table.Column("",{
							hAlign:"Left",
							//width:"100px",
							label 	: new sap.m.Label({text:"Obj ID"}),
							template: new sap.m.Text({text:"{OBJID}"}),
							sortProperty:"OBJID",
							filterProperty:"OBJID",
							autoResizable:true
						}),
						new sap.ui.table.Column("",{
							hAlign:"Left",
							//width:"120px",
							label 	: new sap.m.Label({text:"Lock Mode"}),
							template: new sap.m.Text({text:"{MODE}"}),
							sortProperty:"MODE",
							filterProperty:"MODE",
							autoResizable:true
						}),
						new sap.ui.table.Column("",{
							hAlign:"Left",
							width:"120px",
							label 	: new sap.m.Label({text:"Creation Date"}),
							template: new sap.m.Text({text:"{CREATION_DATE}"}),
							sortProperty:"CREATION_DATE",
							filterProperty:"CREATION_DATE",
							autoResizable:true
						}),
						new sap.ui.table.Column("",{
							hAlign:"Left",
							//width:"120px",
							label 	: new sap.m.Label({text:"Creation Time"}),
							template: new sap.m.Text({text:"{CREATION_TIME}"}),
							sortProperty:"CREATION_TIME",
							filterProperty:"CREATION_TIME",
							autoResizable:true
						}),
						new sap.ui.table.Column("",{
							hAlign:"Left",
							width:"50px",
							label 	: new sap.m.Label({text:""}),
							template: new sap.m.Button({
										icon:"sap-icon://delete",
										press:function(oEvt){

											var lt_selecteditems = ui('LOCK_ENTRY_TABLE').getModel().getData();
											var lo_index = String(oEvt.getSource().getBindingContext().getPath());
											gvv_index = lo_index.split("/")[1];

											var lt_data = [];

											if(lt_selecteditems.length > 0){

												ui('CONFIRM_LOCKENTRY_DIALOG').open();

											}else{

												fn_show_notification_message("Please select at least 1 record.")
											}
										}
							})
						}),

						//new sap.m.Column({header: new sap.m.Link({text:"Username"}),hAlign:"Left",width:"100px"}),
						//new sap.m.Column({header: new sap.m.Link({text:"Class"}),hAlign:"Left", width:"100px"}),
						//new sap.m.Column({header: new sap.m.Link({text:"Obj ID"}),hAlign:"Left",width:"100px"}),
						//new sap.m.Column({header: new sap.m.Link({text:"Lock Mode"}),hAlign:"Left",width:"100px"}),
						//new sap.m.Column({header: new sap.m.Link({text:"Creation Date"}),hAlign:"Left", width:"100px"}),
						//new sap.m.Column({header: new sap.m.Link({text:"Creation Time"}),hAlign:"Left",width:"100px" })
					]
				});
			
		var lv_template =   new sap.m.ColumnListItem("LOCK_ENTRY_TEMPLATE",{
			type:"Active",
			cells:[
				new sap.m.Text({text:"{USERNAME}"}),
				new sap.m.Text({text:"{LOCK_CLASS}"}),
				new sap.m.Text({text:"{OBJID}"}),
				new sap.m.Text({text:"{MODE}"}),
				new sap.m.Text({text:"{CREATION_DATE}"}),
				new sap.m.Text({text:"{CREATION_TIME}"})
			]

		});


		lv_Page.setCustomHeader(lv_header);
		lv_Page.addContent(lv_crumbs);
		//lv_Page.addContent(lo_SubHeader);
		//lv_Page.addContent(lo_lockentry_bar);
		lv_Page.addContent(lv_scrollcontainer);

		return lv_Page;

	}

	function fn_GET_USERS(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		gt_USER_DATA	   = [];
		gt_USER_DATA_INDEX = [];

		$.get("/admin/users/management/system", function(result, status){

			for(i=0; i<result.dataitem.length; i++){


				var lv_icon		= "sap-icon://accept";
				var lv_state	= "Success";

				switch(result.dataitem[i].status){
					case "01":
						/*lv_icon	 = "sap-icon://accept";
						lv_state = "Success";*/
						result.dataitem[i].state =  "Success";
						result.dataitem[i].icon = "sap-icon://accept";
					break;

					case "02":
						/*lv_icon	 = "sap-icon://warning";
						lv_state = "Error";*/
						result.dataitem[i].state ="Error";
						result.dataitem[i].icon	 =	"sap-icon://warning";
					break;

					case "04":
						/*lv_icon	 = "sap-icon://locked";
						lv_state = "Error";*/
						result.dataitem[i].state ="Error";
						result.dataitem[i].icon	 = "sap-icon://locked";
					break;

				}
				gt_USER_DATA.push(result.dataitem[i]);
				gt_USER_DATA_INDEX[result.dataitem[i].username] = result.dataitem[i];


			}

			busy_diag.close();

			sap.ui.getCore().byId('GO_TABLE_SYSACCT_MAINT').destroyItems();
			var arr = JSON.stringify(gt_USER_DATA);
				arr = JSON.parse(arr);

			var gt_sorters = {username:"asc",created_at:"desc",updated_at:"desc"};
				mksort.sort(arr, gt_sorters);

			var model = new sap.ui.model.json.JSONModel();
				model.setData(arr);

			var lo_table = $('#GO_TABLE_SYSACCT_MAINT-listUl');
				lo_table.floatThead('reflow');

			sap.ui.getCore().byId('GO_TABLE_SYSACCT_MAINT').setModel(model).bindAggregation("items", {
					path: "/",
					template: sap.ui.getCore().byId('GO_TABLE_SYSACCT_MAINT_TEMP')
			});

			sap.ui.getCore().byId('LABEL_ITEM_SYSUSER_MTN').setText("Items ("+arr.length+")");

			setTimeout(function(){fn_freeze_table_header();}, 1000);

			if(ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle()!=""){
			  fn_BIND_ACCT_DETAIL_DISPLAY(ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle())
			}

		});

	}


var callService = {


	postData : function (url,data,callBack){
		// console.log(data);
		var self = this;
		var busy_diag = fn_show_busy_dialog('');
		busy_diag.open();
		//var token = ;
		//var data = {username:user_id,password:user_pass,email:"noraimy.abdrahim@e-oasia.com",_token:"{!! csrf_token() !!}"};
		//var data = {username:user_id,password:user_pass,_token:"{!! csrf_token() !!}"};
		fn_ajax_call(
		 	url,
		 	"POST",
		 	data,
		 	function (response) {
				busy_diag.close();
				callBack(response);
				console.log(response);
			},
			function (response) {
				busy_diag.close();
				console.log(response);
				callBack(response);
		 	},
			"json"
		);


	},

	getData : function (url,callBack){
			var self = this;

			$.get(url, function(result, status){
				callBack(result);
			});
	},




}


var processData = {


	postData : function (url,data,callBack){
		//console.log(data);



	},

	getData : function (url,callBack){
			var self = this;


	},




}

	function Page_Right_Invite_Config(){

		var lo_page = new sap.m.Page("PAGE_INVITE_CONF_MAINTENANCE_RIGHT", {
    
			customHeader: new sap.m.Bar({
				contentLeft: [
					new sap.m.Button({ icon:"sap-icon://nav-back",
						press:function(oEvt){ 

							if(gv_mode == "edit"){
								gv_flag_cancel_from = "EDIT_BACK";
								ui("DIALOG_CONFIRM_CANCEL_CHANGES").open();
							}
							else {

								fn_goto_initial_page();
							}
						} 

					}),
					new sap.m.Button({icon:"sap-icon://menu2",
						press:function(){
							go_SplitContainer.setSecondaryContentWidth("230px");
							if(!go_SplitContainer.getShowSecondaryContent()){
								go_SplitContainer.setShowSecondaryContent(true);
							} else {							
								go_SplitContainer.setShowSecondaryContent(false);
							}
							
							setTimeout(function(){	
								var lo_table = $('#GO_TABLE_INVITE_CONF_MAINTENANCE-listUl');
								lo_table.floatThead('reflow');	
							}, 500);
							
						}
					}), 
					new sap.m.Image({src: logo_path}),
				],
				contentRight: [
					 fn_help_button(SelectedAppID,"AM_INVITE_CONF"),
					 new sap.m.Button("",{
						 icon: "sap-icon://home",
						 press:function(){

							if(gv_mode == "edit"){
								gv_flag_cancel_from = "EDIT_HOME";
								ui("DIALOG_CONFIRM_CANCEL_CHANGES").open();
							}
							else{
								window.location.href = MainPageLink;
							}
							
						}
					}) 
				],
				contentMiddle: [ new sap.m.Label({text: "Invite Configuration"}) ],
			}),

			content: [
				new sap.m.Breadcrumbs({
					currentLocationText:"Invite Configuration",
					links:[
						new sap.m.Link({
							text:"Home",
							press:function(oEvt){
								fn_click_breadcrumbs("HOME");
							}
						}),
						new sap.m.Link({
							text:"Account Management",
							press:function(oEvt){
								
							}
						})
					]
				}).addStyleClass('breadcrumbs-padding'),
				new sap.m.Bar({
					visible: false,
					enableFlexBox: false,
					contentLeft: [
						new sap.m.HBox({
							width: "100%",
							items: [
								new sap.m.SearchField("INVITE_CONF_MAINTENANCE_SEARCH",{ 
									layoutData: new sap.m.FlexItemData({growFactor: 2}),
									placeholder: "Search...",
									liveChange: function(oEvent){
										
										sap.ui.getCore().byId("INVITE_CONF_MAINTENANCE_SEARCH").destroyItems();
										var oFilter = new sap.ui.model.Filter(sap.ui.getCore().byId("SEARCH_SELECT_INVITE_CONF_MAINTENANCE").getSelectedItem().getKey(), sap.ui.model.FilterOperator.Contains, oEvent.getSource().getValue());											
										fn_bind_moblie_maint_header("","SEARCH",oFilter);
									},
									search:function(){
										ui("INVITE_CONF_MAINTENANCE_SEARCH").fireLiveChange();
									}
								}),
								new sap.m.Select("SEARCH_SELECT_INVITE_CONF_MAINTENANCE",{
									type: sap.m.SelectType.Default,
									items: [
										new sap.ui.core.Item({text: "User ID", key: "USER_ID"}),
										new sap.ui.core.Item({text: "Display Name", key: "DISPLAY_NAME"}),
									],
									change: function(){
										ui("INVITE_CONF_MAINTENANCE_SEARCH").fireLiveChange();
									}
								}).addStyleClass("class_select_search_bar")
							]
						}),
					],
				}),
				
				new sap.ui.table.Table("GO_TABLE_INVITE_CONF",{
					visibleRowCountMode:"Auto",
					selectionMode:"None",
					enableCellFilter: true,
					toolbar:[
						new sap.m.Toolbar({
							design:"Solid",
							style:"Clear",
							content:[
								new sap.m.Label("GO_TABLE_INVITE_CONF_LABEL",{
									text:"Invite Config List (0)"
								}),
								new sap.m.ToolbarSpacer(),
								new sap.m.Button("INVITE_CONF_EDIT",{
									visible:true,
									type:"Transparent",
									icon: "sap-icon://edit",
									press:function(){

										fn_set_invite_conf_data_mode(gt_invite_conf,"edit",function(data){
											fn_bind_invite_conf(data);
										});
										setTimeout(function(){
											fn_bind_invite_conf_valuehelp();
										},500);
										lo_role_auth_dialog.mode = "edit";
										gv_mode = "edit";
									}
								}),
								new sap.m.Button("INVITE_CONF_ADD",{
									visible:false,
									type:"Transparent",
									icon: "sap-icon://add",
									press:function(){

										lo_add_new_invite_conf.open_dialog();
									}
								}),
								new sap.m.Button("INVITE_CONF_SAVE",{
									visible:false,
									type:"Transparent",
									icon: "sap-icon://save",
									press:function(){

										lo_confirmation_dialog.action = "save";
										lo_confirmation_dialog.title = "Save Updated Invite Config";
										lo_confirmation_dialog.check_data();
									}
								}),
								new sap.m.Button("INVITE_CONF_DECLINE",{
									visible:false,
									type:"Transparent",
									icon: "sap-icon://decline",
									press:function(){

										lo_confirmation_dialog.action = "discard";
										lo_confirmation_dialog.title = "Discard Changes";
										lo_confirmation_dialog.confirmation();
										lo_role_auth_dialog.mode = "display";
										gv_mode = "display";
									}
								})
							]
						})
					],					
					columns : [
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Invite ID"}),
							template: new sap.m.Input({value:"{INVITE_ID}",editable:"{INVITE_ID_EDITABLE}"}),
							sortProperty:"INVITE_ID",
							filterProperty:"INVITE_ID",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "300px",
							label 	: new sap.m.Label({text:"Description"}),
							template: new sap.m.Input({value:"{DESCRIPTION}",editable:"{INVITE_DESC_EDITABLE}"}),
							sortProperty:"DESCRIPTION",
							filterProperty:"DESCRIPTION",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "130px",
							label : new sap.m.Label({text:"Status"}),
							template: new sap.m.HBox({
								renderType : "Bare",
								items :[
									new sap.m.Select({
										selectedKey:"{STATUS}",
										name:"{STATUS_DESC}",
										visible:"{STATUS_VISIBLE_EDT}",
										width:"100%",
									}),
									new sap.m.Text({
										text:"{STATUS_DESC}",
										visible:"{STATUS_VISIBLE_DSP}"
									})
								]
							}),
							sortProperty:"STATUS_DESC",
							filterProperty:"STATUS_DESC",
							autoResizable:true
						}),
						new sap.ui.table.Column({

							hAlign:"Left",
							width : "130px",
							label 	: new sap.m.Label({text:"Expiry (Hours)"}),
							template: new sap.m.Input({
								value:"{EXPIRY_HR}",
								editable:"{EXPIRY_EDITABLE}",
								change : function(evt){

									var lv_id = evt.getSource().getId();
									var lo_index = String(evt.getSource().getBindingContext().getPath());
									var lv_index = lo_index.split("/")[1];
									var lv_control_model = evt.getSource().getParent().getModel().getData();
									var lv_value = evt.getSource().getValue();
									lv_control_model[lv_index].EXPIRY = Math.floor(lv_value * 60);
									evt.getSource().getParent().getModel().refresh();
								}
							}),
							sortProperty:"EXPIRY_HR",
							filterProperty:"EXPIRY_HR",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							visible:false,
							hAlign:"Left",
							width : "150px",
							label 	: new sap.m.Label({text:"Create Username"}),
							template: new sap.m.Input({value:"{CREATE_USERNAME}",editable:"{CRT_USERNAME_EDITABLE}"}),
							sortProperty:"CREATE_USERNAME",
							filterProperty:"CREATE_USERNAME",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "130px",
							label 	: new sap.m.Label({text:"Send Email"}),
							template: new sap.m.Switch({
								state : "{SEND_EMAIL_STATE}",
								enabled:"{SEND_EMAIL_ENABLED}",
								type:"AcceptReject",
								change : function(evt){

									var lv_id = evt.getSource().getId();
									var lo_index = String(evt.getSource().getBindingContext().getPath());
									var lv_index = lo_index.split("/")[1];
									var lv_control_model = evt.getSource().getParent().getModel().getData();
									var lv_state = evt.getSource().getState();
									if(lv_state == true){
										lv_control_model[lv_index].SEND_EMAIL = "X";
									}
									else{
										lv_control_model[lv_index].SEND_EMAIL = "";
									}
									evt.getSource().getParent().getModel().refresh();
								}
							}),
							// sortProperty:"DEVICE_PLATFORM",
							// filterProperty:"DEVICE_PLATFORM",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Object Type"}),
							template: new sap.m.HBox({
								renderType : "Bare",
								items :[
									new sap.m.Select({
										forceselection : false,
										selectedKey:"{MAIL_OBJ_TYPE}",
										name:"{MAIL_OBJ_TYPE}",
										visible:"{MAIL_OBJ_TYPE_VISIBLE_EDT}",
										width:"100%",
										change : function(evt){
											
											var lv_selected = evt.getSource().getSelectedKey();
											var lv_mail_obj_id_field = evt.getSource().getParent().getParent().getCells()[6].getItems()[0];
											lv_mail_obj_id_field.destroyItems();
											lv_mail_obj_id_field.setForceSelection(false);
											lv_mail_obj_id_field.setSelectedKey("");
											for(var i=0;i<gv_mail_obj_id_vhelp[lv_selected].length;i++){
												var lv_vhelp = new sap.ui.core.Item({
													
													key : gv_mail_obj_id_vhelp[lv_selected][i].ID,
													text : gv_mail_obj_id_vhelp[lv_selected][i].ID
												});
												lv_mail_obj_id_field.addItem(lv_vhelp);
												
											}
											var lv_mail_event_type_field = evt.getSource().getParent().getParent().getCells()[7].getItems()[0];
											lv_mail_event_type_field.destroyItems();
											lv_mail_event_type_field.setForceSelection(false);
											lv_mail_event_type_field.setSelectedKey("");
										}
									}),
									new sap.m.Text({
										text:"{MAIL_OBJ_TYPE}",
										visible:"{MAIL_OBJ_TYPE_VISIBLE_DSP}"
									})
								]
							}),
							sortProperty:"MAIL_OBJ_TYPE",
							filterProperty:"MAIL_OBJ_TYPE",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Object ID"}),
							template: new sap.m.HBox({
								renderType : "Bare",
								items :[
									new sap.m.Select({
										forceselection : false,
										selectedKey:"{MAIL_OBJ_ID}",
										name:"{MAIL_OBJ_ID}",
										visible:"{MAIL_OBJ_ID_VISIBLE_EDT}",
										width:"100%",
										change : function(evt){
											
											var lv_selected = evt.getSource().getSelectedKey();
											var lv_mail_event_type_field = evt.getSource().getParent().getParent().getCells()[7].getItems()[0];
											lv_mail_event_type_field.destroyItems();
											lv_mail_event_type_field.setForceSelection(false);
											lv_mail_event_type_field.setSelectedKey("");
											for(var i=0;i<gv_mail_event_type_vhelp[lv_selected].length;i++){
												var lv_vhelp = new sap.ui.core.Item({
													
													key : gv_mail_event_type_vhelp[lv_selected][i].ID,
													text : gv_mail_event_type_vhelp[lv_selected][i].ID
												});
												lv_mail_event_type_field.addItem(lv_vhelp);
											}
										}
									}),
									new sap.m.Text({
										text:"{MAIL_OBJ_ID}",
										visible:"{MAIL_OBJ_ID_VISIBLE_DSP}"
									})
								]
							}),
							sortProperty:"MAIL_OBJ_ID",
							filterProperty:"MAIL_OBJ_ID",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Event Type"}),
							template: new sap.m.HBox({
								renderType : "Bare",
								items :[
									new sap.m.Select({
										forceselection : false,
										selectedKey:"{MAIL_EVENT_TYPE}",
										name:"{MAIL_EVENT_TYPE}",
										visible:"{MAIL_EVENT_TYPE_VISIBLE_EDT}",
										width:"100%",
										
									}),
									new sap.m.Text({
										text:"{MAIL_EVENT_TYPE}",
										visible:"{MAIL_EVENT_TYPE_VISIBLE_DSP}"
									})
								]
							}),
							sortProperty:"MAIL_EVENT_TYPE",
							filterProperty:"MAIL_EVENT_TYPE",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "100px",
							label 	: new sap.m.Label({text:"Role Auth"}),
							template: new sap.m.Button({
								enable:"{ROLE_AUTH_ENABLED}",
								type:"Transparent",
								icon: "sap-icon://add-contact",
								press:function(oEvt){

									lo_role_auth_dialog.mode = gv_mode;
									lo_role_auth_dialog.source = oEvt.getSource();
									lo_role_auth_dialog.roles_vhelp = gv_roles_vhelp;
									lo_role_auth_dialog.status_vhelp = gv_invite_role_vhelp;
									lo_role_auth_dialog.data = gt_invite_roles;
									lo_role_auth_dialog.init();
									
								}
							}),
							// sortProperty:"",
							// filterProperty:"",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "50px",
							label 	: new sap.m.Label({text:""}),
							template: new sap.m.Button({
								// visible:false,
								type:"Transparent",
								icon: "sap-icon://delete",
								press:function(oEvt){
									
									// var lo_index = String(oEvt.getSource().getBindingContext().getPath());
									// var lv_index = lo_index.split("/")[1];
									// var lv_deleted_id = ui("GO_TABLE_INVITE_CONF").getModel().getData()[lv_index];

									// if(lv_deleted_id.id !== ""){
									// 	gt_delete_biz_partner.push({
									// 		"id":lv_deleted_id.id,
									// 		"INVITE_ID":lv_deleted_id.INVITE_ID
									// 	});
									// }

									// gt_invite_conf.splice(lv_index, 1);
									// //step 2 - assign new POSNR to these editable items
									// var lo_model = new sap.ui.model.json.JSONModel();
									// lo_model.setSizeLimit(gt_invite_conf.length);
									// lo_model.setData(gt_invite_conf);
									// ui('GO_TABLE_INVITE_CONF').setModel(lo_model).bindRows("/");
									// ui('GO_TABLE_INVITE_CONF_LABEL').setText("Partner ("+gt_invite_conf.length+")");
									// $('#PAGE_ACCT_MAINTENANCE-cont').animate({ scrollTop: 0 }, 100);\
									
									lo_confirmation_dialog.data = gt_invite_conf;
									lo_confirmation_dialog.source = oEvt.getSource();
									lo_confirmation_dialog.action = "delete";
									lo_confirmation_dialog.title = "Delete Record";
									lo_confirmation_dialog.delete_record();
								}
							}),
							// sortProperty:"MAIL_EVENT_TYPE",
							// filterProperty:"MAIL_EVENT_TYPE",
							autoResizable:true
						}),
					],
					// cellClick:function(oEvt){

					// 	var lv_bind = oEvt.getParameters().rowBindingContext;
					// 	console.log(lv_bind);
					// 	if(lv_bind != undefined){
					// 		var lv_invite_id = lv_bind.getProperty("INVITE_ID");
					// 		console.log(lv_invite_id);
					// 	}	
					// }
				})
			],
		}).addStyleClass('sapUiSizeCompact');

		var lo_add_new_invite_conf = {

			error : false,
			control : new sap.m.Dialog({}).addStyleClass("sapUiSizeCompact"),
			open_dialog : function(){

				var self = this;

				self.control.destroyContent();
				var lv_begin_button = new sap.m.Button({
					icon:"sap-icon://accept",
					press:function(evt){
						self.check_data();
						if(self.error == false){
							self.new_line();
							evt.getSource().getParent().close();
						}
					}
				});

				var lv_end_button = new sap.m.Button({icon:"sap-icon://decline",
					press:function(evt){

						evt.getSource().getParent().close();
					}
				});

				var lv_strip_msg = new sap.m.Panel({visible:false});
				var lv_content = new sap.m.Panel({
					content:[
						new sap.m.FlexBox({
							justifyContent:'Center',
							items:[
								new sap.m.VBox({
									alignItems:'Start',
									items:[

										new sap.m.Label({text: "Invite ID"}),
										new sap.m.Input({
											width : "450px",
											value : "",
											change:function(evt){

												evt.getSource().setValueState("None");
												self.check_data();
											}
										}),
										new sap.m.Label({text: "Description"}),
										new sap.m.Input({
											width : "450px",
											value : "",
											change:function(e){
												
											},
										}),

										new sap.m.Label({text: "STATUS"}),
										new sap.m.Select({
											width : "450px",
											selectedKey : "02",
											change:function(e){

											},
										}),

										new sap.m.Label({text: "Expiry (Hours)"}),
										new sap.m.Input({
											width : "450px",
											value : "",
											inputType : "Number",
											liveChange :function(e){

												var lv_value = e.getSource().getValue();
												lv_value = lv_value.replace(/\D/g,'');
												e.getSource().setValue(lv_value);
											},
											change: function(e){
												
											}
										}),

										new sap.m.Label({visible:false,text: "Create Username"}),
										new sap.m.Input({
											width:"450px",
											visible:false,
											change:function(evt){

											}
										}),

										new sap.m.Label({text: "Send Email"}),
										new sap.m.Switch({
											type:"AcceptReject",
											change : function(evt){

											}
										}),

										new sap.m.Label({text: "Object Type"}),
										new sap.m.Select({
											width:"450px",
											forceSelection : false,
											change:function(evt){
												
												var lv_selected = evt.getSource().getSelectedKey();
												var lv_mail_obj_id_field = evt.getSource().getParent().getItems()[15];
												lv_mail_obj_id_field.destroyItems();
												lv_mail_obj_id_field.setForceSelection(false);
												lv_mail_obj_id_field.setSelectedKey("");
												for(var i=0;i<gv_mail_obj_id_vhelp[lv_selected].length;i++){
													var lv_vhelp = new sap.ui.core.Item({
														
														key : gv_mail_obj_id_vhelp[lv_selected][i].ID,
														text : gv_mail_obj_id_vhelp[lv_selected][i].ID
													});
													lv_mail_obj_id_field.addItem(lv_vhelp);
													
												}
												var lv_mail_event_type_field = evt.getSource().getParent().getItems()[17];
												lv_mail_event_type_field.destroyItems();
												lv_mail_event_type_field.setForceSelection(false);
												lv_mail_event_type_field.setSelectedKey("");
												
											}
										}),

										new sap.m.Label({text: "Object ID"}),
										new sap.m.Select({
											width:"450px",
											forceSelection : false,
											change:function(evt){

												var lv_selected = evt.getSource().getSelectedKey();
												var lv_mail_event_type_field = evt.getSource().getParent().getItems()[17];
												lv_mail_event_type_field.destroyItems();
												lv_mail_event_type_field.setForceSelection(false);
												lv_mail_event_type_field.setSelectedKey("");
												for(var i=0;i<gv_mail_event_type_vhelp[lv_selected].length;i++){
													var lv_vhelp = new sap.ui.core.Item({
														
														key : gv_mail_event_type_vhelp[lv_selected][i].ID,
														text : gv_mail_event_type_vhelp[lv_selected][i].ID
													});
													lv_mail_event_type_field.addItem(lv_vhelp);
												}
											}
										}),

										new sap.m.Label({text: "Event Type"}),
										new sap.m.Select({
											width:"450px",
											forceSelection : false,
											change:function(evt){

											}
										})
									]
								})
							]
						})
					]
				});
				
				self.control.setDraggable(true);
				self.control.setTitle("Create New Invite Config");
				self.control.setContentWidth("600px");
				self.control.setBeginButton(lv_begin_button);
				self.control.setEndButton(lv_end_button);
				self.control.addContent(lv_strip_msg);
				self.control.addContent(lv_content);
				self.bind_value_help();
				self.control.open();
				
			},
			bind_value_help: function(){

				var self = this;
				var lv_field = self.control.getContent()[1].getContent()[0].getItems()[0].getItems();

				lv_field[13].destroyItems();
				gv_mail_obj_type_vhelp.forEach(function(i){

					lv_vhelp = new sap.ui.core.Item({
						key : i,
						text : i
					})
					lv_field[13].addItem(lv_vhelp);
				});

				lv_field[5].destroyItems();
				var lv_vhelp = {};
				gv_invite_conf_vhelp.forEach(function(i){

					lv_vhelp = new sap.ui.core.Item({
						key : i.ID,
						text : i.description
					})
					lv_field[5].addItem(lv_vhelp);
				});
			},
			check_data : function(){

				var self = this;
				var lv_field = self.control.getContent()[1].getContent()[0].getItems()[0].getItems();
				var lv_input1 = lv_field[1];
				var lv_data = ui("GO_TABLE_INVITE_CONF").getModel().getData();
				var lv_message = {};
				self.control.getContent()[0].destroyContent();
				self.error = false;
				if(lv_input1.getValue().toUpperCase() == ""){
					lv_message = fn_show_message_strip("Invite ID is empty");
					self.error = true;
					lv_input1.setValueState("Error");
					self.control.getContent()[0].addContent(lv_message);
					self.control.getContent()[0].setVisible(true);
				}
				else{
					lv_message = fn_show_message_strip("Invite ID already exist");
					for(var i=0;i<lv_data.length;i++){
						if(lv_data[i].INVITE_ID.toUpperCase() == lv_input1.getValue().toUpperCase()){
							self.error = true;
							lv_input1.setValueState("Error");
							self.control.getContent()[0].addContent(lv_message);
							self.control.getContent()[0].setVisible(true);
							break;
						}
					}
				}
			},
			new_line : function(){

				var self = this;
				var lv_field = self.control.getContent()[1].getContent()[0].getItems()[0].getItems();
				
				var lv_send_email_indicator = "";
				if(lv_field[11].getState() == true){
					lv_send_email_indicator = "X";
				}

				gt_invite_conf.push({

					ID 					: "",
					INVITE_ID 			: lv_field[1].getValue(),
					DESCRIPTION 		: lv_field[3].getValue(),
					STATUS 				: lv_field[5].getSelectedKey(),
					EXPIRY_HR 			: lv_field[7].getValue(),
					EXPIRY 				: Math.floor(lv_field[7].getValue() * 60),
					CREATE_USERNAME 	: lv_field[9].getValue(),
					SEND_EMAIL 			: lv_send_email_indicator,
					MAIL_OBJ_TYPE 		: lv_field[13].getSelectedKey(),
					MAIL_OBJ_ID 		: lv_field[15].getSelectedKey(),
					MAIL_EVENT_TYPE 	: lv_field[17].getSelectedKey(),
					DEL_FLAG 			: ""
				});

				fn_set_invite_conf_data_mode(gt_invite_conf,"edit",function(data){
					fn_bind_invite_conf(data);
				});
				setTimeout(function(){
					fn_bind_invite_conf_valuehelp();
				},500);
			}
		};

		var lo_confirmation_dialog = {

			data : {},
			action : "",
			title : "",
			save_data : [],
			source : {},
			confirmation : function(){

				var self = this;
				var lo_data = self.data;

				new sap.m.Dialog({
					title: self.title,
					beginButton: new sap.m.Button({
						text:"Ok",
						type:"Accept",
						icon:"sap-icon://accept",
						press:function(oEvt){

							if(self.action == "save"){
								self.save_record();
							}
							else if(self.action == "discard"){
								self.discard_changes();
							}
							else if(self.action == "delete"){
								self.filter_record();
							}
							oEvt.getSource().getParent().close();
						}
					}),
					endButton:new sap.m.Button({
						text:"Cancel",
						type:"Reject",
						icon:"sap-icon://decline",
						press:function(oEvt){

							oEvt.getSource().getParent().close();
						}
					}),
					content:[
						new sap.m.HBox({
							items:[
								new sap.m.Label({text:"Are you sure to "+self.action+"?"})
							]
						})
					]
				}).open().addStyleClass("sapUiSizeCompact");
			},
			check_data : function(){

				var self = this;
				self.save_data = fn_check_data();
				self.confirmation();
			},
			save_record : function(){

				var self = this;
				if(
					self.save_data.invite_conf_create != "" ||
					self.save_data.invite_conf_update != "" ||
					self.save_data.invite_roles_create != "" ||
					self.save_data.invite_roles_update != ""
				){
					fn_save_invite_conf(self.save_data);
				}
				else{
					fn_show_notification_message("Noting to update");
				}
			},
			discard_changes : function(){
				
				fn_get_invite_conf();
			},
			delete_record : function(){

				var self = this;
				var lo_index = String(self.source.getBindingContext().getPath());
				var lv_index = lo_index.split("/")[1];
				var lv_deleted = self.source.getParent().getModel().getData()[lv_index];
				lv_deleted.DEL_FLAG = "X";
				self.confirmation();

			},
			filter_record : function(){

				var self = this;
				var lo_index = String(self.source.getBindingContext().getPath());
				var lv_index = lo_index.split("/")[1];
				var lv_deleted = self.source.getParent().getModel().getData();
				var table_id = self.source.getParent().getParent().getId();

				// if(lv_deleted[lv_index].ID != ""){
					var lt_Model = new sap.ui.model.json.JSONModel();
					lt_Model.setSizeLimit(self.data.length);
					lt_Model.setData(self.data);
					ui(table_id).setModel(lt_Model);
					ui(table_id).bindRows("/",self.data);

					ui(table_id).getBinding("rows").filter(
						new sap.ui.model.Filter("DEL_FLAG", sap.ui.model.FilterOperator.NE,"X"),
						"Application"
					);
				// }
				// else{
				// 	console.log(lv_deleted,lv_index);
				// 	lv_deleted.splice(lv_index, 1);
				// 	self.source.getParent().getModel().refresh();
				// }
			}
		};

		new sap.m.Dialog("ROLE_AUTH_DIALOG",{
			draggable : true,
			title: "Role Authorization",
			contentWidth : "1400px",
			beginButton: new sap.m.Button({
				text:"Ok",
				type:"Accept",
				icon:"sap-icon://accept",
				press:function(oEvt){

					// lo_role_auth_dialog.mode = "display";
					lo_role_auth_dialog.check_data();
					if(lo_role_auth_dialog.error == false){
						lo_role_auth_dialog.save_temp();
						// ui("BUTTON_ROLE_AUTH_EDIT").setVisible(true);
						ui("BUTTON_ROLE_AUTH_ADD").setVisible(false);
						// ui("BUTTON_ROLE_AUTH_SAVE").setVisible(false);
						oEvt.getSource().getParent().close();
					}
					
				}
			}),
			endButton:new sap.m.Button({
				text:"Cancel",
				type:"Reject",
				icon:"sap-icon://decline",
				press:function(oEvt){

					// lo_role_auth_dialog.mode = "display";
					lo_role_auth_dialog.discard_delete();
					// ui("BUTTON_ROLE_AUTH_EDIT").setVisible(true);
					ui("BUTTON_ROLE_AUTH_ADD").setVisible(false);
					// ui("BUTTON_ROLE_AUTH_SAVE").setVisible(false);
					ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").destroyContent();
					ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").setVisible(false);
					oEvt.getSource().getParent().close();
				}
			}),
			content:[
				new sap.m.Panel("MESSAGE_STRIP_PANEL_ROLE_AUTH",{visible:false,backgroundDesign:"Transparent"}),
				new sap.m.Panel({
					backgroundDesign : "Transparent",
					content:[
						new sap.m.ObjectStatus("OBJECTHEADER_INVITE_ID",{
							title: "Invite ID ",
							text : ""
						}),
					]
				}),

				new sap.ui.table.Table("GO_TABLE_ROLE_AUTH",{

					selectionMode : "None",
					enableCellFilter : true,
					// autoResizeColumn : true,
					// visibleRowCountMode : "Auto",
					filter : function(oEvt){
						oEvt.getSource().getBinding("rows").attachChange(function(oEvt){

						});
					},
					rowSelectionChange : function(oEvt){

						var lt_data = {
						slect_all :oEvt.getParameters().selectAll,
						slect_arr  :oEvt.getParameters().rowIndices,
						slect_indx :oEvt.getParameters().rowIndex
						}
					},
					toolbar : [
						new sap.m.Toolbar({
							design:"Transparent",
							content:[

								// new sap.m.Label("ROLE_AUTH_LABEL",{text:"Role Auth (0)"}),
								new sap.m.ToolbarSpacer(),
								new sap.m.Button("BUTTON_ROLE_AUTH_EDIT",{
									icon:"sap-icon://edit",
									visible:false,
									press:function(){

										lo_role_auth_dialog.mode = "edit";
										lo_role_auth_dialog.data = gt_invite_roles;
										lo_role_auth_dialog.roles_vhelp = gv_roles_vhelp;
										lo_role_auth_dialog.status_vhelp = gv_invite_role_vhelp;
										lo_role_auth_dialog.init();
									}
								}),
								new sap.m.Button("BUTTON_ROLE_AUTH_ADD",{
									icon:"sap-icon://add",
									visible:false,
									press:function(){
										
										lo_role_auth_dialog.new_line();
									}
								}),
								new sap.m.Button("BUTTON_ROLE_AUTH_SAVE",{
									icon:"sap-icon://save",
									visible:false,
									press:function(){
										
										lo_role_auth_dialog.save_temp();

									}
								})
							]
						})
					],
					columns : [

						new sap.ui.table.Column("GO_TABLE_ROLE_AUTH_COL_1",{
							hAlign : "Left",
							width : "15%",
							visible : false,
							label : new sap.m.Label({text : "Invite ID"}),
							template : new sap.m.Input({
								value:"{INVITE_ID}",
								editable : "{INVITE_ID_EDITABLE}",
								valueState : "{INVITE_ID_ERROR}",
								maxLength:256,
								tooltip:"{INVITE_ID}",
								liveChange:function(oEvt){

								}
							}),
							sortProperty:"INVITE_ID",
							filterProperty:"INVITE_ID"
						}),
						new sap.ui.table.Column("GO_TABLE_ROLE_AUTH_COL_2",{
							hAlign : "Left",
							width : "15%",
							label : new sap.m.Label({text : "Role"}),
							template : new sap.m.HBox({
								renderType : "Bare",
								items : [
									new sap.m.Select({
										selectedKey:"{ROLE}",
										maxLength:256,
										name : "{ROLE_DESC}",
										tooltip:"{ROLE}",
										valueState : "{ROLE_VALUESTATE_ERROR}",
										width:"100%",
										visible : "{ROLE_VISIBLE_EDT}",
										forceSelection : false,
										showSecondaryValues : true,
										change : function(oEvt){
											
											ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").destroyContent();
											ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").setVisible(false);
											oEvt.getSource().setValueState("None");
											var lo_index = String(oEvt.getSource().getBindingContext().getPath());
											var lv_index = lo_index.split("/")[1];
											var lv_model_data = oEvt.getSource().getParent().getModel().getData();
											var lv_selectedKey = oEvt.getSource().getSelectedKey();
											for(var i=0;i<gv_roles_vhelp.length;i++){
												if(gv_roles_vhelp[i].ID == lv_selectedKey){
													lv_model_data[lv_index].DESCRIPTION = gv_roles_vhelp[i].description;
													break;
												}
											}
											lv_model_data[lv_index].ROLE_VALUESTATE_ERROR = "None";
											var error = false;
											
											for(var i=0;i<lv_model_data.length;i++){
												if(lv_index != String(i)){
													if(lv_model_data[i].ROLE == lv_selectedKey){
														lv_message = fn_show_message_strip("Role already exist");
														error = true;
														lv_model_data[lv_index].ROLE_VALUESTATE_ERROR = "Error";
														ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").addContent(lv_message);
														ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").setVisible(true);
														break;
													}
												}
											}
											oEvt.getSource().getParent().getModel().refresh();
										}
									}),
									new sap.m.Text({
										text:"{ROLE}",
										visible : "{ROLE_VISIBLE_DSP}"
									})
								]
							}),
							sortProperty : "ROLE",
							filterProperty : "ROLE"
						}),
						new sap.ui.table.Column("GO_TABLE_ROLE_AUTH_COL_3",{
							hAlign : "Left",
							width : "20%",
							label : new sap.m.Label({text : "Description"}),
							template : new sap.m.Text({
								text:"{DESCRIPTION}",
								// maxLength:51,
								tooltip:"{DESCRIPTION}",
								// editable : "{DESCRIPTION_EDITABLE}",
								// liveChange:function(oEvt){

								// }
							}),
							sortProperty : "DESCRIPTION",
							filterProperty : "DESCRIPTION"
						}),
						new sap.ui.table.Column("GO_TABLE_ROLE_AUTH_COL_4",{
							hAlign : "Left",
							width : "10%",
							label : new sap.m.Label({text : "Valid from"}),
							template : new sap.m.DatePicker({
								value:"{VALID_FR}",
								displayFormat : "dd MMM yyyy",
								valueFormat : "yyyy-MM-dd",
								maxLength:11,
								editable : "{VALID_FR_EDITABLE}",
								liveChange:function(oEvt){

								}
							}),
							sortProperty : "",
							filterProperty : ""
						}),
						new sap.ui.table.Column("GO_TABLE_ROLE_AUTH_COL_5",{
							hAlign : "Left",
							width : "10%",
							label : new sap.m.Label({text : "Valid To"}),
							template : new sap.m.DatePicker({
								value:"{VALID_TO}",
								maxLength:11,
								displayFormat : "dd MMM yyyy",
								valueFormat : "yyyy-MM-dd",
								editable : "{VALID_TO_EDITABLE}",
								liveChange:function(oEvt){

								}
							}),
							sortProperty : "",
							filterProperty : ""
						}),
						new sap.ui.table.Column("GO_TABLE_ROLE_AUTH_COL_6",{
							hAlign : "Left",
							width : "10%",
							label : new sap.m.Label({text : "Status"}),
							template : new sap.m.HBox({
								renderType : "Bare",
								items : [
									new sap.m.Select({
										selectedKey:"{STATUS}",
										maxLength:256,
										tooltip:"{STATUS}",
										visible : "{STATUS_VISIBLE_EDT}",
										width:"100%",
										liveChange:function(oEvt){
											
										}
									}),
									new sap.m.Text({
										text:"{STATUS_DESC}",
										visible : "{STATUS_VISIBLE_DSP}"
									})
								]
							}),
							sortProperty : "STATUS_DESC",
							filterProperty : "STATUS_DESC"
						}),
						new sap.ui.table.Column({
							hAlign : "Left",
							width : "4%",
							visible : false,
							label : new sap.m.Label({text : ""}),
							template : new sap.m.Button({
								type:"Transparent",
								icon:"sap-icon://delete",
								press : function(oEvt){

									// var lv_del_vpnlogin = "";
									// //get index of item and remove item from array
									// var lo_index = String(oEvt.getSource().getBindingContext().getPath());
									// var lv_index = lo_index.split("/")[1];
									// var table_id = String(oEvt.getSource().getParent().getParent().getId());
									// var lv_vpnlogin = ui(table_id).getModel().getData()
									// // console.log(oEvt.getSource(),lv_index)
									// //remove item from array
									// lv_del_vpnlogin = lv_vpnlogin.splice(lv_index, 1);

									// if(lv_del_vpnlogin[0].CUSTOMER_ID != ''){

									// 	var lv_vpn_software = ui('OBJECTHEADER_INVITE_ID').getText();
									// 	gv_del_items.push({
									// 		TABLE_ID : table_id,
									// 		DEL_ITEM : lv_del_vpnlogin[0].VPN_USER,
									// 		REF1 	 : gv_cust_id,
									// 		REF2 	 : lv_vpn_software
									// 	});

									// 	gv_recover_vpnlogin.push(lv_del_vpnlogin[0]);
									// }

									// for(var i=0;i<gt_vpnlogin.length;i++){
									// 	if(gt_vpnlogin[i].VPN_USER == lv_del_vpnlogin[0].VPN_USER && gt_vpnlogin[i].VPN_SOFTWARE == vpn_software){
									// 		gt_vpnlogin.splice(i, 1);
									// 		break;
									// 	}
									// }
									// for(var i=0;i<gt_cus_vpnlogin.length;i++){
									// 	if(gt_cus_vpnlogin[i].VPN_USER == lv_del_vpnlogin[0].VPN_USER && gt_cus_vpnlogin[i].VPN_SOFTWARE == vpn_software){
									// 		gt_cus_vpnlogin.splice(i, 1);
									// 		break;
									// 	}
									// }
									// // oEvt.getSource().getParent().getParent().getModel().refresh();
									// var lo_model = new sap.ui.model.json.JSONModel();
									// lo_model.setSizeLimit(lv_vpnlogin.length);
									// lo_model.setData(lv_vpnlogin);

									// ui('GO_TABLE_ROLE_AUTH_DETAIL').setModel(lo_model).bindRows('/');
									// // ui('ROLE_AUTH_LABEL').setText("Role Auth ("+lv_vpnlogin.length+")");
									lo_confirmation_dialog.data = lo_role_auth_dialog.role_auth;
									lo_confirmation_dialog.source = oEvt.getSource();
									lo_confirmation_dialog.action = "delete";
									lo_confirmation_dialog.title = "Delete Record";
									lo_confirmation_dialog.delete_record();

								}
							}),
							// sortProperty : "",
							// filterProperty : ""
						}),
					]
				}),
			]
		}).addStyleClass("sapUiSizeCompact");

		var lo_role_auth_dialog = {

			roles_vhelp : [],
			status_vhelp : [],
			mode : "display",
			source : {},
			data : {},
			error : false,
			role_auth : [],
			init : function(){

				var self = this;
				self.set_valuehelp();
				// self.set_mode();
				self.set_value();
				self.open_dialog();
			},
			set_valuehelp : function(){

				var self = this;
				//roles
				var lv_role_field = ui("GO_TABLE_ROLE_AUTH").getColumns()[1].getTemplate().getItems()[0];
				lv_role_field.destroyItems();
				for(var i=0;i<self.roles_vhelp.length;i++){
					var lv_vhelp = new sap.ui.core.ListItem({
						
						key : self.roles_vhelp[i].ID,
						text : self.roles_vhelp[i].ID,
						additionalText : self.roles_vhelp[i].description

					});
					lv_role_field.addItem(lv_vhelp);
				}
				//status
				var lv_status_field = ui("GO_TABLE_ROLE_AUTH").getColumns()[5].getTemplate().getItems()[0];
				lv_status_field.destroyItems();
				for(var i=0;i<self.status_vhelp.length;i++){
					var lv_vhelp = new sap.ui.core.Item({
						
						key : self.status_vhelp[i].ID,
						text : self.status_vhelp[i].description
					});
					lv_status_field.addItem(lv_vhelp);
				}
			},
			set_mode : function(){

				var self = this;
				if(self.mode == "display"){

					var lv_edit = false;
					var lv_display = true;
					// ui("BUTTON_ROLE_AUTH_EDIT").setVisible(true);
					ui("BUTTON_ROLE_AUTH_ADD").setVisible(lv_edit);
					// ui("BUTTON_ROLE_AUTH_SAVE").setVisible(false);
					ui("GO_TABLE_ROLE_AUTH").getColumns()[6].setVisible(lv_edit);
				}
				else{

					var lv_edit = true;
					var lv_display = false;
					// ui("BUTTON_ROLE_AUTH_EDIT").setVisible(false);
					ui("BUTTON_ROLE_AUTH_ADD").setVisible(lv_edit);
					// ui("BUTTON_ROLE_AUTH_SAVE").setVisible(true);
					ui("GO_TABLE_ROLE_AUTH").getColumns()[6].setVisible(lv_edit);
				}

				self.role_auth.forEach(function(i){
					self.roles_vhelp.forEach(function(key){
						if(key.ID == i.ROLE){
							i.ROLE_DESC = key.description;
						}
					});
					self.status_vhelp.forEach(function(key1){

						if(key1.ID == i.STATUS){
							i.STATUS_DESC = key1.description;
						}
					});

					// if(self.mode == "display"){

						i.ROLE_VISIBLE_EDT = lv_edit;
						i.ROLE_VISIBLE_DSP = lv_display;
						i.DESCRIPTION_EDITABLE = lv_edit;
						i.VALID_FR_EDITABLE = lv_edit;
						i.VALID_TO_EDITABLE = lv_edit;
						i.STATUS_VISIBLE_EDT = lv_edit;
						i.STATUS_VISIBLE_DSP = lv_display;

					// }
					// else{

					// 	i.ROLE_VISIBLE_EDT = true;
					// 	i.ROLE_VISIBLE_DSP = false;
					// 	i.DESCRIPTION_EDITABLE = true;
					// 	i.VALID_FR_EDITABLE = true;
					// 	i.VALID_TO_EDITABLE = true;
					// 	i.STATUS_VISIBLE_EDT = true;
					// 	i.STATUS_VISIBLE_DSP = false;
					// }
				});
				self.bind_value();
			},
			set_value : function(){

				var self = this;
				self.role_auth = [];
				var lv_invite = self.source.getBindingContext().getProperty('INVITE_ID');
				self.data.forEach(function(key){
					if(lv_invite == key.INVITE_ID && key.DEL_FLAG == ""){
						self.role_auth.push(key);
					}
				});
				self.set_mode();
			},
			bind_value : function(){

				var self = this;
				var lv_invite = self.source.getBindingContext().getProperty('INVITE_ID');
				var lo_model = new sap.ui.model.json.JSONModel();
				lo_model.setSizeLimit(self.role_auth.length);
				lo_model.setData(self.role_auth);
				
				ui('GO_TABLE_ROLE_AUTH').setModel(lo_model).bindRows('/');
				ui("GO_TABLE_ROLE_AUTH").getBinding("rows").filter(
					new sap.ui.model.Filter("DEL_FLAG", sap.ui.model.FilterOperator.NE,"X"),
					"Application"
				);
				ui("OBJECTHEADER_INVITE_ID").setText(lv_invite);
			},
			open_dialog : function(){

				var self = this;
				ui("ROLE_AUTH_DIALOG").open();
			},
			new_line : function(){

				var self = this;
				var lv_invite = self.source.getBindingContext().getProperty('INVITE_ID');				


				self.role_auth.push({

					ID 			: "",
					INVITE_ID 	: lv_invite,
					ROLE 		: "",
					DESCRIPTION : "",
					VALID_FR 	: fn_format_datetime(new Date() , "YYYY-MM-DD"),
					VALID_TO 	: "9999-12-31",
					STATUS 		: "01",
					DEL_FLAG 	: "",
				});

				self.set_mode();
			},
			check_data : function(){

				var self = this;
				var lv_data = self.role_auth;
				var lv_message = {};
				self.error = false;
				ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").destroyContent();
				for(var i=0;i<lv_data.length;i++){
					if(lv_data[i].ROLE == ""){
						lv_message = fn_show_message_strip("Role is empty");
						self.error = true;
						lv_data[i].ROLE_VALUESTATE_ERROR = "Error";
						ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").addContent(lv_message);
						ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").setVisible(true);
						break;
					}
				}

				if(self.error == false){
					for(var i=0;i<lv_data.length;i++){
						for(var j=i;j<lv_data.length;j++){
							if(i == j){
								continue;
							}
							else{
								if(lv_data[i].ROLE == lv_data[j].ROLE){
									lv_message = fn_show_message_strip("Role already exist");
									self.error = true;
									lv_data[j].ROLE_VALUESTATE_ERROR = "Error";
									ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").addContent(lv_message);
									ui("MESSAGE_STRIP_PANEL_ROLE_AUTH").setVisible(true);
								}
							}
						}
						if(self.error == true){
							break;
						}
					}
				}
				ui("GO_TABLE_ROLE_AUTH").getModel().refresh();
			},
			save_temp : function(){

				var self = this;
				// console.log("auth role",self.data);
				// console.log("temp auth role",self.role_auth);
				self.role_auth.forEach(function(index){
					if(self.data.length == '0'){
						self.data.push({

							ID 			: index.ID,
							INVITE_ID 	: index.INVITE_ID,
							ROLE 		: index.ROLE,
							DESCRIPTION : index.DESCRIPTION,
							VALID_FR 	: index.VALID_FR,
							VALID_TO 	: index.VALID_TO,
							STATUS 		: index.STATUS,
							DEL_FLAG 	: index.DEL_FLAG
						});
					}
					else{
						var lv_status = '';

						for(var i=0;i<self.data.length;i++){
							if(self.data[i].INVITE_ID == index.INVITE_ID){
								if(self.data[i].ROLE == index.ROLE){

									self.data[i].ROLE = index.ROLE;
									self.data[i].DESCRIPTION = index.DESCRIPTION;
									self.data[i].VALID_FR = index.VALID_FR;
									self.data[i].VALID_TO = index.VALID_TO;
									self.data[i].STATUS = index.STATUS;
									lv_status = "update";
									break;
								}
								else{
									lv_status = "push";
								}
							}
							else{
								lv_status = "push";
							}
						}

						if(lv_status == "push"){
							self.data.push({
								ID 			: index.ID,
								INVITE_ID 	: index.INVITE_ID,
								ROLE 		: index.ROLE,
								DESCRIPTION : index.DESCRIPTION,
								VALID_FR 	: index.VALID_FR,
								VALID_TO 	: index.VALID_TO,
								STATUS 		: index.STATUS,
								DEL_FLAG 	: index.DEL_FLAG
							});
						}
					}
				});
			},
			discard_delete : function(){

				var self = this;
				var lv_check = false;
				console.log(self.role_auth)
				self.role_auth.forEach(function(index){
					if(index.DEL_FLAG == "X"){
						index.DEL_FLAG = "";
						lv_check = true
					}
				});
				if(lv_check == true){
					self.save_temp();
				}
			}
		};
		
		return lo_page;
	}

	var go_generate_screen = {

		mode : "display",
		screen : new sap.m.Page("PAGE_INVITE_CONF_MAINTENANCE_RIGHT1", {}),
		initial : function(){

			var self = this;

			self.create();
		},
		create : function(){

		}
	};

	function Page_Right_Invite_Selection_Screen(){

		var lo_generate_screen = {

			mode : "display",
			page : {},
			initial : function(){

				var self = this;

				self.setPage();
				// self.getValueHelp();
			},
			setPage : function(){

				var self = this;

				var lv_width_Label = "150px";
				var lv_width_Label_To = "10px";
				var lv_width_Input = "300px";
				var lv_width_LongInput = "652px";

				var lo_header = new sap.m.Bar({
					contentLeft: [
						new sap.m.Button({ icon:"sap-icon://nav-back",
							press:function(oEvt){ 
								 
								go_App_Right.back();
							} 
						}),
						new sap.m.Button({icon:"sap-icon://menu2",
							press:function(){
								go_SplitContainer.setSecondaryContentWidth("230px");
								if(!go_SplitContainer.getShowSecondaryContent()){
									go_SplitContainer.setShowSecondaryContent(true);
								} else {							
									go_SplitContainer.setShowSecondaryContent(false);
								}
								
								setTimeout(function(){	
									var lo_table = $('#GO_TABLE_INVITE_LISTING-listUl');
									lo_table.floatThead('reflow');	
								}, 500);
								
							}
						}), 
						new sap.m.Image({src: logo_path}),
					],
					contentRight: [ fn_help_button(SelectedAppID,"AM_INVITE_LST"),new sap.m.Button("", { icon: "sap-icon://home" ,press:function(){ window.location.href = MainPageLink; }}) ],
					contentMiddle: [ new sap.m.Label({text: "Invitation Listing"}) ],
				});

				var lo_crumbs = new sap.m.Breadcrumbs({
					currentLocationText:"Invite Selection Screen",
					links:[
						new sap.m.Link({
							text:"Home",
							press:function(oEvt){
								fn_click_breadcrumbs("HOME");
							}
						}),
						new sap.m.Link({
							text:"Account Management",
							press:function(oEvt){
								
							}
						})
					]
				}).addStyleClass('breadcrumbs-padding');

				var lo_vbox = new sap.m.VBox({
					justifyContent:"End",
					alignItems:"End",
					items:[
						new sap.m.Button({
							icon:"sap-icon://search",
							tooltip:"Search",
							press:function(){

								fn_get_invite_listing();
								go_App_Right.to('PAGE_INVITE_LISTING');
							}

						}).addStyleClass("sapMTB-Transparent-CTX")
					]
				});

				var lo_panel = new sap.m.Panel({
					content:[
						new sap.m.HBox({
							justifyContent:"Center",
							items:[
								new sap.m.Label("",{text:"Invite ID", width:lv_width_Label}).addStyleClass('label-add-padding'),
								new sap.m.MultiComboBox("INPUT_INVITE_ID",{
									width:lv_width_LongInput,
									placeholder:"Select",
									selectedKeys:[],
								})
							]
						}),
						new sap.m.HBox({
							justifyContent:"Center",
							items:[
								new sap.m.Label({text:"Created Date From", width:lv_width_Label}).addStyleClass('label-add-padding'),
								new sap.m.DatePicker("INPUT_INVITE_CREATED_DATE_FROM",{
									// value: fn_format_datetime(new Date() , "DD MMM YYYY"),
									displayFormat : "dd MMM yyyy",
									valueFormat : "yyyy-MM-dd",
									width : lv_width_Input,
									value : "",
									placeholder : "DD MMM YYYY"
								}),

								new sap.m.Label({text:"to", width:lv_width_Label_To}).addStyleClass('label-add-padding'),
								new sap.m.DatePicker("INPUT_INVITE_CREATED_DATE_TO",{
									// value: fn_format_datetime(new Date() , "DD MMM YYYY"),
									displayFormat : "dd MMM yyyy",
									valueFormat : "yyyy-MM-dd",
									width : lv_width_Input,
									value : "",
									placeholder : "DD MMM YYYY"
								}),
							]

						}),
						new sap.m.HBox({
							justifyContent:"Center",
							items:[
								new sap.m.Label("",{text:"Status", width:lv_width_Label}).addStyleClass('label-add-padding'),
								new sap.m.MultiComboBox("INPUT_INVITE_STATUS",{
									width:lv_width_LongInput,
									placeholder:"Select",
									selectedKeys:[],
								})
							]
						}),
					],
				});

				self.page.setCustomHeader(lo_header);
				self.page.addContent(lo_crumbs);
				self.page.addContent(lo_vbox);
				self.page.addContent(lo_panel);
			},
			getValueHelp : function(){

				fn_get_invite_valuehelp();
			}
		};

		lo_generate_screen.mode = "display";
		lo_generate_screen.page = new sap.m.Page("PAGE_INVITE_SELECTION_SCREEN",{}).addStyleClass('sapUiSizeCompact');
		lo_generate_screen.initial();


		return lo_generate_screen.page;
	}

	

	function Page_Right_Invite_Listing(){

		var lv_resent_invite = false;
		
		var lo_page = new sap.m.Page("PAGE_INVITE_LISTING", {
			customHeader: new sap.m.Bar({
				contentLeft: [
					new sap.m.Button({ icon:"sap-icon://nav-back",
						press:function(oEvt){ 
							 
							go_App_Right.back();
						} 

					}),
					new sap.m.Button({icon:"sap-icon://menu2",
						press:function(){
							go_SplitContainer.setSecondaryContentWidth("230px");
							if(!go_SplitContainer.getShowSecondaryContent()){
								go_SplitContainer.setShowSecondaryContent(true);
							} else {
								go_SplitContainer.setShowSecondaryContent(false);
							}

							setTimeout(function(){
								var lo_table = $('#GO_TABLE_INVITE_LISTING-listUl');
								lo_table.floatThead('reflow');
							}, 500);

						}
					}),
					new sap.m.Image({src: logo_path}),
				],
				contentRight: [ fn_help_button(SelectedAppID,"AM_INVITE_LST"),new sap.m.Button("", { icon: "sap-icon://home" ,press:function(){ window.location.href = MainPageLink; }}) ],
				contentMiddle: [ new sap.m.Label({text: "Invitation Listing"}) ],
			}),
			content: [
				new sap.m.Breadcrumbs({
					currentLocationText:"Invitation Listing",
					links:[
						new sap.m.Link({
							text:"Home",
							press:function(oEvt){
								fn_click_breadcrumbs("HOME");
							}
						}),
						new sap.m.Link({
							text:"Account Management",
							press:function(oEvt){
								
							}
						})
					]
				}).addStyleClass('breadcrumbs-padding'),
				new sap.m.Bar({
					visible: false,
					enableFlexBox: false,
					contentLeft: [
						new sap.m.HBox({
							width: "100%",
							items: [
								new sap.m.SearchField("INVITE_LISTING_SEARCH",{
									layoutData: new sap.m.FlexItemData({growFactor: 2}),
									placeholder: "Search...",
									liveChange: function(oEvent){

										sap.ui.getCore().byId("INVITE_LISTING_SEARCH").destroyItems();
										var oFilter = new sap.ui.model.Filter(sap.ui.getCore().byId("SEARCH_SELECT_INVITE_LISTING").getSelectedItem().getKey(), sap.ui.model.FilterOperator.Contains, oEvent.getSource().getValue());
										fn_bind_moblie_maint_header("","SEARCH",oFilter);
									},
									search:function(){
										ui("INVITE_LISTING_SEARCH").fireLiveChange();
									}
								}),
								new sap.m.Select("SEARCH_SELECT_INVITE_LISTING",{
									type: sap.m.SelectType.Default,
									items: [
										new sap.ui.core.Item({text: "User ID", key: "USER_ID"}),
										new sap.ui.core.Item({text: "Display Name", key: "DISPLAY_NAME"}),
									],
									change: function(){
										ui("INVITE_LISTING_SEARCH").fireLiveChange();
									}
								}).addStyleClass("class_select_search_bar")
							]
						}),
					],
				}),

				new sap.ui.table.Table("GO_TABLE_INVITE_LISTING",{
					visibleRowCountMode:"Auto",
					selectionMode:"None",
					enableCellFilter: true,

					toolbar:[
						new sap.m.Toolbar({
							design:"Solid",
							content:[
								new sap.m.Label("GO_TABLE_INVITE_LISTING_LABEL",{
									text:"Invite List (0)"
								}),
								new sap.m.ToolbarSpacer(),
								new sap.m.Button("INVITE_LISTING_REFRESH",{
									visible:true,
									//type:"Transparent",
									icon: "sap-icon://refresh",
									press:function(){

										fn_get_invite_listing();
									}
								}),
								new sap.m.Button("INVITE_LISTING_DOWNLOAD",{
									visible:true,
									//type:"Transparent",
									icon: "sap-icon://download",
									press:function(){

										if(ui('GO_TABLE_INVITE_LISTING').getBinding().iLength > 0){

											fn_download_invite_listing();

										}else{

											fn_show_notification_message("No data to download");
										}

									}
								})
							]
						})
					],
					columns : [

						new sap.ui.table.Column({
							hAlign:"Left",
							width : "330px",
							label 	: new sap.m.Label({text:"Invitation"}),
							template: new sap.m.Text({text:"{DESCRIPTION}",wrapping:false,tooltip:"{DESCRIPTION}",wrapping:false}),
							sortProperty:"DESCRIPTION",
							filterProperty:"DESCRIPTION",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "240px",
							label 	: new sap.m.Label({text:"Email"}),
							template: new sap.m.Text({text:"{EMAIL}",wrapping:false,tooltip:"{EMAIL}"}),
							sortProperty:"EMAIL",
							filterProperty:"EMAIL",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "120px",
							label 	: new sap.m.Label({text:"Status"}),
							template: new sap.m.Text({text: "{STATUS_DESC}",wrapping:false,tooltip:"{STATUS_DESC}"}),
							sortProperty:"STATUS_DESC",
							filterProperty:"STATUS_DESC",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "50px",
							label 	: new sap.m.Label({text:""}),
							template : new sap.m.Button({
								enabled:"{INVITATION_ENABLED}",
								tooltip:"Resend Invitation",
								icon: 'sap-icon://visits',
								press: function(oEvt) {

									var lo_index = String(oEvt.getSource().getBindingContext().getPath());
									var lv_index = lo_index.split("/")[1];
									fn_resent_invitation.data = oEvt.getSource().getParent();
									fn_resent_invitation.action = "resend";
									fn_resent_invitation.title = "Resent Invitation";
									fn_resent_invitation.confirmation();
								},
							}),
							sortProperty:"",
							filterProperty:"",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "50px",
							label 	: new sap.m.Label({text:""}),
							template: new sap.m.Button({
								icon:"sap-icon://activate",
								tooltip:"Deactivate",
								enabled:"{DEACTIVATED_ENABLED}",
								press:function(oEvt){

									fn_resent_invitation.data = oEvt.getSource().getParent();
									fn_resent_invitation.action = "deactivate";
									fn_resent_invitation.title = "Deactivate Invitation";
									fn_resent_invitation.confirmation();
								}
							}),
							sortProperty:"",
							filterProperty:"",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Created On"}),
							template: new sap.m.Text({text:"{CREATED_AT_DSP}",wrapping:false,tooltip:"{CREATED_AT_DSP}"}),
							sortProperty:"CREATED_TIMESTEMP",
							filterProperty:"CREATED_TIMESTEMP",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "140px",
							label 	: new sap.m.Label({text:"Created By"}),
							template: new sap.m.Text({text:"{created_by}",wrapping:false,tooltip:"{created_by}"}),
							sortProperty:"created_by",
							filterProperty:"created_by",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Resend On"}),
							template: new sap.m.Text({text:"{RESEND_AT_DSP}",wrapping:false,tooltip:"{RESEND_AT_DSP}"}),
							sortProperty:"RESEND_TIMESTEMP",
							filterProperty:"RESEND_TIMESTEMP",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "140px",
							label 	: new sap.m.Label({text:"Resend By"}),
							template: new sap.m.Text({text:"{RESEND_BY}",wrapping:false,tooltip:"{RESEND_BY}"}),
							sortProperty:"RESEND_BY",
							filterProperty:"RESEND_BY",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Accepted On"}),
							template: new sap.m.Text({text:"{ACCEPTED_AT_DSP}",wrapping:false,tooltip:"{ACCEPTED_AT_DSP}"}),
							sortProperty:"ACCEPTED_TIMESTEMP",
							filterProperty:"ACCEPTED_TIMESTEMP",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Expired On"}),
							template: new sap.m.Text({text:"{EXPIRY_DSP}",wrapping:false,tooltip:"{EXPIRY_DSP}"}),
							sortProperty:"EXPIRY_TIMESTEMP",
							filterProperty:"EXPIRY_TIMESTEMP",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Deactivate On"}),
							template: new sap.m.Text({text:"{DEACTIVATE_AT_DSP}",wrapping:false,tooltip:"{DEACTIVATE_AT_DSP}"}),
							sortProperty:"DEACTIVATE_TIMESTEMP",
							filterProperty:"DEACTIVATE_TIMESTEMP",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "180px",
							label 	: new sap.m.Label({text:"Deactivate by"}),
							template: new sap.m.Text({text:"{DEACTIVATE_BY}",wrapping:false,tooltip:"{DEACTIVATE_BY}"}),
							sortProperty:"DEACTIVATE_BY",
							filterProperty:"DEACTIVATE_BY",
							autoResizable:true
						}),
						new sap.ui.table.Column({
							hAlign:"Left",
							width : "250px",
							label 	: new sap.m.Label({text:"Token"}),
							template: new sap.m.Text({text:"{TOKEN}",tooltip:"{TOKEN}",wrapping:false}),
							sortProperty:"TOKEN",
							filterProperty:"TOKEN",
							autoResizable:true,
						})
					]
				})
			]
		}).addStyleClass('sapUiSizeCompact');

		var fn_resent_invitation = {

			data : {},
			action : "",
			title : "",
			confirmation : function(){

				var self = this;
				var lo_data = self.data;
				lv_token = lo_data.getBindingContext().getProperty('TOKEN');
				lv_invite = lo_data.getBindingContext().getProperty('INVITE_ID');
				lv_email = lo_data.getBindingContext().getProperty('EMAIL');

				new sap.m.Dialog({
					title: self.title,
					beginButton: new sap.m.Button({
						text:"Ok",
						type:"Accept",
						icon:"sap-icon://accept",
						press:function(oEvt){

							if(self.action == "resend"){
								self.resend_invitation();
							}
							if(self.action == "deactivate"){
								self.deactivate_invitation();
							}
							oEvt.getSource().getParent().close();

						}
					}),
					endButton:new sap.m.Button({
						text:"Cancel",
						type:"Reject",
						icon:"sap-icon://decline",
						press:function(oEvt){

							oEvt.getSource().getParent().close();
						}
					}),
					content:[
						new sap.m.HBox({
							items:[
								new sap.m.Label({text:"Are you sure to "+self.action+" invitation?"})
							]
						})
					]
				}).open().addStyleClass("sapUiSizeCompact");
			},
			resend_invitation : function(){

				fn_resend_invitation(lv_token,lv_invite,lv_email);
			},
			deactivate_invitation : function(){

				fn_deactivate_invitation(lv_token);
			}
		};

		return lo_page;
	}

	function Page_Right_Function_Text () { //05-05-2019

		var lo_page = new sap.m.Page("PAGE_FUNCTION_TEXT_RIGHT", {
			customHeader: new sap.m.Bar({
				contentLeft: [
					new sap.m.Button({ icon:"sap-icon://nav-back",
						press:function(oEvt){
							
							setTimeout(function(){	
								fn_freeze_table_header();	
							}, 500);

							go_App_Right.back();
						}
					}),
					new sap.m.Button({icon:"sap-icon://menu2",
						press:function(){
							go_SplitContainer.setSecondaryContentWidth("230px");
							if(!go_SplitContainer.getShowSecondaryContent()){
								go_SplitContainer.setShowSecondaryContent(true);
							} else {
								go_SplitContainer.setShowSecondaryContent(false);
							}
						}
					}),
					new sap.m.Image({src: logo_path}),
				],
				contentRight: [ 
					new sap.m.Button("", {
						icon: "sap-icon://home",
						press: function () {
							window.location.href = MainPageLink;
						}
					})
				],
				contentMiddle: [
					new sap.m.Label({
						text: "Function Text"
					})
				],
			}),
			content: [
				new sap.m.Breadcrumbs({
					currentLocationText:"Function Text",
					links:[
						new sap.m.Link({
							text:"Home",
							press:function(oEvt){
								fn_click_breadcrumbs("HOME");
							}
						}),
						new sap.m.Link({
							text:"Account Management",
							press:function(oEvt){

							}
						}),
					]
				}).addStyleClass('breadcrumbs-padding'),
				new sap.m.Bar({
					visible: false,
					enableFlexBox: false,
					contentLeft: [
						new sap.m.HBox({
							width: "100%",
							items: [
								
							]
						}),
					],
				}),
				new sap.ui.table.Table("GO_TABLE_FUNCTION_TEXT",{
					visibleRowCountMode:"Auto",
					selectionMode:"None",
					enableCellFilter: true,
					toolbar: [
						new sap.m.Toolbar({
							content: [
								new sap.m.Label("GO_TABLE_FUNCTION_TEXT_LABEL",{
									text:"Function Text (0)"
								}),
								new sap.m.ToolbarSpacer(),
								new sap.m.Button("ADD_ACTION_FOR_FUNCTION_TEXT",{
									icon: "sap-icon://add",
									visible: false,
									press: function () {
										ui('DIALOG_APPLICATION_FOR_FUNCTION_TEXT').open();
									}
								}),
								new sap.m.Button("SAVE_ACTION_FOR_FUNCTION_TEXT",{
									icon: "sap-icon://save",
									visible: false,
									press: function () {
										ui('FUNCTION_TEXT_DIALOG_CONFIRM').open();
									}
								}),
								new sap.m.Button("DECLINE_ACTION_FOR_FUNCTION_TEXT",{
									icon: "sap-icon://decline",
									visible: false,
									press: function () {
										gv_flag_cancel_from = "EDIT_CANCEL_FUNCTION_TEXT";
										ui("DIALOG_CONFIRM_CANCEL_CHANGES").open();
									}
								}),
								new sap.m.Button("EDIT_ACTION_FOR_FUNCTION_TEXT",{
									visible:true,
									icon: "sap-icon://edit",
									press:function(){

										gt_fn_txt_backup = ui('GO_TABLE_FUNCTION_TEXT').getModel().getData();

										gt_fn_txt_backup = JSON.parse(JSON.stringify(gt_fn_txt_backup));							

										ui('ADD_ACTION_FOR_FUNCTION_TEXT').setVisible(true);
										ui('SAVE_ACTION_FOR_FUNCTION_TEXT').setVisible(true);
										ui('DECLINE_ACTION_FOR_FUNCTION_TEXT').setVisible(true);
										ui('EDIT_ACTION_FOR_FUNCTION_TEXT').setVisible(false);

										fn_bind_data_to_input_fn_txt('edit');
										gv_mode = 'edit';
									}
								}),
							]
						}).addStyleClass('class_transparent_bar'),
					],
					columns : [
						new sap.ui.table.Column("fn_txt_FUNCTION",{
							hAlign:"Left",
							label 	: new sap.m.Label({text:"Function"}),
							template: new sap.m.Text({text:"{FUNCTION}"}),
							sortProperty:"FUNCTION",
							filterProperty:"FUNCTION",
							autoResizable:true
						}),
						new sap.ui.table.Column("fn_txt_FUNCTION_DESC",{
							hAlign:"Left",
							label 	: new sap.m.Label({text:"Description"}),
							sortProperty:"FUNCTION_DESC",
							filterProperty:"FUNCTION_DESC",
							autoResizable:true
						}),
						new sap.ui.table.Column("fn_txt_TSTC",{
							hAlign:"Left",
							label 	: new sap.m.Label({text:"TSTC"}),
							sortProperty:"TSTC",
							filterProperty:"TSTC",
							autoResizable:true
						}),
						new sap.ui.table.Column("fn_txt_ICON",{
							width:"50px",
							hAlign:"Left",
							label 	: new sap.m.Label({text:"Icon"}),
							template: new sap.m.Button({icon: "{ICON}", width:"100%",textAlign:"Left"}),
							autoResizable:true
						}),
						new sap.ui.table.Column("fn_txt_SORT",{
							width:"100px",
							hAlign:"Left",
							label 	: new sap.m.Label({text:"Sort"}),
							sortProperty:"SORT_IND",
							filterProperty:"SORT_IND",
							autoResizable:true
						}),
						new sap.ui.table.Column("fn_txt_Delete",{
							width:"50px",
							hAlign:"Left",
							label 	: new sap.m.Label({text:""}),
							autoResizable:true
						}),
					],
				}),
			],
		}).addStyleClass('sapUiSizeCompact');

		new sap.m.Dialog("FUNCTION_TEXT_DIALOG_CONFIRM",{
			title:"Confirmation",
			contentWidth : "100px",
			beginButton:  new sap.m.Button({
				icon : "sap-icon://accept",
				text : "Ok" ,
				type : "Accept" ,
				press: function(oEvt){

					var lv_fn_txt_data = ui("GO_TABLE_FUNCTION_TEXT").getModel().getData();

					var lv_filltered_data = lv_fn_txt_data.filter(function(item, key){
						return item.FUNCTION_DESC != (gt_fn_txt_backup[key] || {}).FUNCTION_DESC ||
							item.TSTC != (gt_fn_txt_backup[key] || {}).TSTC || 
							item.ICON != (gt_fn_txt_backup[key] || {}).ICON ||
							item.SORT_IND != (gt_fn_txt_backup[key] || {}).SORT_IND;
					});

					fn_save_update_fn_txt(lv_filltered_data);

					oEvt.getSource().getParent().close();

				}
			}),
			endButton: new sap.m.Button({
				icon:"sap-icon://decline",
				text:"Cancel",
				type: "Reject" ,
				press:function(oEvt){
					oEvt.getSource().getParent().close();
				}
			}),
			content:[
				new sap.m.Label("FUNCTION_TEXT_DIALOG_CONFIRM_LABEL", {
					text:"Confirm to save ?",
					textAlign:"Left"
				})
			]
		}).addStyleClass("sapUiSizeCompact");

		var lv_dialog_confirm_delete = new sap.m.Dialog("DELETE_FUNCTION_TEXT_DIALOG_CONFIRM",{ //05-05-2019
			title:"Confirmation",
			contentWidth : "100px",
			beginButton:  new sap.m.Button({
				icon : "sap-icon://accept",
				text : "Ok" ,
				type : "Accept" ,
				press: function(oEvt){

					var lv_index = gv_deleted_fn_txt;

					var lv_function = ui("GO_TABLE_FUNCTION_TEXT").getModel().getData()[lv_index].FUNCTION;
					
					oEvt.getSource().getParent().close();

					fn_delete_function_txt(lv_function).done(function(){
						fn_get_function_text();
						fn_show_notification_message(lv_function + " function removed.");
					});

					
				}
			}),
			endButton: new sap.m.Button({
				icon:"sap-icon://decline",
				text:"Cancel",
				type: "Reject" ,
				press:function(oEvt){
					oEvt.getSource().getParent().close();
				}
			}),
			content:[
				new sap.m.Label({text:"Confirm to delete selected function text ?",textAlign:"Left"})
			]
		}).addStyleClass("sapUiSizeCompact");


		var lo_dialog_application = new sap.m.Dialog("DIALOG_APPLICATION_FOR_FUNCTION_TEXT",{
			contentWidth : "450px",
			contentHeight : "350px",
			stretchOnPhone:false,
			stretch: false,
			verticalScrolling : false,
			beforeOpen: function () {
				ui('INPUT_FUNCTION').setValue('');
				ui('INPUT_FUNCTION_DESCRIPTION').setValue('');
				ui('INPUT_TSTC').setValue('');
				ui('INPUT_ICON').setValue('');
				ui('INPUT_SORT').setValue('');
			},
			customHeader:[
				new sap.m.Bar({
					contentMiddle:[
						new sap.m.Label({text:"New Function"})
					],
				})
			],
			content:[
				new sap.m.FlexBox({
					justifyContent:'Center',
					items:[
						new sap.m.VBox({
							items:[
								new sap.m.Label({text:"Function:", width: "150px"}),
								new sap.m.Input("INPUT_FUNCTION", {
									width: "250px", 
									value:"",
									change: function (oEvt) {
										var lv_value = oEvt.getSource().getValue().trim();
										oEvt.getSource().setValue(lv_value.toUpperCase());
									}
								}),
								new sap.m.Label({text:"Function Description:", width: "150px"}),
								new sap.m.Input("INPUT_FUNCTION_DESCRIPTION", {width: "250px", value:""}),
								new sap.m.Label({text:"TSTC:", width: "150px"}),
								new sap.m.Input("INPUT_TSTC", {
									width:"250px",
									showValueHelp: true,
									valueHelpRequest: function (oEvt) {
										gv_confirm_input = oEvt.getSource().getId();
										fn_bind_function_text_valuehelp(gt_glbmtstc, true);
									},
									change: function (oEvt) {
										var lv_value = oEvt.getSource().getValue().toUpperCase().trim();
										oEvt.getSource().setValue(lv_value);
										
										var lt_tstc_id = [];

										gt_glbmtstc.some(function(item){
											lt_tstc_id.push(item.ID);
										});
										
										var state = $.inArray(lv_value, lt_tstc_id) !== -1 ? null : 'Error';
										oEvt.getSource().setValueState(state);

										ui('BTN_ADD_NEW_FUNCTION').setEnabled(state === null)

									}
								}),
								new sap.m.Label({text:"Icon:", width: "150px"}),
								new sap.m.Input("INPUT_ICON", {width: "250px", value:""}),
								new sap.m.Label({text:"Sort:", width: "150px"}),
								new sap.m.Input("INPUT_SORT", {width: "250px", value:""}),
							]
						})
					]
				})

			],
			buttons: [
				new sap.m.Button("BTN_ADD_NEW_FUNCTION", {icon: "sap-icon://save",
					press: function (oEvt) {
						var lv_function = ui('INPUT_FUNCTION').getValue().toUpperCase().trim();
						var lv_function_desc = ui('INPUT_FUNCTION_DESCRIPTION').getValue().trim();
						var lv_tstc = ui('INPUT_TSTC').getValue().toUpperCase().trim();
						var lv_icon = ui('INPUT_ICON').getValue().trim();
						var lv_sort = ui('INPUT_SORT').getValue().trim();

						var lv_inputs = {
							'FUNCTION' : lv_function,
							'FUNCTION_DESC' : lv_function_desc,
							'TSTC' : lv_tstc,
							'ICON' : lv_icon,
							'SORT_IND' : lv_sort,
						}

						if (lv_inputs.FUNCTION != '' && lv_inputs.FUNCTION_DESC != '' && lv_inputs.TSTC != '') {

							if (fn_check_if_function_name_exists(lv_inputs.FUNCTION)) {
								fn_show_notification_message("Function is already exists");
							} else {
								new sap.m.Dialog({
									title:"Create Function",
									contentWidth : "100px",
									beginButton:  new sap.m.Button({icon:"sap-icon://accept",text:"Ok" , type: "Accept" ,press:function(evt){
										fn_add_data_and_bind(lv_inputs);
										evt.getSource().getParent().close();
										ui("DIALOG_APPLICATION_FOR_FUNCTION_TEXT").close();
									}}),
									endButton: new sap.m.Button({icon:"sap-icon://decline",text:"Cancel", type: "Reject" ,press:function(evt){
										evt.getSource().getParent().close();
									}}),
									content:[
										new sap.m.Label({text:"Confirm to create new function?",textAlign:"Right"})
									]
								}).addStyleClass("sapUiSizeCompact").open();
							}

						} else {

							var lt_missing_fields = [];

							if (lv_inputs.FUNCTION == '') {
								lt_missing_fields.push('Function');
							}
							if (lv_inputs.FUNCTION_DESC == '') {
								lt_missing_fields.push('Function Description');
							}
							if (lv_inputs.TSTC == '') {
								lt_missing_fields.push('TSTC');
							}

							var lv_missing_fields = fn_split_array_to_string(lt_missing_fields);
							var lv_message = "Please input mandatory fields. ("+ lv_missing_fields +").";

							fn_show_notification_message(lv_message);
						}

					},
				}),
				new sap.m.Button({icon: "sap-icon://decline",
					press:function(){
						ui("DIALOG_APPLICATION_FOR_FUNCTION_TEXT").close();
					},
				})
			]

		}).addStyleClass('sapUiSizeCompact');

		return lo_page;
	}

	function page_selection_user_roles () { //06-07-2019

		var lv_width_Label = "150px";
		var lv_width_Label_To = "15px";
		var lv_width_Input = "300px";
		var lv_width_LongInput = "652px";

		var lv_Page  = new sap.m.Page("USER_ROLES_SELECTION_SCREEN",{visible: true}).addStyleClass('sapUiSizeCompact');
		var lv_header = new sap.m.Bar({
			enableFlexBox: false,
			contentLeft:[
				new sap.m.Button({ icon:"sap-icon://nav-back",
					press:function(oEvt){
						window.location.href = MainPageLink;
					}
				}),
				new sap.m.Button({icon:"sap-icon://menu2",
					press:function(){
						go_SplitContainer.setSecondaryContentWidth("250px");
						if(!go_SplitContainer.getShowSecondaryContent()){
							go_SplitContainer.setShowSecondaryContent(true);
						} else {
							go_SplitContainer.setShowSecondaryContent(false);
						}

						// fn_reflow_table_header();
					}
				}),
				new sap.m.Image({src: logo_path}),
			],
			contentMiddle:[gv_Lbl_NewPrdPage_Title = new sap.m.Label({text:"User Roles Selection Screen"})],

			contentRight:[
				fn_help_button(SelectedAppID, "AM_USER_ROLES"),
				new sap.m.Button({
					icon: "sap-icon://home",
					press: function () {
						window.location.href = MainPageLink;
					}
				})
			]
		});

		var lv_crumbs = new sap.m.Breadcrumbs({
			currentLocationText:"Selection Screen",
			links:[
				new sap.m.Link({
					text:"Home",
					press:function(oEvt){
						fn_click_breadcrumbs("HOME");
					}
				}),
				new sap.m.Link({
					text:"User Roles",
					press:function(oEvt){

					}
				})
			]
		}).addStyleClass('breadcrumbs-padding');

		var search_user_roles_btn = new sap.m.VBox({
			justifyContent:"End",
			alignItems:"End",
			items:[
				new sap.m.Button("", {
					icon: "sap-icon://search",
					press: function () {

						if (ui('INPUT_USER_ID_TO').getValueState() === 'Error') {
							return false;
						}

						var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
							busy_diag.open();

						var data = {
							_token : "{!! csrf_token() !!}",
							USER_ID_FROM : ui('INPUT_USER_ID_FROM').getValue(),
							USER_ID_TO : ui('INPUT_USER_ID_TO').getValue(),
							ROLE : ui('INPUT_ROLE').getSelectedKeys(),
							STATUS : ui('INPUT_STATUS').getSelectedKeys(),
							INPUT_WITH_ROLES : ui('INPUT_WITH_ROLES').getState(),
						};

						getFiteredUserRoles(data).done(function(data){

							var model = new sap.ui.model.json.JSONModel();
							model.setData(data);
							ui('COE_TABLE_LISTING').setModel(model).bindRows('/');
				
							fn_clear_table_sorter("COE_TABLE_LISTING");

							ui("LABEL_VPN_DETAIL").setText("User Roles List ("+ data.length +")");

							busy_diag.close();

						});

						go_App_Right.to('USER_ROLE_LISTING_PAGE');

					}
				}).addStyleClass("sapMTB-Transparent-CTX")
			]
		});

		var lv_panel = new sap.m.Panel("",{
			content:[
				//1
				new sap.m.HBox("user-role_HBOX",{
					justifyContent:"Center",
					items:[
						new sap.m.Label({text:"User ID", width:lv_width_Label}).addStyleClass('label-add-padding'),
						new sap.m.Input("INPUT_USER_ID_FROM", {
							value:"",
							width:lv_width_Input,
							showValueHelp:true,
							valueHelpRequest:function(oEvt){
								var lv_id = oEvt.getSource().getId();
								fn_bind_valuehelp_user_roles(lv_id);
							},
							change: function (oEvt) {
								var lv_id = oEvt.getSource().getId();
								var lv_value = oEvt.getSource().getValue().trim();
								fn_check_select_fields_user_roles(lv_value, "INPUT_USER_ID_FROM", "INPUT_USER_ID_TO", "", gt_userids, lv_id);
							}
						}),
						new sap.m.Label({text:"To", width:lv_width_Label_To}).addStyleClass('label-add-padding'),
						new sap.m.Input("INPUT_USER_ID_TO", {
							value:"",
							width:lv_width_Input,
							showValueHelp:true,
							valueHelpRequest:function(oEvt){
								var lv_id = oEvt.getSource().getId();
								fn_bind_valuehelp_user_roles(lv_id);
							},
							change: function (oEvt) {
								var lv_id = oEvt.getSource().getId();
								var lv_value = oEvt.getSource().getValue().trim();
								fn_check_select_fields_user_roles(lv_value, "INPUT_USER_ID_FROM", "INPUT_USER_ID_TO", "", gt_userids, lv_id);
							}
						}),
					]
				}),
				//2
				new sap.m.HBox("",{
					justifyContent:"Center",
					items:[
						new sap.m.Label("",{text:"Role", width:lv_width_Label}).addStyleClass('label-add-padding'),
						new sap.m.MultiComboBox("INPUT_ROLE", {
							width:lv_width_LongInput,
							selectedKeys:[],
						})
					]
				}),
				//3
				new sap.m.HBox("",{
					justifyContent:"Center",
					items:[
						new sap.m.Label("",{text:"User Status", width:lv_width_Label}).addStyleClass('label-add-padding'),
						new sap.m.MultiComboBox("INPUT_STATUS",{
							width:lv_width_LongInput,
							selectedKeys:[],
						})
					]
				}),
				//4
				new sap.m.HBox("",{
					justifyContent:"Center",
					items:[
						new sap.m.Label("",{text:"Display Option", width:lv_width_Label}).addStyleClass('label-add-padding'),
						new sap.m.HBox({width:lv_width_LongInput, items:[
							new sap.m.Switch("INPUT_WITH_ROLES", {
								state: true,
								change: function (oEvt) {
									var value = oEvt.getSource().getState();
									if (value == true) {
										ui('INPUT_WITHOUT_ROLES').setState(false)
									} else {
										ui('INPUT_WITHOUT_ROLES').setState(true)
									}
								}
							}),
							new sap.m.Text({
								text : "User Listing with Roles Assigned"
							}).addStyleClass('label-add-padding'),
						]}),
						
					]
				}),
				//4
				new sap.m.HBox("",{
					justifyContent:"Center",
					items:[
						new sap.m.HBox({width:"465px", items:[
							new sap.m.Switch("INPUT_WITHOUT_ROLES", {
								state: false,
								change: function (oEvt) {
									var value = oEvt.getSource().getState();
									if (value == true) {
										ui('INPUT_WITH_ROLES').setState(false)
									} else {
										ui('INPUT_WITH_ROLES').setState(true)
									}
								}
							}),
							new sap.m.Text({
								text : "User Listing without Roles Assigned"
							}).addStyleClass('label-add-padding'),
						]}),
					]
				}),
			],
		});


		lv_Page.setCustomHeader(lv_header);
		lv_Page.addContent(lv_crumbs);
		lv_Page.addContent(search_user_roles_btn);
		lv_Page.addContent(lv_panel);

		return lv_Page;
	}

	function page_listing_user_roles () { //06-07-2019

		var lv_width_Label = "150px";
		var lv_width_Label_To = "15px";
		var lv_width_Input = "300px";
		var lv_width_LongInput = "652px"

		var lv_Page  = new sap.m.Page("USER_ROLE_LISTING_PAGE",{}).addStyleClass('sapUiSizeCompact');
		var lv_header = new sap.m.Bar({
			enableFlexBox: false,
			contentLeft:[
				new sap.m.Button({ icon:"sap-icon://nav-back",
					press:function(oEvt){
						go_App_Right.to("USER_ROLES_SELECTION_SCREEN");
					}
				}),
				new sap.m.Button({icon:"sap-icon://menu2",
					press:function(){
						go_SplitContainer.setSecondaryContentWidth("250px");
						if(!go_SplitContainer.getShowSecondaryContent()){
							go_SplitContainer.setShowSecondaryContent(true);
						} else {
							go_SplitContainer.setShowSecondaryContent(false);
						}

						fn_reflow_table_header();
					}
				}),
				new sap.m.Image({src: logo_path}),
			],
			contentMiddle:[gv_Lbl_NewPrdPage_Title = new sap.m.Label({text:"User Roles Assignment Listing"})],

			contentRight:[
				fn_help_button(SelectedAppID, "AM_USER_ROLES"),
				new sap.m.Button({
					icon: "sap-icon://home",
					press: function () {
						window.location.href = MainPageLink;
					}
				})
			]
		});

		var lv_crumbs = new sap.m.Breadcrumbs({
			currentLocationText:"User Roles Assignment Listing",
			links:[
				new sap.m.Link({
					text:"Home",
					press:function(oEvt){
						fn_click_breadcrumbs("HOME");
					}
				}),
				new sap.m.Link({
					text:"Account Management",
					press:function(oEvt){

					}
				})
			]
		}).addStyleClass('breadcrumbs-padding');

		var coeListingTable = new sap.ui.table.Table("COE_TABLE_LISTING", {
			selectionMode : "None",
			enableCellFilter : true,
			autoResizeColumn:true,
			visibleRowCountMode:"Auto",
			filter : function(oEvt){

			},
			sort:function(oEvt){

			},
			rowSelectionChange : function (oEvt) {

			},
			toolbar : [
				new sap.m.Toolbar({
					design:"Transparent",
					content:[
						new sap.m.Text("LABEL_VPN_DETAIL",{text: "User Roles List (0)"}),
						new sap.m.ToolbarSpacer(),
						new sap.m.Button("", {
							icon: "sap-icon://refresh",
							press: function () {

								var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
									busy_diag.open();

									var data = {
										_token : "{!! csrf_token() !!}",
										USER_ID_FROM : ui('INPUT_USER_ID_FROM').getValue(),
										USER_ID_TO : ui('INPUT_USER_ID_TO').getValue(),
										ROLE : ui('INPUT_ROLE').getSelectedKeys(),
										STATUS : ui('INPUT_STATUS').getSelectedKeys(),
										INPUT_WITH_ROLES : ui('INPUT_WITH_ROLES').getState(),
									};

								getFiteredUserRoles(data).done(function(data){

									var model = new sap.ui.model.json.JSONModel();
									model.setData(data);
									ui('COE_TABLE_LISTING').setModel(model).bindRows('/');

									fn_clear_table_sorter("COE_TABLE_LISTING");

									ui("LABEL_VPN_DETAIL").setText("User Roles List ("+ data.length +")");

									busy_diag.close();

								});

							}
						}),
						new sap.m.Button("", {
							icon: "sap-icon://download",
							press: function () {
								
								if(ui('COE_TABLE_LISTING').getBinding().iLength > 0) {
									fn_download_user_roles_listing();
								} else {
									fn_show_notification_message("No data to download");
								}

							}
						})
					]
				})
			],
			columns : [

				new sap.ui.table.Column("COL_USER_ID", { //222369
					label : new sap.m.Label({text : "User ID"}),
					icon : "sap-icon://key",
					template: new sap.m.Link({
                        text:"{USER_ID}",
                        press: function (oEvt) {
							var lv_user_id = oEvt.getSource().getBindingContext().getProperty('USER_ID');
							window.localStorage.setItem('USER_ROLE_USER_ID', lv_user_id);
							var lv_window = window.open('management_v2', '_blank');
							lv_window.focus();
                        }
                    }),
					sortProperty:"USER_ID",
					filterProperty:"USER_ID"
				}),

				new sap.ui.table.Column("COL_NAME", {
					label : new sap.m.Label({text : "Display Name"}),
					icon : "sap-icon://key",
					template : new sap.m.Input({
						value: "{name}",
						editable: false,
						tooltip: "{name}"
					}),
					sortProperty:"name",
					filterProperty:"name"
				}),

				new sap.ui.table.Column("COL_EMAIL", {
					label : new sap.m.Label({text : "Email"}),
					icon : "sap-icon://key",
					template : new sap.m.Input({
						value: "{email}",
						editable: false,
						tooltip: "{email}"
					}),
					sortProperty:"email",
					filterProperty:"email"
				}),

				new sap.ui.table.Column("COL_USER_STATUS", {
					label : new sap.m.Label({text : "User Status"}),
					icon : "sap-icon://key",
					template : new sap.m.ObjectStatus({
						icon : "{USER_STATUS_ICON}", 
						state: "{USER_STATUS}",
						tooltip: "{orig_user_status_text}"
					}),
					sortProperty:"USER_STATUS",
					filterProperty:"USER_STATUS"
				}),

				new sap.ui.table.Column("COL_ROLE", {
					label : new sap.m.Label({text : "Role"}),
					icon : "sap-icon://key",
					template : new sap.m.Input({
						value: "{ROLE}",
						editable: false,
						tooltip: "{ROLE}"
					}),
					sortProperty:"ROLE",
					filterProperty:"ROLE"
				}),

				new sap.ui.table.Column("COL_DESCRIPTION", {
					label : new sap.m.Label({text : "Role Description"}),
					icon : "sap-icon://key",
					template : new sap.m.Input({
						value: "{DESCRIPTION}",
						editable: false,
						tooltip: "{DESCRIPTION}"
					}),
					sortProperty:"DESCRIPTION",
					filterProperty:"DESCRIPTION"
				}),

				new sap.ui.table.Column("COL_VALID_FR", {
					label : new sap.m.Label({text : "Validity From"}),
					icon : "sap-icon://key",
					template : new sap.m.Input({
						value: "{VALID_FR_DISPLAY}",
						editable: false,
						tooltip: "{VALID_FR_DISPLAY}"
					}),
					sortProperty:"VALID_FR",
					filterProperty:"VALID_FR_DISPLAY"
				}),

				new sap.ui.table.Column("COL_VALID_TO", {
					label : new sap.m.Label({text : "Validity To"}),
					icon : "sap-icon://key",
					template : new sap.m.Input({
						value: "{VALID_TO_DISPLAY}",
						editable: false,
						tooltip: "{VALID_TO_DISPLAY}"
					}),
					sortProperty:"VALID_TO",
					filterProperty:"VALID_TO_DISPLAY"
				}),

				new sap.ui.table.Column("COL_STATUS", {
					label : new sap.m.Label({text : "Role Status"}),
					icon : "sap-icon://key",
					template : new sap.m.ObjectStatus({
						icon : "{ICON}", 
						state:"{STATUS}", 
						visible:"{ROLE_STATUS_VISIBLE}",
						tooltip: "{orig_status_text}"
					}),
					sortProperty:"STATUS",
					filterProperty:"STATUS"
				})

			]
		});

		lv_Page.setCustomHeader(lv_header);
		lv_Page.addContent(lv_crumbs);
		lv_Page.addContent(coeListingTable);

		return lv_Page;
	}
	
	function Page_Account_Upload(){
		
		var rABS = typeof FileReader !== 'undefined' && FileReader.prototype && FileReader.prototype.readAsBinaryString;
		
		var lv_page = new sap.m.Page("PAGE_ACCOUNT_UPLOAD",{
			customHeader:[
				new sap.m.Bar({
					contentLeft:  [
						new sap.m.Button({ icon:"sap-icon://nav-back",
							press:function(oEvt){
								window.location.href =MainPageLink;
							}
						}),
						new sap.m.Button({icon:"sap-icon://menu2",
							press:function(){
								go_SplitContainer.setSecondaryContentWidth("230px");
								if(!go_SplitContainer.getShowSecondaryContent()){
									go_SplitContainer.setShowSecondaryContent(true);
								} else {
									go_SplitContainer.setShowSecondaryContent(false);
								}
							}
						}),
						new sap.m.Image({src: logo_path}),
					],
					contentMiddle:[new sap.m.Label({text:"Account Management"})],
					contentRight:[
						fn_help_button(SelectedAppID,"AM_ACCUPLOAD"),
						new sap.m.Button({
							icon: "sap-icon://home",
							tooltip: "Home",
							press: function(){
								window.location.href = MainPageLink;
							}
						})
					]
				})
			]
		}).addStyleClass('sapUiSizeCompact');
		
		var lv_crumbs =  new sap.m.Breadcrumbs({
			currentLocationText:"Account Upload",
			links:[
				new sap.m.Link({
					text:"Home",
					press:function(oEvt){
						fn_click_breadcrumbs("HOME");
					}
				}),
				new sap.m.Link({
					text:"Account Management",
					press:function(oEvt){

					}
				})
			]
		}).addStyleClass('breadcrumbs-padding');
		
		lv_page.addContent(lv_crumbs);
		
		var lv_message_strip = new sap.m.VBox("MESSAGE_STRIP_UPLOAD_ACCCOUNT");
		
		lv_page.addContent(lv_message_strip);
		
		// VALIDATION
		var lo_user_validation_table = new sap.ui.table.Table("TABLE_USER_VALIDATION",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount:5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Status"}),
					width:"80px",
					template:new sap.m.ObjectStatus({icon:"sap-icon://status-completed", state:"{state}"}),
					sortProperty:"state",
					filterProperty:"state",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					width:"120px",
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Email"}),
					width:"200px",
					template:new sap.m.Text({text: "{email}",tooltip:"{email}",maxLines:1}),
					sortProperty:"email",
					filterProperty:"email",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Display Name"}),
					width:"150px",
					template:new sap.m.Text({text: "{disp_name}"}),
					sortProperty:"disp_name",
					filterProperty:"disp_name",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"First Name"}),
					width:"100px",
					template:new sap.m.Text({text: "{firstname}"}),
					sortProperty:"firstname",
					filterProperty:"firstname",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Last Name"}),
					width:"100px",
					template:new sap.m.Text({text: "{lastname}"}),
					sortProperty:"lastname",
					filterProperty:"lastname",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Validation Message"}),
					template:new sap.m.Text({text:"{message}",tooltip:"{message}",maxLines:1}),
					sortProperty:"message",
					filterProperty:"message",
					autoResizable:true,
				}),
			]
		});
		
		var lo_auth_validation_table = new sap.ui.table.Table("TABLE_AUTH_VALIDATION",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount:5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Status"}),
					width:"80px",
					template:new sap.m.ObjectStatus({icon:"sap-icon://status-completed", state:"{state}"}),
					sortProperty:"state",
					filterProperty:"state",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					width:"120px",
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Auth Role"}),
					width:"150px",
					template:new sap.m.Text({text: "{role}"}),
					sortProperty:"role",
					filterProperty:"role",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Valid From"}),
					width:"120px",
					template:new sap.m.Text({text: "{valid_from}"}),
					sortProperty:"valid_from",
					filterProperty:"valid_from",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Valid To"}),
					width:"120px",
					template:new sap.m.Text({text: "{valid_to}"}),
					sortProperty:"valid_to",
					filterProperty:"valid_to",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Validation Message"}),
					template:new sap.m.Text({text:"{message}",tooltip:"{message}",maxLines:1}),
					sortProperty:"message",
					filterProperty:"message",
					autoResizable:true,
				}),
			]
		});
		
		var lo_param_validation_table = new sap.ui.table.Table("TABLE_PARAM_VALIDATION",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount : 5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Status"}),
					width:"80px",
					template:new sap.m.ObjectStatus({icon:"sap-icon://status-completed", state:"{state}"}),
					sortProperty:"state",
					filterProperty:"state",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					width:"120px",
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Param ID"}),
					width:"150px",
					template:new sap.m.Text({text: "{param_id}"}),
					sortProperty:"param_id",
					filterProperty:"param_id",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Value"}),
					width:"100px",
					template:new sap.m.Text({text: "{value}"}),
					sortProperty:"value",
					filterProperty:"value",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Validation Message"}),
					template:new sap.m.Text({text:"{message}",tooltip:"{message}",maxLines:1}),
					sortProperty:"message",
					filterProperty:"message",
					autoResizable:true,
				}),
			]
		});
		
		var lo_bizpart_validation_table = new sap.ui.table.Table("TABLE_BIZPART_VALIDATION",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount : 5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Status"}),
					width:"80px",
					template:new sap.m.ObjectStatus({icon:"sap-icon://status-completed", state:"{state}"}),
					sortProperty:"state",
					filterProperty:"state",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					width:"120px",
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"BIZ Partner"}),
					width:"120px",
					template:new sap.m.Text({text: "{biz_partner}"}),
					sortProperty:"biz_partner",
					filterProperty:"biz_partner",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Partner No"}),
					width:"150px",
					template:new sap.m.Text({text: "{partner_no}"}),
					sortProperty:"partner_no",
					filterProperty:"partner_no",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Validation Message"}),
					template:new sap.m.Text({text:"{message}",tooltip:"{message}",maxLines:1}),
					sortProperty:"message",
					filterProperty:"message",
					autoResizable:true,
				}),
			]
		});
		
		// REVIEW
		var lo_user_review_table = new sap.ui.table.Table("TABLE_USER_REVIEW",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount:5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Email"}),
					template:new sap.m.Text({text: "{email}"}),
					sortProperty:"email",
					filterProperty:"email",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Display Name"}),
					template:new sap.m.Text({text: "{disp_name}"}),
					sortProperty:"disp_name",
					filterProperty:"disp_name",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"First Name"}),
					template:new sap.m.Text({text: "{firstname}"}),
					sortProperty:"firstname",
					filterProperty:"firstname",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Last Name"}),
					template:new sap.m.Text({text: "{lastname}"}),
					sortProperty:"lastname",
					filterProperty:"lastname",
					autoResizable:true,
				}),
			]
		});
		
		var lo_auth_review_table = new sap.ui.table.Table("TABLE_AUTH_REVIEW",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount:5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Auth Role"}),
					template:new sap.m.Text({text: "{role}"}),
					sortProperty:"role",
					filterProperty:"role",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Valid From"}),
					template:new sap.m.Text({text: "{valid_from}"}),
					sortProperty:"valid_from",
					filterProperty:"valid_from",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Valid To"}),
					template:new sap.m.Text({text: "{valid_to}"}),
					sortProperty:"valid_to",
					filterProperty:"valid_to",
					autoResizable:true,
				}),
			]
		});
		
		var lo_param_review_table = new sap.ui.table.Table("TABLE_PARAM_REVIEW",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount : 5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Param ID"}),
					template:new sap.m.Text({text: "{param_id}"}),
					sortProperty:"param_id",
					filterProperty:"param_id",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Value"}),
					template:new sap.m.Text({text: "{value}"}),
					sortProperty:"value",
					filterProperty:"value",
					autoResizable:true,
				}),
			]
		});
		
		var lo_bizpart_review_table = new sap.ui.table.Table("TABLE_BIZPART_REVIEW",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			visibleRowCount : 5,
			columns:[
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Username"}),
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"BIZ Partner"}),
					template:new sap.m.Text({text: "{biz_partner}"}),
					sortProperty:"biz_partner",
					filterProperty:"biz_partner",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Partner No"}),
					template:new sap.m.Text({text: "{partner_no}"}),
					sortProperty:"partner_no",
					filterProperty:"partner_no",
					autoResizable:true,
				}),
			]
		});
		
		// SUMMARY
		var lo_user_summary_table = new sap.ui.table.Table("TABLE_ACCOUNT_UPLOAD_USER_SUMMARY",{
			selectionMode:"None",
			enableCellFilter: true,
			autoResizeColumn:true,
			minAutoRowCount : 5,
			cellClick:function(oEvt){
				
				var lv_user_id = oEvt.getParameters().rowBindingContext.getProperty("username");
				var lv_base64 = btoa(lv_user_id);
				
				var lv_targetURL = '/admin/upload_user/show_user/' + lv_base64;
				
				var win = window.open(lv_targetURL, '_blank');
					win.focus();
					
			},
			columns:[
				new sap.ui.table.Column({label:new sap.m.Text({text:"User ID"}),
					//width:"100px",
					template:new sap.m.Text({text: "{username}"}),
					sortProperty:"username",
					filterProperty:"username",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Display Name"}),
					//width:"150px",
					template:new sap.m.Text({text: "{name}"}),
					sortProperty:"name",
					filterProperty:"name",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"First Name"}),
					//width:"150px",
					template:new sap.m.Text({text: "{firstname}"}),
					sortProperty:"firstname",
					filterProperty:"firstname",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Last Name"}),
					//width:"150px",
					template:new sap.m.Text({text: "{lastname}"}),
					sortProperty:"lastname",
					filterProperty:"lastname",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Status"}),
					//width:"75px",
					template:new sap.m.Text({text: "{status_desc}"}),
					sortProperty:"status_desc",
					filterProperty:"status_desc",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Creation Date"}),
					//width:"150px",
					template:new sap.m.Text({text:"{creation_date}"}),
					sortProperty:"created_at",
					filterProperty:"creation_date",
					autoResizable:true,
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Creation Time"}),
					//width:"150px",
					template:new sap.m.Text({text:"{creation_time}"}),
					sortProperty:"creation_time",
					filterProperty:"creation_time",
					autoResizable:true,
				}),
			]
		});
		
		var lo_wizard = new sap.m.Wizard("WIZARD",{
			//visible:false,
			complete: function(){
				ui("LEFT_MENU_TEMPLATE-lv_menu_list-0").firePress();
				gv_default_menu = "AM_ACCMAIN";
			},
			steps :[
				new sap.m.WizardStep("WIZARD_STEP_1",{
					title : "Upload",
					validated: false,
					activate:function(){
					},
					content:[
						new sap.m.VBox("CONTAINER_STEP_1",{
							items:[
								new sap.m.HBox({
									items:[
										new sap.ui.unified.FileUploader("INPUT_UPLOADER_COMPLEX",{
											multiple: true,
											name: "fileUploader",	// name of the input type=file element within the form
											uploadOnChange: false,	// immediately upload the file after selection
											sameFilenameAllowed:true,
											fileAllowed:function(oEvt){
												ui('MESSAGE_STRIP_UPLOAD_ACCCOUNT').destroyItems()
												oEvt.getSource().setValueState("None")
											},
											typeMissmatch:function(oEvt){
												console.log(oEvt.getSource())
												oEvt.getSource().setValueState("Error")
												ui("MESSAGE_STRIP_UPLOAD_ACCCOUNT").addItem(
													new sap.m.MessageStrip({
														text: "Please upload excel ext files only",
														type:"Error",
														showIcon: true,
														showCloseButton: true,
														close:function(){
															ui('INPUT_UPLOADER_COMPLEX').setValueState("None")
														}
													})
												);
											},
											fileType:['xlsx','xlsm','xlsb','xltx','xltm','xlt','xls','xml','xlam','xla','xlw','xlr','csv'],
											change : function(e){
												gt_file_data = [];
												var file = e.getParameter("files") && e.getParameter("files")[0];
												go_upload_file_data = file;
												if(file && window.FileReader){
													var reader = new FileReader();  
													var that = this;  
													var name = file.name;
													reader.onload = function(e) {
														var data = e.target.result;
														var wb, arr;
														var readtype = {type: rABS ? 'binary' : 'base64' };
														if(!rABS) {
															arr = fixdata(data);
															data = btoa(arr);
														}
														wb = XLSX.read(data, readtype);
														console.log(wb)
														var result = {};
														wb.SheetNames.forEach(function(sheetName) {
															var roa = XLSX.utils.sheet_to_json(wb.Sheets[sheetName], {raw:false, header:1});
															if(roa.length > 0) result[sheetName] = roa;
														});
														
														console.log(result)
														gt_upload_users = result;
														
														setTimeout(function(){ui("WIZARD-nextButton").setText("Data Validation");},1);
														ui("WIZARD").discardProgress(ui("WIZARD_STEP_1"));
														ui("WIZARD_STEP_1").setValidated(true);
														
													};
													
													if(rABS) reader.readAsBinaryString(file);
													else reader.readAsArrayBuffer(file);
													console.log(reader)
												}
											}
										
										}),
										new sap.m.Button("BUTTON_DOWNLOAD_ACCOUNT_UPLOAD_TEMPLATE",{
											type:"Emphasized",
											text:"Download Template",
											press:function(){
											   fn_download_account_upload_template();
											}
										
										})
									]
								}),
								new sap.m.RadioButtonGroup({
									columns:2,
									buttons:[
										new sap.m.RadioButton({
											text: "Normal Account Creation"
										}),
										new sap.m.RadioButton({
											text: "Creation Using Invite"
										})
									]
								}),
								new sap.m.HBox({
									items:[
										new sap.m.Label({text:"Status:",visible:true}).addStyleClass('class_label_padding_status'),
										new sap.m.Switch("INPUT_ACCOUNT_UPLOAD_STATUS",{state: true, type: sap.m.SwitchType.AcceptReject}),
									]
								}),
								new sap.m.HBox({
									items:[
										new sap.m.Label({text:"Send Welcome Email:",visible:true}).addStyleClass('class_label_padding_status'),
										new sap.m.Switch("INPUT_ACCOUNT_UPLOAD_SEND",{state: true, type: sap.m.SwitchType.AcceptReject}),
									]
								}),
							]
						}),
					]
				}),
				new sap.m.WizardStep("WIZARD_STEP_2",{
					title : "Data Validation",
					validated: false,
					activate:function(){
						ui('TABLE_USER_VALIDATION').unbindRows();
						ui('TABLE_AUTH_VALIDATION').unbindRows();
						ui('TABLE_PARAM_VALIDATION').unbindRows();
						ui('TABLE_BIZPART_VALIDATION').unbindRows();
						fn_validate_account_upload();
					},
					content:[
						new sap.m.VBox("CONTAINER_STEP_2",{
							items:[
								new sap.m.VBox({items:[
									new sap.m.Label("LABEL_VALIDATION_USER_COUNT",{text:""}),
									lo_user_validation_table,
								]}),
								new sap.m.VBox({items:[
									new sap.m.Label("LABEL_VALIDATION_AUTH_COUNT",{text:""}),
									lo_auth_validation_table,
								]}).addStyleClass('vbox-add-padding'),
								new sap.m.VBox({items:[
									new sap.m.Label("LABEL_VALIDATION_PARAM_COUNT",{text:""}),
									lo_param_validation_table,
								]}).addStyleClass('vbox-add-padding'),
								new sap.m.VBox({items:[
									new sap.m.Label("LABEL_VALIDATION_BIZPART_COUNT",{text:""}),
									lo_bizpart_validation_table,
								]}).addStyleClass('vbox-add-padding'),
								new sap.m.Button({
									type:"Emphasized",
									text:"Re-Upload",
									press:function(){
									   ui("WIZARD").discardProgress(ui("WIZARD_STEP_1"));
									}
								}),
							]
						}),
					]
				}),
				new sap.m.WizardStep("WIZARD_STEP_3",{
					title : "Review",
					validated: false,
					activate:function(){
						fn_show_upload_account_review(go_upload_file_data);
					},
					content:[
						new sap.m.VBox({items:[
							new sap.m.Label("LABEL_REVIEW_USER_COUNT",{text:""}),
							lo_user_review_table
						]}),
						new sap.m.VBox({items:[
							new sap.m.Label("LABEL_REVIEW_AUTH_COUNT",{text:""}),
							lo_auth_review_table
						]}).addStyleClass('vbox-add-padding'),
						new sap.m.VBox({items:[
							new sap.m.Label("LABEL_REVIEW_PARAM_COUNT",{text:""}),
							lo_param_review_table
						]}).addStyleClass('vbox-add-padding'),
						new sap.m.VBox({items:[
							new sap.m.Label("LABEL_REVIEW_BIZPART_COUNT",{text:""}),
							lo_bizpart_review_table
						]}).addStyleClass('vbox-add-padding'),
					]
				}),
				new sap.m.WizardStep("WIZARD_STEP_4",{
					title : "Summary",
					validated: false,
					activate:function(){
						fn_process_account_upload();
					},
					content:[
						new sap.m.VBox("CONTAINER_STEP_4",{
							items:[
								lo_user_summary_table
							]
						}),
					]
				}),
			]
		});

		lv_page.addContent(lo_wizard);

		return lv_page;

	}

</script>
