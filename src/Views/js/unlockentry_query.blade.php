<script type="text/javascript">

var gt_user_role_bk = [];
var gt_user_param_bk = [];
var gt_upload_users = [];
var go_upload_file_data = [];

var gt_validate_user = [];
var gt_validate_auth = [];
var gt_validate_param = [];
var gt_validate_bizpart = [];


/*
// ================================================================================
// Function to GET AUTHORIZATION
// ================================================================================
*/	
	function fn_get_authorization(gt_list){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lv_data = {
			APP_ID:SelectedAppID
		};

		fn_ajax_call(
			"/admin/users/management_v2/get_authorization" , 
			"GET",
			lv_data,
			function (response){
				if(response.return.error){
					console.log(response);
					fn_show_notification_message(response.return.message);
					setTimeout(function(){
						window.location.href = MainPageLink;
					},1000);
				}else {
					
					if(response.return.status == "01"){
						gv_approver_edit = response.lv_edit;
						for(var j=0; j < gt_list.length; j++){
							//get the authorized functions
							for(var i =0; i < response.apps.length; i++){
								if(response.apps[i].VALUE == gt_list[j].funct){
									gt_list[j].visible = true;
								}
							}	
						}

						var model = new sap.ui.model.json.JSONModel();
							model.setSizeLimit(gt_list.length);
							model.setData(gt_list);

						ui('lv_menu_list').setModel(model).bindAggregation("items",{
							path:"/",
							template:ui('LEFT_MENU_TEMPLATE')
						});
						
						fn_goto_initial_page();
		
					}else{
						
						var model = new sap.ui.model.json.JSONModel();
							model.setSizeLimit(gt_list.length);
							model.setData(gt_list);
		
						ui('lv_menu_list').setModel(model).bindAggregation("items",{
							path:"/",
							template:ui('LEFT_MENU_TEMPLATE')
						});
					}
					
				}

				busy_diag.close();
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
				busy_diag.close();
			}
		);

	}
	
	function fn_check_authorization(lv_funct){
		var lv_return = false;
		for(var i=0; i < gt_list.length; i++){
			if(gt_list[i].funct == lv_funct && gt_list[i].visible == true){
				lv_return = true;
				break;
			}
		}
		return lv_return;
	}

	function fn_prep_data_to_set_account_details(lv_id,lv_user_id){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lv_data = {
			APP_ID		: SelectedAppID,
			ID			: lv_id,
			USER_ID		: lv_user_id,
		};
		
		fn_ajax_call(
			"/admin/users/management_v2/prep_data_to_set_account_details",
			"GET",		
			lv_data,
			function(response) {
		
				if (response.return.status=="01") {
					
					// user info
					gt_user_info = response.lt_user_info.lt_data;
					
					// partner tab
					gt_user_role = response.lt_user_role.lt_data;
					gt_user_role_bk = JSON.parse(JSON.stringify(gt_user_role));
					
					// partner type
					gt_user_param = response.lt_user_param.lt_data;
					gt_user_param_bk = JSON.parse(JSON.stringify(gt_user_param));

					//  biz partner
					gt_biz_partner = response.lt_partner.biz_partner;
					gt_biz_partner_bk = JSON.parse(JSON.stringify(gt_biz_partner));
					
					//country code
					gt_country_code = response.lt_country_code.lt_data;
					//partner type
					gv_partner_type_vhelp = response.lt_partner.partner_type;
					
					//userparam values
					gt_userparam_values = response.lt_userparam_values;
					
					fn_BIND_USER_INFO(gt_user_info);
					fn_BIND_USER_ROLE(gt_user_role,"TEXT");
					fn_BIND_USER_PARAM(gt_user_param,"TEXT");
					fn_bind_country_code(gt_country_code);
					fn_bind_biz_partner(gt_biz_partner,"TEXT");
					fn_bind_partner_type(gv_partner_type_vhelp);
					
					setTimeout(function(){
						busy_diag.close();
						
						setTimeout(function() { 		
							$('.sapUxAPObjectPageHeaderObjectImage').click(function(){ 
								var lv_url = ui("ACCT_MNT_DISPLAY_PHOTO").getText();
								ui("SHOW_USER_PHOTO").setSrc(lv_url);
								ui("DIALOG_SHOW_PHOTO").open();
							});
						}, 500)
						
					},500);
					
					go_App_Right.to("Page_Acct_Maint_RUD");					
					go_SplitContainer.setShowSecondaryContent(false);
					
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}
	
	//Check if the userparam already exists

	function fn_validate_userparam(){
		var lv_return = "";
		var lv_param_value = "";
		var lv_param_exist = "";
		var lv_param_key = "";
		
		
		var lv_param_table_data = ui('ACCT_MNT_DSP_TABLE_ACCTPREF').getModel().getData();
		
		if(lv_param_table_data.length > 0){
				for(var i=0; i<lv_param_table_data.length; i++){
					if(lv_param_table_data[i].param_id == 'UP_MOBID_U'){
						lv_param_value = lv_param_table_data[i].param_value;
						lv_param_key = i;
					
						for(var x=0; x<gt_userparam_values.length; x++){
							if(lv_param_table_data[i].param_value == gt_userparam_values[x]){
								lv_param_exist = "exist";
							}else{
								//lv_param_exist = "not_exist";
							}
						}
					}
				}

				if(lv_param_value.length > 0 ){

						if(lv_param_value.length === 3){
					
						}else{
							ui('MESSAGE_STRIP_PANEL_CONTRACT_DISPLAY').destroyContent().setVisible(false);
							var lv_message_strip = fn_show_message_strip("User Mobile Person ID value must be 3 characters");
							ui('MESSAGE_STRIP_PANEL_CONTRACT_DISPLAY').addContent(lv_message_strip).setVisible(true);
							lv_return = true;	
						}
				}
			
			if(lv_param_exist == "exist"){
				lv_return = true;
				lv_param_table_data[lv_param_key]['VALUE_STATE'] = "Error";
				ui('ACCT_MNT_DSP_TABLE_ACCTPREF').getModel().refresh();
				ui('MESSAGE_STRIP_PANEL_CONTRACT_DISPLAY').destroyContent().setVisible(false);
				var lv_message_strip = fn_show_message_strip("User Mobile Person ID is already in use");
				ui('MESSAGE_STRIP_PANEL_CONTRACT_DISPLAY').addContent(lv_message_strip).setVisible(true);
			}else{
				
			}
		}
		
		
		return lv_return;
	
	}
	

/*
// ================================================================================
// Function to UPDATE USER DETAILS
// ================================================================================
*/
	function fn_update_user_details(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lv_user_id = ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle();
		var lv_status_text = ui("ACCT_MNT_DSP_TEXT_STATUS").getText();
		var lv_status = "";
		var lv_del_flag = "";
		var lv_mobile_no = "";
			
		switch(lv_status_text){
			
			case "Active" :
				lv_status = "01";
			break;
			
			case "Inactive" :
				lv_status = "02";
			break;
			
			case "Deleted" :
				lv_status = "03";
				lv_del_flag = "X";
			break;
			
			case "Locked" :
				lv_status = "04";
			break;
			
		}
		
		var lv_select_mobile_no = ui('ACCT_MNT_DSP_SELECT_MOBILE_NO').getValue();
		var lv_input_mobile_no = ui('ACCT_MNT_DSP_INPT_MOBILE_NO').getValue();
		
		if(lv_select_mobile_no){
			lv_mobile_no = lv_select_mobile_no + "-" + lv_input_mobile_no;
		}
		
		//get user info
		var lt_user_info = {
			id			: ui('ACCT_MNT_DSP_TEXT_ID').getText(),
			username	: ui('ACCT_MNT_DSP_INPT_USERID').getValue(),
			email		: ui('ACCT_MNT_DSP_INPT_EMAIL').getValue(),
			name 		: ui('ACCT_MNT_DSP_INPT_DSPNAME').getValue(),
			fname		: ui('ACCT_MNT_DSP_INPT_FNAME').getValue(),
			lname		: ui('ACCT_MNT_DSP_INPT_LNAME').getValue(),
			mobile_no	: lv_mobile_no,
			status		: lv_status,
			del_flag	: lv_del_flag
		};
		
		//get user parameters
		var lt_user_param_insert = [];
		var lt_user_param_update = [];
		for(var i=0; i < gt_user_param.length; i++){
			
			var lv_insert = true;
			var lv_update = false;
			
			for(var x=0; x < gt_user_param_bk.length; x++){
				
				if(gt_user_param_bk[x].user_id === gt_user_param[i].user_id && gt_user_param_bk[x].param_id === gt_user_param[i].param_id){
					
					if(gt_user_param_bk[x].param_value !== gt_user_param[i].param_value){
						lv_update = true;
					}
					
					lv_insert = false;
					break;
				}
				
			}
			
			if(lv_insert == true){
				lt_user_param_insert.push({
					USER_ID		: gt_user_param[i].user_id,
					PARAM_ID	: gt_user_param[i].param_id,
					VALUE		: gt_user_param[i].param_value,
				});
			}else if(lv_update == true){
				lt_user_param_update.push({
					USER_ID		: gt_user_param[i].user_id,
					PARAM_ID	: gt_user_param[i].param_id,
					VALUE		: gt_user_param[i].param_value,
				});
			}
			
			
		}
		
		//get user role
		var lt_user_role_insert = [];
		var lt_user_role_update = [];
		for(var x=0; x < gt_user_role.length; x++){
			
			var lv_status = ""
			if(gt_user_role[x].radio_indx == false){
				lv_status = "02";
			}else{
				lv_status = "01";
			}
			
			if(gt_user_role[x].id !== ""){
				
				for(var i=0; i < gt_user_role_bk.length; i++){
					
					if(gt_user_role_bk[i].id === gt_user_role[x].id){
						
						if(
							gt_user_role_bk[i].status !== lv_status ||
							gt_user_role_bk[i].valid_fr !== gt_user_role[x].valid_fr ||
							gt_user_role_bk[i].valid_to !== gt_user_role[x].valid_to ||
							gt_user_role_bk[i].role !== gt_user_role[x].role 
						){
							
							lt_user_role_update.push({
								ID			: gt_user_role[x].id,
								USER_ID		: gt_user_role[x].user_id,
								ROLE		: gt_user_role[x].role,
								STATUS		: lv_status,
								VALID_FR	: gt_user_role[x].valid_fr,
								VALID_TO	: gt_user_role[x].valid_to,
							});
						}
						
						break;
					}
				}
				
			}else{
				
				lt_user_role_insert.push({
					ID			: gt_user_role[x].id,
					USER_ID		: gt_user_role[x].user_id,
					ROLE		: gt_user_role[x].role,
					STATUS		: lv_status,
					VALID_FR	: gt_user_role[x].valid_fr,
					VALID_TO	: gt_user_role[x].valid_to,
				});
				
			}
		}

		//get biz partner
		var lt_biz_partner_insert = [];
		var lt_biz_partner_update = [];
		for(var x=0; x < gt_biz_partner.length; x++){
			
			var lv_status = ""
			if(gt_biz_partner[x].STATUS_STATE == false){
				lv_status = "02";
			}else{
				lv_status = "01";
			}
			
			if(gt_biz_partner[x].id !== ""){
				
				for(var i=0; i < gt_biz_partner_bk.length; i++){
					
					if(gt_biz_partner_bk[i].id === gt_biz_partner[x].id){
						
						if(
							
							gt_biz_partner_bk[i].BP_TYPE !== gt_biz_partner[x].BP_TYPE ||
							gt_biz_partner_bk[i].PARTNER_NO !== gt_biz_partner[x].PARTNER_NO ||
							gt_biz_partner_bk[i].STATUS !== lv_status
						){
							
							lt_biz_partner_update.push({
								id 			: gt_biz_partner[x].id,
								TYPE 		: gt_biz_partner[x].TYPE,
								OBJ_ID		: gt_biz_partner[x].OBJ_ID,
								BP_TYPE		: gt_biz_partner[x].BP_TYPE,
								PARTNER_NO	: gt_biz_partner[x].PARTNER_NO,
								STATUS		: lv_status,
							});
						}
						
						break;
					}
				}
				
			}else{
				
				lt_biz_partner_insert.push({

					id 			: gt_biz_partner[x].id,
					TYPE 		: gt_biz_partner[x].TYPE,
					OBJ_ID		: ui('ACCT_MNT_DSP_INPT_USERID').getValue(),
					BP_TYPE		: gt_biz_partner[x].BP_TYPE,
					PARTNER_NO	: gt_biz_partner[x].PARTNER_NO,
					STATUS		: lv_status,
				});
				
			}
		}
		
		var lt_array = {
			APP_ID				: SelectedAppID,
			user_info			: lt_user_info,
			user_param_insert	: lt_user_param_insert,
			user_param_update	: lt_user_param_update,
			user_role_insert	: lt_user_role_insert,
			user_role_update	: lt_user_role_update,
			user_param_deleted	: gt_user_param_deleted,
			user_role_deleted	: gt_user_role_deleted,
			user_photo			: gt_upload_photo,
			biz_partner_insert 	: lt_biz_partner_insert,
			biz_partner_update 	: lt_biz_partner_update,
			biz_partner_delete 	: gt_delete_biz_partner
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/update_user_details",
			"POST",
			lt_array,
			function(result){
			
				if (result.return.status=="01") {
					
					gt_user_param_deleted = [];
					gt_user_role_deleted = [];
					gt_delete_biz_partner = [];
					
					var evtDesc = "Save on Userid " + sap.ui.getCore().byId('ACCT_MNT_DSP_INPT_USERID').getValue();
					$.get( "/admin/event/trace", { app: SelectedAppID, fn: "USRACC_SAVE" ,at: "USER_ACTION", evt: evtDesc});
					
					var lv_id = ui('ACCT_MNT_DSP_TEXT_ID').getText();
					fn_prep_data_to_set_account_details(lv_id,lv_user_id);
					
					fn_show_notification_message(gt_Global_Message.T04);
					busy_diag.close();
					
				}else {			
					fn_show_notification_message("Failed to save changes.");
					busy_diag.close();
				}
			
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}

/*
// ================================================================================
// Function to RESET PASSWORD
// ================================================================================
*/	
	function fn_SET_GLBMUSER_RESETPASS(){
		
		var lv_selected_id = ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle();
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		fn_ajax_call(
			"/login/identify",
			'POST',
			{ valueId : lv_selected_id },
			function(data){
				
				if(data.status == "01"){
					var evtDesc 	= "Password reset on this UserId. " + sap.ui.getCore().byId('ACCT_MNT_DSP_INPT_USERID').getValue();
					$.get( "/admin/event/trace", { app: SelectedAppID, fn: "USRACC_RESET" ,at: "USER_ACTION", evt: evtDesc});
					fn_show_notification_message("New password has been sent to the registered email address");
				}else{
					fn_show_notification_message("Failed to reset password.");
				}
				
				busy_diag.close();
				
			},
			function(XHR, textStatus, errorThrown){
				fn_show_notification_message("Failed to reset password.");
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);

	}

/*
// ================================================================================
// Function to CHANGE PASSWORD
// ================================================================================
*/	
	function fn_SET_GLBMUSER_CHANGEPASS(){
		
		var lv_username = ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle();
		var lv_pass 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_NEW_PASS');
		var lv_passCn 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_CONFRM_PASS');
		var lv_genPass 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_SETINTLPASS');
		var lv_sendEm 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_SENDEMAIL');

		
		var urlToPost = '/admin/users/password/change';

		var dataToSend = {	
			username	: lv_username,
			password	: lv_pass.getValue(),
			genpass 	: lv_genPass.getSelected(),
			newPass 	: lv_pass.getValue(),
			cnewPass 	: lv_passCn.getValue(),
			sendmail 	: lv_sendEm.getSelected(),
			'password_confirmation':lv_passCn.getValue()
		};
		
		callService.postData(urlToPost,dataToSend,function(callBack){
	
			// console.log(callBack);
			//console.log(callBack.error);

			if(callBack.error == true ){

				ui('MESSAGE_STRIP_PANEL_ST').setVisible(true);
				fn_generate_msge_strip("MESSAGE_STRIP_PANEL_ST", "PASS_CHANGE_ERR", callBack.message, true);

				console.log(callBack);
				// fn_show_notification_message(callBack.message);
				//busy_diag.close();
			}else{
				var evtDesc = "Successfully Change/Reset Password on this Userid " +  ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle();
				$.get( "/admin/event/trace", { app: SelectedAppID, fn: "USRACC_CHANGE" ,at: "USER_ACTION", evt: evtDesc});

				ui("ACCT_MNT_DSP_DIALOG_CHANGEPASS").close();
				
				fn_show_notification_message("Sucessfully Change Password");
				//busy_diag.close();
			}
		});
		

	}	

	function fn_checking_change_pass(){
		
		var lv_username = ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle();
		var lv_pass 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_NEW_PASS');
		var lv_passCn 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_CONFRM_PASS');
		var lv_genPass 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_SETINTLPASS');
		var lv_sendEm 	= sap.ui.getCore().byId('Acct_Maint_RUD_CHNGEPASS_SENDEMAIL');

		
		var urlToPost = '/admin/account_info/checking_change_pass';

		var dataToSend = {	
			username	: lv_username,
			newPass 	: lv_pass.getValue(),
			cnewPass 	: lv_passCn.getValue(),
			genpass 	: lv_genPass.getSelected(),
			sendmail 	: lv_sendEm.getSelected(),
			'password_confirmation':lv_passCn.getValue()
		};
		
		callService.postData(urlToPost,dataToSend,function(callBack){
	
			// console.log(callBack);
			//console.log(callBack.error);

			if(callBack.error == true ){
				// console.log(callBack);
				ui('MESSAGE_STRIP_PANEL_ST').setVisible(true);
				 fn_generate_msge_strip("MESSAGE_STRIP_PANEL_ST", "PASS_CHANGE_ERR", callBack.message, true);
				// fn_show_notification_message(callBack.message);
				//busy_diag.close();
			}else{
				
				new sap.m.Dialog({
						title:"Confirmation",
						//showHeader	:false,
						contentWidth : "100px",
						beginButton:  new sap.m.Button({icon:"sap-icon://accept",text:"Ok" , type: "Accept" ,press:function(evt){

							fn_SET_GLBMUSER_CHANGEPASS();
							// ui("ACCT_MNT_DSP_DIALOG_CHANGEPASS").close();
							evt.getSource().getParent().close();
							}}),
							endButton: new sap.m.Button({icon:"sap-icon://decline",text:"Cancel", type: "Reject" ,press:function(evt){
									evt.getSource().getParent().close();
							}}),
									content:[
										new sap.m.FlexBox({
											items:[
												new sap.m.Label({text:"Confirm to change password?",textAlign:"Right"})
											]
										})
									]
							}).addStyleClass("sapUiSizeCompact").open();
			}
		});
		

	}

/*
// ================================================================================
// Function to CHECK UP_MOBID_U AND VALUE IS NOT EXISTING IN TABLE
// ================================================================================
*/
	function fn_CHECK_USERPARAM_VALUE_IFEXISTING(value,res){

		var lv_found = false;
		
		fn_ajax_call(
			"/admin/users/management_v2/check_data_ifexisting/"+value+"/glbcuserparam",
			"GET",
			{},
			function(result){

				if(result.dataitem.length>0){
					lv_found = true;
				}
			
				return res(lv_found);
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
				return res(lv_found);
			}
		);

	}

/*
// ================================================================================
// Function to CHECK UP_MOBID_U IS ALREADY EXISTING IN glbcusernr
// ================================================================================
*/
	function fn_CHECK_MOBID_EXISTING_GLBCUSERNR(value,res){

		var lv_found = false;
		
		fn_ajax_call(
			"/admin/users/management_v2/check_data_ifexisting/"+value+"/glbcusernr",
			"GET",
			{},
			function(result){

				if(result.dataitem.length>0){
					lv_found = true;
				}
				
				return res(lv_found);
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
				return res(lv_found);
			}
		);

	}

/*
// ================================================================================
// Function to UPDATE ROLE OBJV
// ================================================================================
*/	
	function fn_UPDATE_ROLEOBJV(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		//get user role obj
		var lt_roleobjv_insert = [];
		var lt_roleobjv_update = [];
		
		//20180522 - John suggested to seperate the INSERT, UPDATE and DELETED items
		for(var i=0; i < gt_ROLEOBJV.length; i++){
			
			var lv_id = gt_ROLEOBJV[i].id;
			var lv_status = "";
			
			if(gt_ROLEOBJV[i].radio_indx == false){
				lv_status = "02";
			}else{
				lv_status = "01";
			}
			
			if( lv_id != ""){
				
				if(
					gt_ROLEOBJV_INDEX[lv_id].role 		!== gt_ROLEOBJV[i].role ||
					gt_ROLEOBJV_INDEX[lv_id].object 	!== gt_ROLEOBJV[i].object ||
					gt_ROLEOBJV_INDEX[lv_id].fieldname 	!== gt_ROLEOBJV[i].fieldname ||
					gt_ROLEOBJV_INDEX[lv_id].value 		!== gt_ROLEOBJV[i].value ||
					gt_ROLEOBJV_INDEX[lv_id].status 	!== lv_status
				
				){
					
					lt_roleobjv_update.push({
						ID			: gt_ROLEOBJV[i].id,
						ROLE		: gt_ROLEOBJV[i].role,
						COUNTER		: "1",
						OBJECT		: gt_ROLEOBJV[i].object,
						FIELDNAME	: gt_ROLEOBJV[i].fieldname,
						VALUE		: gt_ROLEOBJV[i].value,
						STATUS		: lv_status,
					});
					
				}
				
			}else{
				
				lt_roleobjv_insert.push({
					ID			: gt_ROLEOBJV[i].id,
					ROLE		: gt_ROLEOBJV[i].role,
					COUNTER		: "1",
					OBJECT		: gt_ROLEOBJV[i].object,
					FIELDNAME	: gt_ROLEOBJV[i].fieldname,
					VALUE		: gt_ROLEOBJV[i].value,
					STATUS		: lv_status,
				});
				
			}
			
		}
 
		var lt_array = {
			APP_ID			: SelectedAppID,
			inserted_items 	: lt_roleobjv_insert,
			updated_items 	: lt_roleobjv_update,
			deleted_items 	: gt_roleobjv_deleted,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/update_user_role_objv",
			"POST",
			lt_array,
			function(result){
				if(result.status == "01"){
					gt_roleobjv_deleted = [];
					fn_GET_USER_GLBMROLEOBJV(gv_roleobjv_role);
					fn_SET_DISPLAY_MODE("ROLEOBJV_READ");
					fn_show_notification_message(gt_Global_Message.T07);	// 2016.01.13 - Nahor removed the set time in this notification
				}
				else if(result.status == "02"){
					fn_show_notification_message(result.message);
					busy_diag.close();
				}
				else {
					fn_show_notification_message(gt_Global_Message.T18);
				}
				busy_diag.close();
		 
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	
	}

	
/*
// ================================================================================
// Function to UPDATE USER ROLE
// ================================================================================
*/	
	function fn_UPDATE_GLBMROLE(){
		
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lv_role 	 = ui("ROLE_MNT_LEFT_ROLEID_INP").getValue().trim();
		var lv_role_desc = ui("ROLE_MNT_LEFT_ROLEDESC_INP").getValue().trim();
		
		var lt_role = {
			ROLE       	: lv_role,
			DESCRIPTION	: lv_role_desc
		}
		
		var record = {
			APP_ID		: SelectedAppID,
			items 		: lt_role,
		}

		fn_ajax_call(
			"/admin/users/management_v2/update_user_glbmrole",
			"POST",
			record,
			function(result){
			
				ui("ROLE_MNT_LEFT_ROLEID_INP").setValue("");
				ui("ROLE_MNT_LEFT_ROLEDESC_INP").setValue("");

				if(result.status === "01"){

					fn_GET_GLBMROLE_REBIND(lv_role,lv_role_desc);
					fn_show_notification_message(gt_Global_Message.T17);
					busy_diag.close();
					
				}else if(result.status === "02"){
					fn_show_notification_message(gt_Global_Message.T18);
					busy_diag.close();
				}else{
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
	
	}

	function fn_CHECK_USER_ROLEOBJV_TABLE_DATA(){
		
		var lv_error = false;
		
		var table_items = ui("ROLEOBJV_MNT_TABLE").getModel();
		
		for(var i=0; i<table_items.length; i++){
		
			var lv_Object 		= table_items[i].getCells()[1].getValue();
			var lv_Fieldname 	= table_items[i].getCells()[2].getValue();
			var lv_Value 		= table_items[i].getCells()[3].getValue();
			
			console.log(lv_Object);
			table_items[i].getCells()[1].setValueState("None");
			table_items[i].getCells()[1].setValueStateText("");
			table_items[i].getCells()[2].setValueState("None");
			table_items[i].getCells()[2].setValueStateText("");
			table_items[i].getCells()[3].setValueState("None");
			table_items[i].getCells()[3].setValueStateText("");
		
			if(lv_Object.trim()==""){
				
				lv_error = true;
				
				table_items[i].getCells()[1].setValueState("Error");
				table_items[i].getCells()[1].setValueStateText("Field is required");
			}
			
			if(lv_Fieldname.trim()==""){
				
				lv_error = true;
				table_items[i].getCells()[2].setValueState("Error");
				table_items[i].getCells()[2].setValueStateText("Field is required");
				
			}
			
			if(lv_Value.trim()==""){
				
				lv_error = true;
				
				table_items[i].getCells()[3].setValueState("Error");
				table_items[i].getCells()[3].setValueStateText("Field is required");
				
			}
			
			if(fn_check_if_exists(lv_Object,gt_glbmobject_valuehelp)==false){
				lv_error = true;
				
				table_items[i].getCells()[1].setValueState("Error");
				table_items[i].getCells()[1].setValueStateText("Field is required");
				
			}
			
			if(fn_check_if_exists(lv_Fieldname,gt_glbmfieldname_valuehelp)==false){
				lv_error = true;
				table_items[i].getCells()[2].setValueState("Error");
				table_items[i].getCells()[2].setValueStateText("Field is required");
				
			}
		}
		

		return lv_error;
	}

/*
// ================================================================================
// Function to CREATE ROLE
// ================================================================================
*/	
	function fn_CREATE_ROLE(){
		
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lt_role = [];
		
		gv_selected_role = ui("ROLE_MNT_LEFT_ROLEID_INP").getValue().toUpperCase().trim(); //changed 11052015
		gv_selected_role_desc = ui("ROLE_MNT_LEFT_ROLEDESC_INP").getValue().trim();


		gv_roleobjv_role = gv_selected_role;
	
		lt_role = {
			role			: ui("ROLE_MNT_LEFT_ROLEID_INP").getValue().toUpperCase(),
			description		: ui("ROLE_MNT_LEFT_ROLEDESC_INP").getValue(),
		}
		
		var record = {
			items:lt_role
		}
		
		fn_ajax_call(
			"/admin/users/management/create_user_glbmrole",
			"POST",
			record,
			function(result){
			
				ui("ROLE_MNT_LEFT_ROLEID_INP").setValue("");
				ui("ROLE_MNT_LEFT_ROLEDESC_INP").setValue("");

				if(result.status === "01"){

					fn_GET_GLBMROLE_REBIND(gv_selected_role,gv_selected_role_desc);
					fn_show_notification_message(result.message);
					busy_diag.close();
					
				}else if(result.status === "02"){

					fn_show_notification_message(result.message);
					busy_diag.close();
					
				}else{
					busy_diag.close();
				}
					
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
		
	
	}	
	
	function CHECK_ROLEID_IFEXSTING(roleID,res){
	
		var lv_found = false;
		
		fn_ajax_call(
			"/admin/users/management_v2/check_data_ifexisting/"+roleID+"/glbmrole",
			"GET",
			{},
			function(result){
	
				if(result.dataitem.length>0){
					lv_found = true;
				}
			
				return res(lv_found);
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
				return res(lv_found);
			}
		);
		
	}
	
	function CHECK_USERID_IFEXSTING(userID,res){
		
		var lv_found = false;
		
		fn_ajax_call(
			"/admin/users/management_v2/check_data_ifexisting/"+userID+"/glbmuser",
			"GET",
			{},
			function(result){
		
				if(result.dataitem.length>0){
					lv_found = true;
				}
				
				return res(lv_found);
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
				return res(lv_found);
			}
		);
		
	}
	
	function CHECK_GET_EMPLOYEEIFEXISTNG(empID,res){
		
		var lv_found = false;
		
		fn_ajax_call(
			"/admin/users/management_v2/check_data_ifexisting/"+empID+"/glbmemp",
			"GET",
			{},
			function(result){
		 
				if(result.return.status == "01") {
					
					lv_found = result.dataitem[0];
					console.log(lv_found)

				}
				
				return res(lv_found);
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
				return res(lv_found);
			}
		);
		
	}

	
/*
// ================================================================================
// Function to GET USER DETAIL
// ================================================================================
*/		
	//query for user user (user)
	function fn_GET_USER(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_USER_DATA	   	= [];
		gt_user_data_bk 	= [];
		gt_USER_DATA_INDEX 	= [];
        
		fn_ajax_call(
			"/admin/users/management_v2/get_user",
			"GET",
			{},
			function(result){
			
				for(i=0; i<result.dataitem.length; i++){
					
					gt_USER_DATA.push({
						id					: result.dataitem[i].id,
						user_id				: result.dataitem[i].username,
						display_name    	: result.dataitem[i].name,
						firstname			: result.dataitem[i].firstname,
						lastname        	: result.dataitem[i].lastname,
						email               : result.dataitem[i].email,
						status				: result.dataitem[i].STATUS,
						status_desc			: result.dataitem[i].status_desc,
						del_flag        	: result.dataitem[i].del_flag,
						validity_period 	: result.dataitem[i].validity_period,
						created_by      	: result.dataitem[i].created_by,
						created_at 			: result.dataitem[i].created_at,
						creation_date		: result.dataitem[i].creation_date,
						creation_time		: result.dataitem[i].creation_time,
						creation_date_dsp	: fn_format_datetime( result.dataitem[i].creation_date,"DD MMM YYYY"),
						creation_time_dsp   : timeFormat_12H.format(  timeFormat_12H.parse( result.dataitem[i].creation_time )),
						
					});
					
					gt_USER_DATA_INDEX[result.dataitem[i].username] = {
						id					: result.dataitem[i].id,
						user_id				: result.dataitem[i].username,
						display_name    	: result.dataitem[i].name,
						firstname			: result.dataitem[i].firstname,
						lastname        	: result.dataitem[i].lastname,
						email               : result.dataitem[i].email,
						status				: result.dataitem[i].STATUS,
						status_desc			: result.dataitem[i].status_desc,
						del_flag        	: result.dataitem[i].del_flag,
						validity_period 	: result.dataitem[i].validity_period,
						created_by      	: result.dataitem[i].created_by,
						created_at 			: result.dataitem[i].created_at,
						creation_date		: result.dataitem[i].creation_date,
						creation_time		: result.dataitem[i].creation_time,
						creation_date_dsp	: fn_format_datetime( result.dataitem[i].creation_date,"DD MMM YYYY"),
						creation_time_dsp   : timeFormat_12H.format(  timeFormat_12H.parse( result.dataitem[i].creation_time )),
					}
				}
				
				gt_user_data_bk = JSON.stringify(gt_USER_DATA);
				gt_user_data_bk = JSON.parse(gt_user_data_bk);
				
				busy_diag.close();
				fn_BIND_ACCT_MAINTENANCE();
				
				if(ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle()!=""){
				  fn_BIND_ACCT_DETAIL_DISPLAY(ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle())
				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}

	//Bind Account Maintenance Table
	function fn_BIND_ACCT_MAINTENANCE(){
		setTimeout(function(){
			fn_freeze_table_header();
		}, 500);

		
		var lv_switch_state = ui('SWITCH_INCLUDE_DELETED_USER').getState();
		
		if(lv_switch_state){
			
			gt_USER_DATA = JSON.stringify(gt_user_data_bk);
			gt_USER_DATA = JSON.parse(gt_USER_DATA);
			
		}else{
			
			var i = gt_USER_DATA.length;
			while (i > 0) {
				
				i--;
				if(gt_USER_DATA[i].del_flag === "X"){
					
					gt_USER_DATA.splice(i,1);
					
				}
			}
		}
		
		var model = new sap.ui.model.json.JSONModel();
			//model.setData(arr);
			model.setData(gt_USER_DATA);
			

		//sap.ui.getCore().byId('GO_TABLE_ACCT_MAINT').setModel(model).bindAggregation("items", {
				//path: "/",
				//template: sap.ui.getCore().byId('GO_TABLE_ACCT_MAINT_TEMP')
		//});
		ui('GO_TABLE_ACCT_MAINT').setModel(model).bindRows("/");
		ui('LABEL_ITEM_USER_MTN').setText("Items"+"  " +"("+gt_USER_DATA.length+")");
			
		
		//for Copy USER Value HELP
		var model_copy = new sap.ui.model.json.JSONModel();
			model_copy.setData(gt_user_data_bk);
			
		var template_list_valuehelp= new sap.m.StandardListItem({
			type:"Active",
			title:"{user_id}"
		});
		
		ui('GO_DIALOG_COPY_USER_VALUEHELP_USER_ID').setModel(model_copy).bindAggregation("items",{
			path:"/",
			template:template_list_valuehelp
		});
		
		ui("SEARCHFIELD_ACCT_MAINT").fireSearch();
		
	}
	
	
/*
// ================================================================================
// Function to rebind role
// ================================================================================
*/
	function fn_GET_GLBMROLE_REBIND(lv_role, lv_role_desc){ //changed 11052015
		
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_ROLE		  = [];
		
		fn_ajax_call(
			"/admin/users/management/get_glbmrole",
			"GET",
			{},
			function(result, status){
				
				for(i=0; i<result.dataitem.length; i++){

					gt_ROLE.push({
						role			: result.dataitem[i].ROLE,
						role_desc		: result.dataitem[i].DESCRIPTION,
					});
					
				}
				
				fn_BIND_PAGE_LEFT_ROLE_MNT_LIST(gt_ROLE);
				busy_diag.close(); 
				
				//DETERIME IF THE FUNCTION HAS PARAMETER   				//changed 11052015
				lv_role = (lv_role || "") ? lv_role : gt_ROLE[0].role;
				lv_role_desc = (lv_role_desc || "") ? lv_role_desc : gt_ROLE[0].role_desc;
				
				gv_roleobjv_role = lv_role;
				
				//AFTER REBINDING, RESET THE SCREEN
				fn_GET_USER_GLBMROLEOBJV(lv_role); 
				ui("BAR_ROLE_MTN_LABEL_HEADER").setTitle(lv_role+"( "+lv_role_desc+" )");

			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
		
	}
	
	function fn_BIND_PAGE_LEFT_ROLE_MNT_LIST(data){
		
		ui('ROLE_MNT_LEFT_LIST').destroyItems(); //changed 10282015

		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);

		ui('ROLE_MNT_LEFT_LIST').setModel(model).bindAggregation("items", {
			path: "/",
			template: ui('ROLE_MNT_LEFT_LIST_TEMP')
		});

		ui('ROLE_MNT_LEFT_SEARCH').fireLiveChange();
		
	}
	

/*
// ================================================================================
// Bind USER DETAILS
// ================================================================================
*/	
	function fn_BIND_USER_PARAM(data,type){
		
		var model = new sap.ui.model.json.JSONModel();
			model.setData(data);
		
		//ui('ACCT_MNT_DSP_TABLE_ACCTPREF').destroyItems();
		
		//var lo_table = $('#ACCT_MNT_DSP_TABLE_ACCTPREF-listUl');
			//lo_table.floatThead('destroy');	
		
		if(type!="INPUT"){
			
			ui('ACCT_MNT_DSP_TABLE_ACCTPREF').setModel(model).bindRows('/');
				//path: "/",
				//template: ui('ACCT_MNT_DSP_TABLE_TEMPLATE_ACCTPREF_TXT')
			//});
			ui('ACC_USER_ID').setTemplate(new sap.m.Text({text: "{user_id}", textAlign:"Left"}),);
			ui('ACC_PARAMETER').setTemplate(new sap.m.Text({text : "{param_id}", textAlign:"Right"}),);
			ui('ACC_PARAMETER_DESC').setTemplate(new sap.m.Text({text : "{param_desc}", textAlign:"Right"}),);
			ui('ACC_PARAMETER_VALUE').setTemplate(new sap.m.Text({text : "{param_value}", textAlign:"Right"}),);
			ui('ACC_DELETE').setVisible(false);
				
		}else{
			
			ui('ACCT_MNT_DSP_TABLE_ACCTPREF').setModel(model).bindRows('/');
			ui('ACC_USER_ID').setTemplate(new sap.m.Input({value:"{user_id}", textAlign:"Left"}),);
			ui('ACC_PARAMETER').setTemplate(new sap.m.Text({text:"{param_id}", textAlign:"Left"}),);
			ui('ACC_PARAMETER_DESC').setTemplate(new sap.m.Text({text:"{param_desc}", textAlign:"Left"}),);
			ui('ACC_PARAMETER_VALUE').setTemplate(
				new sap.m.Input({
					tooltip	:"{param_value}",
					value	:"{param_value}",
					editable: true , 
					change	: function(evt){
					
						//ADDED CHECKING for VALUE OF USER PARAM
						var lv_old_val = evt.getSource().getTooltip();
						
						var lv_param_id = evt.getSource().getParent().getCells()[1].getText();
						var lv_control = evt.getSource();
						
						if(lv_param_id == 'UP_MOBID_U'){
							
							var lv_value = evt.getSource().getValue().trim();
							var lv_value_length = lv_value.length;
							var lv_operation = "";
							var lv_new_value = "";
							var lv_zero = "";
							var lv_zeros_to_add = 3 - lv_value_length;
							
							
							if(lv_value !== ""){

								if(lv_value_length === 3){
									
									evt.getSource().setValueState("None");
									lv_operation = "exact";
								}
								else if(lv_value_length < 3){
									
									evt.getSource().setValueState("None");
									lv_operation = "add";
									
								}else if(lv_value_length > 3){
									
									evt.getSource().setValueState("Error");
									fn_show_notification_message("Maximum of 3 Characters");	// 2016.01.13 - Nahor removed the set time in this notification
								}
								if(lv_operation === "add"){
									for(var y =0; y < lv_zeros_to_add; y++){
										lv_zero += "0";
										lv_new_value = lv_zero + lv_value;
									}
								}else if(lv_operation === "exact"){
									lv_new_value = lv_value;
								}else{
									lv_new_value= lv_value;
									evt.getSource().setValueState("Error");
								}
								
								lv_control.setValue(lv_new_value);
								
								fn_CHECK_USERPARAM_VALUE_IFEXISTING(lv_new_value,function(res){

									if(res!=false){

										if(lv_old_val == lv_new_value){

											lv_control.setValueState("None");

										}else{

											lv_control.setValueState("Error");
											fn_show_notification_message("Mobile ID is already in used");		// 2016.01.13 - Nahor removed the set time in this notification
										}	

									}else{

										fn_CHECK_MOBID_EXISTING_GLBCUSERNR(lv_new_value, function(res){

											if(res!=false){

												lv_control.setValueState("Error");
												fn_show_notification_message("Mobile ID is already existing");	// 2016.01.13 - Nahor removed the set time in this notification

											}
										});
									}
								});
								
							}else{

								evt.getSource().setValueState("Error");
							}
						}

					}	
				}),
			
			);
			ui('ACC_DELETE').setVisible(true);		
		}
	}
	
	//binding for user role
	function fn_BIND_USER_ROLE(data,type){
		
		var model = new sap.ui.model.json.JSONModel();
			model.setData(data);
			
		//ui('ACCT_MNT_DSP_TABLE_ACCTAUTH').destroyItems();
		
		//var lo_table = $('#ACCT_MNT_DSP_TABLE_ACCTAUTH-listUl');
			//lo_table.floatThead('destroy');	
		
		if(type!="INPUT"){
			
			ui('ACCT_MNT_DSP_TABLE_ACCTAUTH').setModel(model).bindRows('/')
			ui('AUTH_ID').setTemplate(new sap.m.Text({text: "{id}", textAlign:"Left"}),);	
			ui('AUTH_ROLE').setTemplate(new sap.m.Text({text : "{role}", textAlign:"Right"}),);	
			ui('AUTH_ROLE_DESC').setTemplate(new sap.m.Text({text : "{role_desc}", textAlign:"Right"}),);	
			ui('AUTH_VAL_FROM').setTemplate(new sap.m.Text({text : "{valid_fr_dsp}", textAlign:"Right"}),);	
			ui('AUTH_VAL_TO').setTemplate(new sap.m.Text({text : "{valid_to_dsp}", textAlign:"Right"}),);	
			ui('AUTH_STATUS').setTemplate(new sap.m.ObjectStatus({icon : "{icon}", state:"{state}"}),);	
			ui('AUTH_DELETE').setVisible(false);	
						
		}else{
			
			ui('ACCT_MNT_DSP_TABLE_ACCTAUTH').setModel(model).bindRows('/');
			ui('AUTH_ID').setTemplate(new sap.m.Text({text: "{id}", textAlign:"Left"}),);	
			ui('AUTH_ROLE').setTemplate(
				new sap.m.Input({
					value:"{role}", 
					enabled: true,
					showValueHelp:true,
					valueHelpRequest: function(oEvt){
						
						gv_confirm_input = oEvt.getSource().getId();
						
						if(gt_glbmrole_valuehelp.length > 0){
							fn_bind_role_valuehelp(gt_glbmrole_valuehelp,true);
						}else{
							fn_get_role_valuehelp(true);
						}
						
					},
					change:function(oEvt){ 
						
						var lv_value = oEvt.getSource().getValue().trim();
						var lv_desc = fn_get_desc(lv_value, gt_glbmrole_valuehelp);
						
						var lv_control = oEvt.getSource().getParent().getCells()[1];
						
						oEvt.getSource().setValue(lv_value.toUpperCase());
						
						if(lv_value !== ""){
							
							if(fn_check_if_exists(lv_value,gt_glbmrole_valuehelp) != false){
								
								var lv_count = 0;
								for(var i =0; i < gt_user_role.length; i++){
									if(gt_user_role[i].role.toUpperCase() === lv_value.toUpperCase()){
										lv_count++;
									}
								}
								
								if(lv_count > 1){
									oEvt.getSource().setValueState("Error");
									oEvt.getSource().setValueStateText("Invalid");
									lv_control.setText("");
									fn_show_notification_message(lv_value + " role is already exists.");
								}else{
									oEvt.getSource().setValueState("None");
									oEvt.getSource().setValueStateText("");
									lv_control.setText(lv_desc);
								}
							}else{
								oEvt.getSource().setValueState("Error");
								oEvt.getSource().setValueStateText("Invalid");
								lv_control.setText("");
							}
							
						}else{
							oEvt.getSource().setValueState("None");
							oEvt.getSource().setValueStateText("");
							lv_control.setText("");
						}
					}
				}),
			);	
			ui('AUTH_ROLE_DESC').setTemplate(new sap.m.Text({text :"{role_desc}", textAlign:"Left"}),);	
			ui('AUTH_VAL_FROM').setTemplate(
				new sap.m.DatePicker({
					value			: "{valid_fr}",
					editable		: true , 
					type			: sap.m.InputType.Date,
					displayFormat	: "dd MMM yyyy", 
					valueFormat		: "yyyy-MM-dd",
					change			:  function(evt){}	
				}),
			);	
			ui('AUTH_VAL_TO').setTemplate(
				new sap.m.DatePicker({
					value			: "{valid_to}",
					editable		: true , 
					type			: sap.m.InputType.Date,
					displayFormat	: "dd MMM yyyy", 
					valueFormat		: "yyyy-MM-dd",
					change			:  function(evt){}	
				}),
			);	
			ui('AUTH_STATUS').setTemplate(new sap.m.Switch({ state: "{radio_indx}", type: sap.m.SwitchType.AcceptReject,enabled:true}),);	
			ui('AUTH_DELETE').setVisible(true);		
		}
	}
	
	function fn_bind_country_code(data){
		
		ui('ACCT_MNT_DSP_SELECT_MOBILE_NO').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
			
		var lv_list_mobile_temp = new sap.ui.core.ListItem({
			key:"{ICC_CODE}",
			text:"{COUNTRY_DSP}",
		});
			
		ui('ACCT_MNT_DSP_SELECT_MOBILE_NO').setModel(model).bindAggregation("items", {
			path: "/", 
			template: lv_list_mobile_temp,
		});
		
	}

	function fn_bind_biz_partner(data,mode){

		if(mode == "TEXT"){
			var lv_mode = false;
			var lv_visible_dsp = true;
			var lv_visible_edt = false;
		}
		else if(mode == "INPUT"){
			var lv_mode = true;
			var lv_visible_dsp = false;
			var lv_visible_edt = true;
		}

		ui("CREATE_PARTNER_ADD_BUTTON").setVisible(lv_mode);
		ui("GO_TABLE_CREATE_PARTNER_DELCOL").setVisible(lv_mode);

		for(var i=0;i<data.length;i++){

			switch(data[i].STATUS){

				case "01":
					data[i].STATUS_STATE = true;
				break;
				case "02":
					data[i].STATUS_STATE = false;
				break;
				default:
					data[i].STATUS_STATE = false;
			}

			data[i].PARTNER_TYPE_ENABLED = lv_mode;
			data[i].PARTNER_NO_EDITABLE = lv_mode;
			data[i].STATUS_ENABLED = lv_mode;
			data[i].BP_TYPE_DSP_VISIBLE = lv_visible_dsp;
			data[i].BP_TYPE_EDT_VISIBLE = lv_visible_edt;
		}

		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);

		ui("GO_TABLE_CREATE_PARTNER").setModel(lo_model).bindRows('/');
		ui("LABEL_PARTNER").setText("Partner ("+data.length+")");
		fn_clear_table_sorter("GO_TABLE_CREATE_PARTNER");
	}

	function fn_bind_partner_type(data){

		ui("GO_CREATE_PARTNER_TYPE_SELECT").destroyItems();

		for(var i=0;i<data.length;i++){

			var lv_partner_type = new sap.ui.core.Item({
				key : data[i].ID,
				text : data[i].description
			});

			ui("GO_CREATE_PARTNER_TYPE_SELECT").addItem(lv_partner_type);
		}
	}
	
/*
// ================================================================================
// Function to check if role is existing in glbcuserrole
// ================================================================================
*/
	function fn_CHECK_GLBCUSERROLE_ISEXISITING(lv_role){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		fn_ajax_call(
			"/admin/users/management_v2/check_data_ifexisting/"+lv_role+"/glbcuserrole",
			"GET",
			{},
			function(result){
			
				if(result.return.status == "01"){

					go_Dialog_Cannot_Delete.open();

				}else if(result.return.status == "02"){

					go_Dialog_Confirm_Delete.open();
				
				}else{

				}

				busy_diag.close();
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
		
	}

/*
// ================================================================================
// Function to delete role
// ================================================================================
*/	
	function fn_DELETE_ROLE(lv_role){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			//busy_diag.open();
		
		var record = {
			APP_ID		: SelectedAppID,
			items 		: lv_role,
		}
		
		console.log(gt_ROLE);
		
		fn_ajax_call(
			"/admin/users/management_v2/delete_user_glbmrole",
			"POST",
			record,
			function(result){
		
				ui("ROLE_MNT_LEFT_ROLEID_INP").setValue("");
				ui("ROLE_MNT_LEFT_ROLEDESC_INP").setValue("");

				if(result.status == "01"){

					fn_GET_GLBMROLE_REBIND();
					fn_show_notification_message(gt_Global_Message.T19);	// 2016.01.13 - Nahor removed the set time in this notification


				}else if(result.status == "02"){

					fn_GET_GLBMROLE_REBIND();
					fn_show_notification_message(gt_Global_Message.T20);	// 2016.01.13 - Nahor removed the set time in this notification
				
				}else{
					
				}
				
				busy_diag.close();
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}
	
/*
// ================================================================================
// Function to GET GLBMROLEOBJV
// ================================================================================
*/	
	function fn_GET_USER_GLBMROLEOBJV(role){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_ROLEOBJV		  = [];
		gt_ROLEOBJV_INDEX = [];
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbcroleobjv/"+role,
			"GET",
			{},
			function(result){
			
				for(i=0; i<result.dataitem.length; i++){
					
					var lv_state = true;
					
					if(result.dataitem[i].STATUS !== "01"){
						lv_state = false;
					}else{
						lv_state = true;
					}
					
					gt_ROLEOBJV.push({
						id				: result.dataitem[i].ID,
						role			: result.dataitem[i].ROLE,
						object			: result.dataitem[i].OBJECT,
						fieldname		: result.dataitem[i].FIELDNAME,
						value			: result.dataitem[i].VALUE,
						status			: result.dataitem[i].STATUS,
						radio_indx		: lv_state,
						state			: (result.dataitem[i].STATUS == "01") ? "Success" : "Error",
						icon			: (result.dataitem[i].STATUS == "01") ? "sap-icon://accept" : "sap-icon://warning",
					});
					
					gt_ROLEOBJV_INDEX[result.dataitem[i].ID] = {
						id				: result.dataitem[i].ID,
						role			: result.dataitem[i].ROLE,
						object			: result.dataitem[i].OBJECT,
						fieldname		: result.dataitem[i].FIELDNAME,
						value			: result.dataitem[i].VALUE,
						status			: result.dataitem[i].STATUS,
						radio_indx		: lv_state,
						state			: (result.dataitem[i].STATUS == "01") ? "Success" : "Error",
						icon			: (result.dataitem[i].STATUS == "01") ? "sap-icon://accept" : "sap-icon://warning",
					
					}
					
				}

				busy_diag.close();
				console.log("UP");
				
				
				//DETERMINE IF THERE ARE OBJECTS UNDER ROLE
				if(gt_ROLEOBJV.length === 0){
					ui("DELETE_ROLE_BUTTON").setEnabled(true);
				}else{
					ui("DELETE_ROLE_BUTTON").setEnabled(false);
				}

				fn_BIND_ROLEOBJV(gt_ROLEOBJV,"TEXT");
			
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
		
	}
	
	function fn_BIND_ROLEOBJV(data,type){
		
		//ui('ROLEOBJV_MNT_TABLE').destroyItems();
		ui("ROLEOBJV_MNT_RIGHT_LABEL").setText("Items (" +data.length +")");
		
		var model = new sap.ui.model.json.JSONModel();
			model.setData(data);
		
		//var lo_table = $('#ROLEOBJV_MNT_TABLE-listUl');
			//lo_table.floatThead('destroy');	
		
		if(type!="INPUT"){
			
			ui('ROLEOBJV_MNT_TABLE').setModel(model).bindRows('/');
			ui('ROLE_ID').setTemplate(new sap.m.Text({text : "{id}", textAlign:"Right"}),);
			ui('ROLE_OBJECT').setTemplate(new sap.m.Text({text : "{object}", textAlign:"Right"}),);
			ui('ROLE_FIELDNAME').setTemplate(new sap.m.Text({text : "{fieldname}", textAlign:"Right"}),);
			ui('ROLE_VALUE').setTemplate(new sap.m.Text({text : "{value}", textAlign:"Right"}),);
			ui('ROLE_STATUS').setTemplate(new sap.m.ObjectStatus({icon : "{icon}", state:"{state}"}),);
			ui('ROLE_DELETE').setVisible(false);
			
			//new sap.m.Text({text: "{id}", textAlign:"Left"}),
			//new sap.m.Text({text : "{object}", textAlign:"Right"}),
			//new sap.m.Text({text : "{fieldname}", textAlign:"Right"}),
			//new sap.m.Text({text : "{value}", textAlign:"Right"}),
			//new sap.m.ObjectStatus({icon : "{icon}", state:"{state}"}),
				//path: "/",
				//template: ui('ROLEOBJV_MNT_TABLE_TEMPLATE_TXT')
			//});
			
		}else{
			
			ui('ROLEOBJV_MNT_TABLE').setModel(model).bindRows('/');
			ui('ROLE_DELETE').setVisible(true);
			ui('ROLE_ID').setTemplate(new sap.m.Input({value:"{id}", textAlign:"Left"}),);
			ui('ROLE_OBJECT').setTemplate(
				new sap.m.Input({
					value:"{object}", 
					enabled: true,
					showValueHelp:true,
					valueHelpRequest: function(oEvt){
						
						gv_confirm_input = oEvt.getSource().getId();
						
						if(gt_glbmobject_valuehelp.length > 0){
							fn_bind_object_valuehelp(gt_glbmobject_valuehelp,true);
						}else{
							fn_get_object_valuehelp(true);
						}
						
					},
					change:function(oEvt){ 
					
						var lv_value = oEvt.getSource().getValue().trim();
						oEvt.getSource().setValue(lv_value.toUpperCase());
						
						if(lv_value !== ""){
							
							if(fn_check_if_exists(lv_value,gt_glbmobject_valuehelp) != false){
								oEvt.getSource().setValueState("None");
								oEvt.getSource().setValueStateText("");
							}else{
								oEvt.getSource().setValueState("Error");
								oEvt.getSource().setValueStateText("Invalid");
							}
							
						}else{
							oEvt.getSource().setValueState("None");
							oEvt.getSource().setValueStateText("");
						}
					}
				}),
			);
			
			ui('ROLE_FIELDNAME').setTemplate(
				new sap.m.Input({
					value:"{fieldname}", 
					enabled: true,
					showValueHelp:true,
					valueHelpRequest: function(oEvt){
						
						gv_confirm_input = oEvt.getSource().getId();
						
						if(gt_glbmfieldname_valuehelp.length > 0){
							fn_bind_fieldname_valuehelp(gt_glbmfieldname_valuehelp,true);
						}else{
							fn_get_fieldname_valuehelp(true);
						}
						
					},
					change:function(oEvt){ 
					
						var lv_value = oEvt.getSource().getValue().trim();
						oEvt.getSource().setValue(lv_value.toUpperCase());
						
						if(lv_value !== ""){
							
							if(fn_check_if_exists(lv_value,gt_glbmfieldname_valuehelp) != false){
								oEvt.getSource().setValueState("None");
								oEvt.getSource().setValueStateText("");
							}else{
								oEvt.getSource().setValueState("Error");
								oEvt.getSource().setValueStateText("Invalid");
							}
							
						}else{
							oEvt.getSource().setValueState("None");
							oEvt.getSource().setValueStateText("");
						}
						
					}
				}),
			);
			ui('ROLE_VALUE').setTemplate(
				new sap.m.Input({
					value:"{value}", 
					textAlign:"Left",
					change:function(evt){ 
						if(evt.getSource().getValue().trim()==""){
							evt.getSource().setValueState("Error");
							evt.getSource().setValueStateText("Field is required");
						}else{
							evt.getSource().setValueState("None");
							evt.getSource().setValueStateText("");
						}
					}
				}),
			);
			ui('ROLE_STATUS').setTemplate(new sap.m.Switch({ state:"{radio_indx}", type: sap.m.SwitchType.AcceptReject,enabled:true}),);	
		}
		
		setTimeout(function(){fn_freeze_table_header();}, 500);
	}

/*
// ================================================================================
// Function to Get Client Administration
// ================================================================================
*/	
	function fn_GET_CLIENT_ADMINISTRATION(){
	
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
		busy_diag.open();
		
		gt_CLNT_CONF	   = [];
		gt_CLNT_CONF_INDEX = [];
		gt_CLNT_CONF_LEFT  = [];
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbcappconf",
			"GET",
			{},
			function(result){
				
				console.log(result);
				
				if (result.return.status=="01") {
					
					for(var i=0; i<result.dataitem.length; i++){
						
						gt_CLNT_CONF.push({
							TSTC 			: result.dataitem[i].TSTC,
							FUNCTION 		: result.dataitem[i].FUNCTION,
							PARAM_ID 		: result.dataitem[i].PARAM_ID,
							VALUE 			: result.dataitem[i].VALUE,
							FUNCTION_DESC	: result.dataitem[i].FUNCTION_DESC,
							PARAM_DESC		: result.dataitem[i].PARAM_DESC, 
							TITLE			: result.dataitem[i].TITLE,
						});
						
						gt_CLNT_CONF_INDEX[result.dataitem[i].TITLE] = {
							TSTC 			: result.dataitem[i].TSTC,
							FUNCTION 		: result.dataitem[i].FUNCTION,
							PARAM_ID 		: result.dataitem[i].PARAM_ID,
							VALUE 			: result.dataitem[i].VALUE,
							FUNCTION_DESC	: result.dataitem[i].FUNCTION_DESC,
							PARAM_DESC		: result.dataitem[i].PARAM_DESC, 
							TITLE			: result.dataitem[i].TITLE,
						};
						
					}
					
					for(var x=0; x<result.glbmtstc.length; x++){
						gt_CLNT_CONF_LEFT.push({
							TSTC 	: result.glbmtstc[x].TSTC,
							TITLE	: result.glbmtstc[x].TITLE,
						});
					}
					
					fn_BIND_CLIENT_ADMIN_LEFT(gt_CLNT_CONF_LEFT);
					busy_diag.close();
					
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
	}

	function fn_get_client_admin_valuehelp(lv_tstc){
	
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		gt_function_valuehelp = [];
		gt_parameter_valuehelp = [];
		
		var lt_array = {
			TSTC	: lv_tstc
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/get_client_admin_valuehelp",
			"GET",
			lt_array,
			function(response){
				
				if(response.return.status == "01"){
					
					gt_function_valuehelp 	= response.function;
					gt_parameter_valuehelp 	= response.parameter;
					
					busy_diag.close();
					
				}else{
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
	}
	
	function fn_bind_client_admin_valuehelp(data,lv_title){
		
		ui('SELECT_DIALOG_ITEMS').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_template_list_valuehelp = new sap.m.StandardListItem({
			type:"Active",
			title:"{ID}",
			description:"{description}"
		
		});
		
		ui('SELECT_DIALOG_ITEMS').setModel(model).bindAggregation("items", {
			path: "/",
			template: lo_template_list_valuehelp
		});
		
		ui('SELECT_DIALOG_ITEMS').setTitle(lv_title);
		ui("SELECT_DIALOG_ITEMS").open();
		
	}
/*
// ================================================================================
// Function to Update Client Administration Value
// ================================================================================
*/	
	function fn_UPDATE_CLIENT_ADMINISTRATION(data){


		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		var lt_glbmparamtxt = [];
		for(var i=0; i<data.length; i++){
			
			for(var x=0; x < gt_CLNT_CONF_RIGHT.length; x++){
				
				if(data[i].PARAM_ID === gt_CLNT_CONF_RIGHT[x].PARAM_ID){
					
					lt_glbmparamtxt.push({
						PARAM_TYPE 		: "A",
						PARAM_ID 		: gt_CLNT_CONF_RIGHT[x].PARAM_ID,
						PARAM_DESC 		: gt_CLNT_CONF_RIGHT[x].PARAM_DESC,
					});
					break;
				}
			}
		}
		
		var record = {
			items : {
				glbcappconf : data,
				glbmparamtxt : lt_glbmparamtxt
			}
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/update_user_glbcappconf",
			"POST",
			record,
			function(result){
					
					if(result.status == "01"){

						fn_show_notification_message(gt_Global_Message.T27);
						
						fn_GET_CLIENT_ADMINISTRATION();
						fn_SET_DISPLAY_MODE("CLIENT_ADMIN_READ");
						busy_diag.close();
						
					}else if(result.status == "02") {

						fn_show_notification_message(result.message);
						busy_diag.close();
						
					}else{
						fn_show_notification_message(gt_Global_Message.T28);
						busy_diag.close();
					}

			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}

/*
// ================================================================================
// Function to Bind Client Administration left table
// ================================================================================
*/
	function fn_BIND_CLIENT_ADMIN_LEFT(data){
		ui('CLNT_CONF_LEFT_LIST').destroyItems(); 

		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
			
		ui('CLNT_CONF_LEFT_LIST').setModel(model).bindAggregation("items", {
			path: "/",
			template: ui('CLNT_CONF_LEFT_LIST_TEMP')
		});
		
		if(data.length > 0){
			if(!gv_selected_roleid){
			ui("BAR_CLNT_CONF_LABEL_HEADER").setTitle(data[0].TITLE);
				gv_selected_roleid = data[0].TSTC;
				ui("BAR_CLNT_CONF_LABEL_HEADER").setTitle(data[0].TITLE);
			}
			
			ui("CLNT_CONF_LEFT_SEARCH").fireLiveChange();
			fn_DISPLAY_CLIENT_ADMINISTRATION(gv_selected_roleid);
		}
		
	}

/*
// ================================================================================
// Function to Bind Client Administration right table
// ================================================================================
*/
	function fn_DISPLAY_CLIENT_ADMINISTRATION(role_id){
	
		gt_CLNT_CONF_RIGHT = [];
		gt_CLNT_CONF_RIGHT_bk = [];
		
		for(var i =0 ;i< gt_CLNT_CONF.length; i++ ){
			
			if(gt_CLNT_CONF[i].TSTC !== role_id){
				continue;
			}
			
			gt_CLNT_CONF_RIGHT.push({
				TSTC 			: gt_CLNT_CONF[i].TSTC,
				FUNCTION 		: gt_CLNT_CONF[i].FUNCTION,
				PARAM_ID 		: gt_CLNT_CONF[i].PARAM_ID,
				VALUE 			: gt_CLNT_CONF[i].VALUE,
				FUNCTION_DESC	: gt_CLNT_CONF[i].FUNCTION_DESC,
				PARAM_DESC		: gt_CLNT_CONF[i].PARAM_DESC, 
				TITLE			: gt_CLNT_CONF[i].TITLE,
			});
			
		}
		
		gt_CLNT_CONF_RIGHT_bk = JSON.parse(JSON.stringify(gt_CLNT_CONF_RIGHT));
		fn_BIND_CLIENT_ADMIN_RIGHT(gt_CLNT_CONF_RIGHT,READ);

	}

/*
// ================================================================================
// Function to Bind Client Administration right table
// ================================================================================
*/
	function fn_BIND_CLIENT_ADMIN_RIGHT(data , mode ){
		
		//ui('CLNT_CONF_TABLE').destroyItems(); 

		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_table = $('#CLNT_CONF_TABLE-listUl');
			lo_table.floatThead('destroy');	

		switch(mode){
			
			case READ :
				//ui('CLNT_CONF_TABLE').setModel(model).bindAggregation("items", {
					//path: "/",
					//template: ui('CLNT_CONF_TABLE_TEMPLATE_TXT')
				//});	
				ui('CLNT_CONF_TABLE').setModel(model).bindRows("/");
				ui('FUNCTION').setTemplate(new sap.m.Text({text: "{FUNCTION}", textAlign:"Left"}),);
				ui('FUNCTION_DESC').setTemplate(new sap.m.Text({text : "{FUNCTION_DESC}", textAlign:"Right"}),);
				ui('PARAM_ID').setTemplate(new sap.m.Text({text : "{PARAM_ID}", textAlign:"Right"}),);
				ui('PARAM_DESC').setTemplate(new sap.m.Text({text : "{PARAM_DESC}", textAlign:"Right"}),);
				ui('VALUE').setTemplate(new sap.m.Text({text : "{VALUE}", textAlign:"Right"}));

				break;		
			case UPDATE :
					//ui('CLNT_CONF_TABLE').setModel(model).bindAggregation("items", {
					//path: "/",
					//template: ui('CLNT_CONF_TABLE_TEMPLATE_INP')
				//});
				ui('CLNT_CONF_TABLE').setModel(model).bindRows("/");
				ui('FUNCTION').setTemplate(new sap.m.Text({text: "{FUNCTION}", textAlign:"Left"}));
				ui('FUNCTION_DESC').setTemplate(new sap.m.Text({text : "{FUNCTION_DESC}", textAlign:"Left"}));
				ui('PARAM_ID').setTemplate(new sap.m.Text({text : "{PARAM_ID}", textAlign:"Left"}));
				ui('PARAM_DESC').setTemplate(new sap.m.Text({text : "{PARAM_DESC}", textAlign:"Left"}));
				ui('VALUE').setTemplate(new sap.m.Input({value : "{VALUE}", textAlign:"Left"}));
				break;	
		}	
		ui("CLNT_CONF_TABLE_LABEL").setText("Items ("+data.length+")");
		setTimeout(function(){fn_freeze_table_header();}, 1000);
	}

	// Get User details
	function fn_GET_COPY_USER(){
		
		var username = sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_FROM').getValue();
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_COPY_USER = [];
		
		fn_ajax_call(
			"/admin/users/management_v2/get_copy_user/"+username,
			"GET",
			{},
			function(result){
		
				if(result.return.status == "01") {
					
					gt_COPY_USER = result.dataitem;
					fn_SAVE_COPY_USER_DATA();
					busy_diag.close();				

					
				}else if(result.return.status == "02"){
					
					busy_diag.close();
					fn_show_notification_message("No record found.");
					
				}else{
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
	}
	
	//SAVE User data
	function fn_SAVE_COPY_USER_DATA(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lt_copy_user = [];
		var lv_stat = "";
		
		
		var lv_copy_dispname = ui('ACCT_MNT_INPUT_COPY_USER_DISPNAME').getValue().trim();
		var lv_copy_fname = ui('ACCT_MNT_INPUT_COPY_USER_FNAME').getValue().trim();
		var lv_copy_lname = ui('ACCT_MNT_INPUT_COPY_USER_LNAME').getValue().trim();
		
		var lv_username = sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_TO');
		var lv_email 	= sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_EMAIL');
		var lv_pass 	= sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_PASS');
		var lv_passCn 	= sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_PASSCN');
		var lv_dispname = (lv_copy_dispname || "") ? lv_copy_dispname : gt_COPY_USER[0].name;
		var lv_fname    = (lv_copy_fname || "") ? lv_copy_fname : gt_COPY_USER[0].firstname;
		var lv_lname    = (lv_copy_lname || "") ? lv_copy_lname : gt_COPY_USER[0].lastname;
		var lv_status	= sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_STATUS');
		var lv_welcome  = sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_WELCOME');

		
		
		if(lv_username.getValueState() != 'Success'&& lv_email.getValueState() != 'Success'){
			
			fn_show_notification_message("Please rectify the errors", 2000);

		}else{
			
			var urlToPost 	= '/admin/users/management/system/register';
			var dataToSend = {
			
				username				:	lv_username.getValue() ,
				password				:	lv_pass.getValue(),
				email					:	lv_email.getValue(), 
				name					:	lv_dispname, 
				firstname				:	lv_fname,
				lastname				:	lv_lname,
				'password_confirmation'	:	lv_passCn.getValue()
				
			}
			
			callService.postData(urlToPost,dataToSend,function(callBack){
				if(callBack.error ==true){
					
					console.log(callBack.request.email);
					fn_show_notification_message(callBack.request.email[0]);	
					console.log(callBack.request.password);
					fn_show_notification_message(callBack.request.password[0]);
				
					console.log(callBack.request.username);
					fn_show_notification_message(callBack.request.username[0]);
					fn_show_notification_message("Please Rectify The Errors");
					
					busy_diag.close();
					
				}else{
					

					fn_SAVE_COPY_USER();
					busy_diag.close();
			
				}
			});
		}
		
	}
	
	// Save copy user
	function fn_SAVE_COPY_USER(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lv_flag = false;
		var lv_acct_auth = 0;
		var lv_acct_pref = 0;
		
		var lt_copy_userparam 	= fn_GET_COPY_USERPARAM_DATA();
		var lt_copy_userrole 	= fn_GET_COPY_USERROLE_DATA();
		
		console.log(lt_copy_userparam);
		console.log(lt_copy_userrole);
		
		//save glbcuserrole
		if(ui("ACCT_MNT_INPUT_COPY_USER_ACCT_AUTH").getSelected() == true){lv_acct_auth = 1;}
		
		//save glbuserparam
		if(ui("ACCT_MNT_INPUT_COPY_USER_ACCT_PREF").getSelected() == true){lv_acct_pref = 1;}
		
		var record = {
			APP_ID 	 : SelectedAppID,
			items:{
				lv_acct_auth 		: lv_acct_auth,
				lv_acct_pref 		: lv_acct_pref,
				lt_copy_userparam	: lt_copy_userparam,
				lt_copy_userrole	: lt_copy_userrole,
			}
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/create_copy_user",
			"POST",
			record,
			function(result){
				
				if(result.status == "01"){
					
					fn_show_notification_message(gt_Global_Message.T03);
					
					fn_GET_USER();
					
					//ui("MENU_01").setSelected(true);
					gv_selected_menu = "AM_ACCMAIN";
					fn_clear_inputted_values("COPY_USER");
					
					ui("ACCT_MNT_DIALOG_COPY_USER").close();
					busy_diag.close();
					
				}else{
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
	}
	
	
	// Get User role
	function fn_GET_COPY_USERROLE_DATA(){
		
		var lt_userrole = [];
		var count = 0;
		
		for(var i = 0; i < gt_COPY_USER.length; i++){

			if(gt_COPY_USER[i].ROLE === null){
				//do nothing
			}else{
				if(lt_userrole.length === 0){

					lt_userrole.push({
				
						USER_ID			: ui("ACCT_MNT_INPUT_COPY_USER_TO").getValue(),
						ROLE			: gt_COPY_USER[i].ROLE,
						VALID_FR		: gt_COPY_USER[i].VALID_FR,
						VALID_TO		: gt_COPY_USER[i].VALID_TO,
						STATUS 			: gt_COPY_USER[i].STATUS
						
					});
				}

				else{
					for(var x = 0; x < lt_userrole.length; x++){
						
						if(gt_COPY_USER[i].ROLE == lt_userrole[x].ROLE){count++;}
					}
					
					if(count == 0){
						
						lt_userrole.push({
					
							USER_ID			: ui("ACCT_MNT_INPUT_COPY_USER_TO").getValue(),
							ROLE			: gt_COPY_USER[i].ROLE,
							VALID_FR		: gt_COPY_USER[i].VALID_FR,
							VALID_TO		: gt_COPY_USER[i].VALID_TO,
							STATUS 			: gt_COPY_USER[i].STATUS
							
						});
					}
					count = 0;
				}
			}
		}
		return lt_userrole;
	
	}	
	
	// Get user parameter
	function fn_GET_COPY_USERPARAM_DATA(){
		
		var lt_userparam = [];
		var count = 0;
		
		for(var i = 0; i < gt_COPY_USER.length; i++){
			
			if(gt_COPY_USER[i].PARAM_ID === null){
				//do nothing
			}else{
				
				if(gt_COPY_USER[i].PARAM_ID !== "UP_MOBID_U"){

					if(lt_userparam.length === 0){
						
						lt_userparam.push({
				
							USER_ID			: ui("ACCT_MNT_INPUT_COPY_USER_TO").getValue(),
							PARAM_ID		: gt_COPY_USER[i].PARAM_ID,
							VALUE			: gt_COPY_USER[i].VALUE	
						});
					}

					else{
						for(var x = 0; x < lt_userparam.length; x++){
							if(gt_COPY_USER[i].PARAM_ID == lt_userparam[x].PARAM_ID){count++;}
						}
						if(count == 0){
								
							lt_userparam.push({
					
								USER_ID			: ui("ACCT_MNT_INPUT_COPY_USER_TO").getValue(),
								PARAM_ID		: gt_COPY_USER[i].PARAM_ID,
								VALUE			: gt_COPY_USER[i].VALUE	
							});
						}
						count = 0;
					}
				}
			}
		}
		
		return lt_userparam;
	
	}	
	
	//Get password policy value
	function fn_GET_PASSWORD_POLICY(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		gt_PWDPOLICY = [];
		
		var record = {
			APP_ID		: SelectedAppID,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/get_password_policy",
			"GET",
			record,
			function(result){
				
				if (result.return.status=="01") {
				
					gt_PWDPOLICY = result.dataitem;
					
					ui("PWD_POLC_DSP_INPT_MINIMUM_LENGTH").setValue(gt_PWDPOLICY[0].MIN_LENGTH)
					ui("PWD_EXPI_DSP_INPT_MINIMUM_LENGTH").setValue(gt_PWDPOLICY[0].EXPIRY_DAY)
					ui("PWD_HIST_DSP_INPT_MINIMUM_LENGTH").setValue(gt_PWDPOLICY[0].HISTORY_SIZE)
					
					if(gt_PWDPOLICY[0].UPPERCASE == "X"){
						ui("PWD_POLC_DSP_INPT_UPPERCASE").setSelected(true);
					}else{
						ui("PWD_POLC_DSP_INPT_UPPERCASE").setSelected(false);
					}
					if(gt_PWDPOLICY[0].LOWERCASE == "X"){
						ui("PWD_POLC_DSP_INPT_LOWERCASE").setSelected(true);
					}else{
						ui("PWD_POLC_DSP_INPT_LOWERCASE").setSelected(false);
					}
					if(gt_PWDPOLICY[0].NUMBER == "X"){
						ui("PWD_POLC_DSP_INPT_NUMBER").setSelected(true);
					}else{
						ui("PWD_POLC_DSP_INPT_NUMBER").setSelected(false);
					}
					if(gt_PWDPOLICY[0].NONALPHA == "X"){
						ui("PWD_POLC_DSP_INPT_NONALPHA").setSelected(true);
					}else{
						ui("PWD_POLC_DSP_INPT_NONALPHA").setSelected(false);
					}
					
					busy_diag.close();
					
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				fn_show_notification_message("Failed to get data.");
				busy_diag.close(); 
			},
		);
	}
	
	//Save password policy
	function fn_SAVE_PASSWORD_POLICY(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		var uppercase 		= "";
		var lowercase 		= "";
		var number 			= "";
		var nonalpha 		= "";
        
		if(ui("PWD_POLC_DSP_INPT_UPPERCASE").getSelected() == true){uppercase = "X";} 
		if(ui("PWD_POLC_DSP_INPT_LOWERCASE").getSelected() == true){lowercase = "X";}
		if(ui("PWD_POLC_DSP_INPT_NUMBER").getSelected() == true){number = "X";}
		if(ui("PWD_POLC_DSP_INPT_NONALPHA").getSelected() == true){nonalpha = "X";}
		
		var lt_glbcpwpolicy = {
			ID				: '1',
			UPPERCASE		: uppercase,
			LOWERCASE		: lowercase,
			NUMBER			: number,
			NONALPHA		: nonalpha,
			MIN_LENGTH 		: parseInt(ui("PWD_POLC_DSP_INPT_MINIMUM_LENGTH").getValue()),
			EXPIRY_DAY      : parseInt(ui("PWD_EXPI_DSP_INPT_MINIMUM_LENGTH").getValue()),
			HISTORY_SIZE    : parseInt(ui("PWD_HIST_DSP_INPT_MINIMUM_LENGTH").getValue()),
		}
		
		var record = {
			APP_ID		: SelectedAppID,
			items 		: lt_glbcpwpolicy,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/update_user_glbcpwpolicy",
			"POST",
			record,
			function(result){
				
				if(result.status === "01"){
					
					fn_GET_PASSWORD_POLICY();
					
					fn_show_notification_message(gt_Global_Message.T34);
					busy_diag.close();
					
				}else{
					
					fn_show_notification_message(gt_Global_Message.T35);
					busy_diag.close();
					
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}
//2016.02.18 END - Nahor
	
//GET CATALOG
	function fn_get_catalog(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_catalog = [];
		var lv_state = false;
		
		var record = {
			APP_ID		: SelectedAppID,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/get_catalog",
			"GET",
			record,
			function(result){
			
				if(result.return.status == "01") {
					
					for(var i = 0; i < result.dataitem.length; i++){
						
						if(result.dataitem[i].MAIN == 'X'){
							lv_state = true;
						}else{
							lv_state = false;
						}
						
						gt_catalog.push({
							CATALOG_ID		: result.dataitem[i].CATALOG_ID,
							TITLE			: result.dataitem[i].TITLE, 
							ICON			: result.dataitem[i].ICON, 
							TARGETURL		: result.dataitem[i].TARGETURL, 
							MAIN			: result.dataitem[i].MAIN,
							STATE			: lv_state
						});
					}
					
					fn_bind_catalog_list(gt_catalog);
					busy_diag.close();
					
				}else if(result.return.status == "02"){
					busy_diag.close();
					fn_show_notification_message("No record found.");
				}else{
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
	}
	
//BIND CATALOG LIST
	function fn_bind_catalog_list(data){
	
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);
		
		
		sap.ui.getCore().byId('CATALOG_LIST').setModel(lo_model).bindAggregation("items",{
			path:"/",
			template: ui("CATALOG_LIST_TMP")
		});
		
		ui("CATALOG_MAINTENANCE_LEFT_SEARCH").fireLiveChange();
		go_App_Right.to("PAGE_CATALOG_APPLICATIONS_RIGHT");
		go_App_Left.to("PAGE_CATALOG_MAINTENANCE_LEFT");
	}
	
//SET CATALOG DETAILS
	function fn_set_catalog_details(lv_catalog_id){
		
		for(var i = 0; i < gt_catalog.length; i++){
			
			if(lv_catalog_id == gt_catalog[i].CATALOG_ID){
				
				ui("CAT_MNT_INPUT_NEW_CAT_CATALOG_DESC").setValue(gt_catalog[i].TITLE);
				ui("CAT_MNT_INPUT_NEW_CAT_TARGET_URL").setValue(gt_catalog[i].TARGETURL);
				ui("CAT_MNT_INPUT_NEW_CAT_ICON").setValue(gt_catalog[i].ICON);
				ui("CAT_MNT_INPUT_NEW_CAT_MAIN").setState(gt_catalog[i].STATE);
				break;
				
			}else{
				//do nothing
			}
		}
	}

//GET APPLICATIONS
	function fn_get_applications(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_applications = [];
		gt_applications_data_index = [];
		
		var record = {
			APP_ID		: SelectedAppID,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbmtstc",
			"GET",
			record,
			function(result){
			
				if(result.return.status == "01" || result.return.status == "02"){
					
					for(var i = 0; i < result.dataitem.length; i++){
						var lv_state = false;
						
						if(result.dataitem[i].STATUS == '01'){
							lv_state = true;
						}
						
						gt_applications.push({
							TSTC 		: result.dataitem[i].TSTC, 
							TITLE		: result.dataitem[i].TITLE, 
							TARGETURL	: result.dataitem[i].TARGETURL,
							ICON		: result.dataitem[i].ICON, 
							IMAGE 		: result.dataitem[i].IMAGE, 
							STATUS		: result.dataitem[i].STATUS,
							STATE		: lv_state
						});
						
						gt_applications_data_index[result.dataitem[i].TSTC] = {
							TSTC 		: result.dataitem[i].TSTC, 
							TITLE		: result.dataitem[i].TITLE, 
							TARGETURL	: result.dataitem[i].TARGETURL,
							ICON		: result.dataitem[i].ICON, 
							IMAGE 		: result.dataitem[i].IMAGE, 
							STATUS		: result.dataitem[i].STATUS,
							STATE		: lv_state
						};
					}
					
					gt_applications_bk = JSON.parse(JSON.stringify(gt_applications));
					
					busy_diag.close();
					fn_BIND_APPLICATIONS_VALHELP(gt_applications,false);
					fn_bind_app_list(gt_applications);
				}else{
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);
		
	}
	
	function fn_VALIDATE_APPLICATION(lv_tstc){
		
		var lv_isvalid = false
		var lt_array = [];
		
		for(var x =0; x < gt_applications.length; x++){
			lt_array.push(gt_applications[x].TSTC.toUpperCase());
		}
		
		if ($.inArray(lv_tstc, lt_array) !== -1){
			lv_isvalid = true;
		}
		
		return 	lv_isvalid;
	}
	
//BIND APPLICATION
	function fn_bind_app_list(data){	
		setTimeout(function(){fn_freeze_table_header();}, 500);
		
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);
			
		ui('GO_APPLICATION_TABLE').setModel(lo_model).bindRows("/");
		ui('go_Lbl_App_Table').setText("Applications"+" "+"("+data.length+")");
		
		ui('STATE').setTemplate(new sap.m.Switch({type: sap.m.SwitchType.AcceptReject,state: "{STATE}",enabled: false}));
		ui('APP_DESCRIPTION').setTemplate(new sap.m.Text({text: "{TITLE}", width:"100%",textAlign:"Left"}));
		ui('TARGET_URL').setTemplate(new sap.m.Text({text: "{TARGETURL}", width:"100%",textAlign:"Left"}),);
		ui('ICON').setWidth("50px").setTemplate(new sap.m.Button({icon: "{ICON}", width:"100%",textAlign:"Left"}),);
		
		gv_mode = "display";
		go_App_Right.to("PAGE_CATALOG_APPLICATIONS_RIGHT");
	}
	
//REBIND APPLICATION
	function fn_rebind_app_list(data){
		
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);
			
		ui('GO_APPLICATION_TABLE').setModel(lo_model).bindRows("/");
		sap.ui.getCore().byId('go_Lbl_App_Table').setText("Applications (" + data.length +")");
		setTimeout(function(){fn_freeze_table_header();}, 500);
		
		ui('STATE').setTemplate(new sap.m.Switch({type: sap.m.SwitchType.AcceptReject,state: "{STATE}",enabled: true}));
		ui('APP_DESCRIPTION').setTemplate(new sap.m.Input({value: "{TITLE}", width:"100%",textAlign:"Left"}));
		ui('TARGET_URL').setTemplate(new sap.m.Input({value: "{TARGETURL}", width:"100%",textAlign:"Left"}),);
		ui('ICON').setWidth("200px").setTemplate(new sap.m.Input({value: "{ICON}", width:"100%",textAlign:"Left"}));
		
		gv_mode = "edit";
		go_App_Right.to("PAGE_CATALOG_APPLICATIONS_RIGHT");
	}
	
//FUNCTION BIND CATALOG ID VALUE HELP
	function fn_BIND_APPLICATIONS_VALHELP(data,lv_open){
		
		var lo_model = new sap.ui.model.json.JSONModel();
		lo_model.setSizeLimit(data.length);
		lo_model.setData(data);
		
		var template_list_valuehelp= new sap.m.StandardListItem({
			type:"Active",
			title:"{TITLE}",
			description:"{TSTC}"
		
		});
		
		ui('GO_DIALOG_SHOW_VALUEHELP_APPLICATION').setModel(lo_model).bindAggregation("items",{
			path:"/",
			template:template_list_valuehelp
		});
		
		if(lv_open == true){
			ui("GO_DIALOG_SHOW_VALUEHELP_APPLICATION").open();
		}
		
	}
	
	
//GET ASSIGNMENT
	function fn_get_assignment(cat_id){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		gt_assignment = [];
		gt_assignment_data_index = [];
		gt_assignment_deleted = [];
		
		var record = {
			APP_ID		: SelectedAppID,
			"CAT_ID"	: cat_id
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbccatassign",
			"GET",
			record,
			function(result){
		
				if(result.return.status == "01"){
					
					for(var i = 0; i < result.dataitem.length; i++){
						
						var lv_state = false;
						
						if(result.dataitem[i].STATUS == '01'){
							lv_state = true;
						}
							gt_assignment.push({
								ID 				: result.dataitem[i].ID, 
								CATALOG_ID 		: result.dataitem[i].CATALOG_ID, 
								CONTENT			: result.dataitem[i].CONTENT, 
								SEQUENCE		: result.dataitem[i].SEQ,
								TITLE			: result.dataitem[i].TITLE,
								STATUS			: result.dataitem[i].STATUS,
								STATE			: lv_state
							});
							
							gt_assignment_data_index[result.dataitem[i].CONTENT] = {
								ID 				: result.dataitem[i].ID, 
								CATALOG_ID 		: result.dataitem[i].CATALOG_ID, 
								CONTENT			: result.dataitem[i].CONTENT, 
								SEQUENCE		: result.dataitem[i].SEQ,
								TITLE			: result.dataitem[i].TITLE,
								STATUS			: result.dataitem[i].STATUS,
								STATE			: lv_state
							};
						
					}
					
					gt_assignment_bk = JSON.parse(JSON.stringify(gt_assignment));
					
					//DETERMINE IF THERE ARE ASSIGNMENTS UNDER CATALOG
					if(gt_assignment.length === 0){
						ui("DIALOG_CATALOG_BTN_DELETE").setEnabled(true);
					}else{
						ui("DIALOG_CATALOG_BTN_DELETE").setEnabled(false);
					}
					
					fn_bind_assignment_list(gt_assignment);
					busy_diag.close();
				}else{
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close(); 
			}
		);	
	}
	
//VALIDATE ASSIGNMENT_CONTENT
	function fn_VALIDATE_ASSIGNMENT_CONTENT(lv_value){
		
		var lv_isvalid = false
		var lv_found = false;
		var lv_count = 0;
		
		console.log(lv_value);
		
		var lt_app = [];
		for(var x =0; x < gt_applications.length; x++){
			lt_app.push(gt_applications[x].TSTC.toUpperCase());
		}
		
		if ($.inArray(lv_value, lt_app) !== -1){
			lv_isvalid = true;
		}
		
		console.log(lv_isvalid);
		
		if(lv_isvalid){
			
			var lt_assign = [];
			for(var i =0; i < gt_assignment.length; i++){
				lt_assign.push(gt_assignment[i].CONTENT.toUpperCase());
			}
			
			if ($.inArray(lv_value, lt_assign) !== -1){
				lv_found = true;
			}else{
				lv_found = false;
			}
			
		}else{
			lv_found = true;
		}
		
    	return lv_found;
	}
	
//BIND ASSIGNMENT
	function fn_bind_assignment_list(data){
		
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);
		
		//var lo_table = $('#GO_ASSIGNMENT_TABLE-listUl');
			//lo_table.floatThead('destroy');	
		
		//sap.ui.getCore().byId('GO_ASSIGNMENT_TABLE').setModel(lo_model).bindAggregation("items",{
			//path:"/",
			//template: ui("GO_ASSIGNMENT_TABLE_TEMPLATE_TEXT")
			
		//});
		ui('GO_ASSIGNMENT_TABLE').setModel(lo_model).bindRows("/");
		ui("LABEL_ASSIGNMENTS").setText("Assignments("+data.length+")");
		ui("GO_SCROLLCONTAINER_ASSIGNMENT_TABLE").setVisible(true);
		
		ui('ID').setTemplate(new sap.m.Text({text:"{ID}"}));
		ui('CATALOG_ID').setTemplate(new sap.m.Text({text:"{CATALOG_ID}"}),);
		ui('CONTENT').setTemplate(new sap.m.Text({text:"{CONTENT}"}),);
		ui('SEQUENCE').setTemplate(new sap.m.Text({text:"{SEQUENCE}"}),);
		ui('STATE1').setTemplate(new sap.m.Switch({type: sap.m.SwitchType.AcceptReject,state: "{STATE}",enabled: false}),);
		ui('DELETE').setVisible(false);		
		//ui("GO_ASSIGNMENT_TABLE").setMode("None");
		setTimeout(function(){fn_freeze_table_header();}, 500);
		
		gv_mode = "display";
		go_App_Right.to("PAGE_CATALOG_ASSIGNMENT_RIGHT");
	}
	
//REBIND ASSIGNMENT
	function fn_rebind_assignment_list(data){
		
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);
		
		//var lo_table = $('#GO_ASSIGNMENT_TABLE-listUl');
			//lo_table.floatThead('destroy');	
		
		//sap.ui.getCore().byId('GO_ASSIGNMENT_TABLE').setModel(lo_model).bindAggregation("items",{
			//path:"/",
			//template: ui("GO_ASSIGNMENT_TABLE_TEMPLATE_INPUT")		
		//});
			ui('ID').setTemplate(new sap.m.Text({text:"{ID}"}));
			ui('CATALOG_ID').setTemplate(new sap.m.Text({text:"{CATALOG_ID}"}),);
			ui('CONTENT').setTemplate(
				new sap.m.Input({
					value: "{CONTENT}",
					showValueHelp: true,
					width: "200px",
					valueHelpRequest: function(oEvt){
						
						gv_input_id = oEvt.getSource().sId;
						console.log(gv_input_id);
						//ui("GO_DIALOG_SHOW_VALUEHELP_APPLICATION").open();
						fn_BIND_APPLICATIONS_VALHELP(gt_applications,true);
						
					},
					change: function(oEvt){
						
						var lv_value = oEvt.getSource().getValue().trim();
						oEvt.getSource().setValue(lv_value.toUpperCase());
						
						if(lv_value){
							if(fn_validate_assignment_table(lv_value) != true){

								oEvt.getSource().setValueState("None");
								oEvt.getSource().setValueStateText("");

							}else{

								oEvt.getSource().setValueState("Error");
								oEvt.getSource().setValueStateText("Content is not existing or already assigned.");

							}
							
						}else{
							oEvt.getSource().setValueState("None");
							oEvt.getSource().setValueStateText("");
						}
					}
				}),
			
			);
			ui('SEQUENCE').setTemplate(new sap.m.Input({value:"{SEQUENCE}", width: "50px"}));
			ui('STATE1').setTemplate(new sap.m.Switch({
					type: sap.m.SwitchType.AcceptReject,
					state: "{STATE}",
			}));
			ui('DELETE').setVisible(true);	
				
		ui("LABEL_ASSIGNMENTS").setText("Assignments("+data.length+")");
		ui('GO_ASSIGNMENT_TABLE').getModel().refresh();
		ui("GO_SCROLLCONTAINER_ASSIGNMENT_TABLE").setVisible(true);
		//ui("GO_ASSIGNMENT_TABLE").setMode("Delete");
		setTimeout(function(){fn_freeze_table_header();}, 1500);
		
		gv_mode = "edit";
		go_App_Right.to("PAGE_CATALOG_ASSIGNMENT_RIGHT");
		
	}
	
/*
// ================================================================================
// Function to GLBMCATALOG
// ================================================================================
*/	
	function fn_CREATE_GLBMCATALOG(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lt_GLBMCATALOG = [];
		
		var lv_stat = "";
		var lv_main = "";
		
		if(ui("CAT_MNT_INPUT_NEW_CAT_MAIN").getState()==false){
			lv_stat = "02";
			lv_main = "";
		}else{
			lv_stat = "01";
			lv_main = "X";
		}
			

			lt_GLBMCATALOG = {
				catalog_id		: ui("CAT_MNT_INPUT_NEW_CAT_CATALOG_ID").getValue().trim(),
				title			: ui("CAT_MNT_INPUT_NEW_CAT_CATALOG_DESC").getValue().trim(),
				icon			: ui("CAT_MNT_INPUT_NEW_CAT_ICON").getValue().trim(),
				targetURL		: ui("CAT_MNT_INPUT_NEW_CAT_TARGET_URL").getValue().trim(),
				main			: lv_main,
				status			: lv_stat,
			}
			
		var record = {
			items:lt_GLBMCATALOG
		}
			
		fn_ajax_call(
			"/admin/users/management/create_user_glbmcatalog",
			"POST",
			record,
			function(result){
				fn_new_catalog_set_to_default(); //set to default
				if(result.status == "01"){
					
					fn_get_catalog();
					//fn_show_notification_message(gt_Global_Message.T36);
					fn_show_notification_message(result.message);
					busy_diag.close();
					ui("DIALOG_CATALOG").close();

				}else if(result.status == "02"){
					fn_show_notification_message(gt_Global_Message.T37);
					
					busy_diag.close();

				}else{
					busy_diag.close();

				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
	}
	
/*
// ================================================================================
// Function to UPDATE CATALOG
// ================================================================================
*/	
	function fn_UPDATE_GLBMCATALOG(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		var lv_stat = "";
		var lv_main = "";
		
		if(ui("CAT_MNT_INPUT_NEW_CAT_MAIN").getState()==false){
			lv_stat = "02";
			lv_main = "";
		}else{
			lv_stat = "01";
			lv_main = "X";
		}
		
		var lt_glbmcatalog = {
			CATALOG_ID	: ui("CAT_MNT_INPUT_NEW_CAT_CATALOG_ID").getValue(),
			TITLE		: ui("CAT_MNT_INPUT_NEW_CAT_CATALOG_DESC").getValue(),
			TARGETURL	: ui("CAT_MNT_INPUT_NEW_CAT_TARGET_URL").getValue(),
			ICON		: ui("CAT_MNT_INPUT_NEW_CAT_ICON").getValue(),
			MAIN		: lv_main,
			STATUS		: lv_stat
		}
		
		var record = {
			APP_ID		: SelectedAppID,
			items 		: lt_glbmcatalog,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/update_user_glbmcatalog",
			"POST",
			record,
			function(result){
				
				fn_new_catalog_set_to_default(); //set to default
				if(result.status == "01"){
					
					fn_get_catalog();
					fn_show_notification_message(gt_Global_Message.T48);
					busy_diag.close();
					ui("DIALOG_CATALOG").close();

				}else if(result.status == "02"){
					fn_show_notification_message(gt_Global_Message.T49);
					
					busy_diag.close();

				}else{
					busy_diag.close();

				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
	}
	
/*
// ================================================================================
// Function to delete role
// ================================================================================
*/	
	function fn_DELETE_CATALOG(lv_catalog){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			//busy_diag.open();
		
		var record = {
			APP_ID		: SelectedAppID,
			items 		: lv_catalog,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/delete_user_glbmcatalog",
			"POST",
			record,
			function(result){
			
				fn_new_catalog_set_to_default();

				if(result.status == "01"){
					
					fn_get_catalog();
					fn_new_catalog_set_to_default();
					fn_show_notification_message("Successfully deleted catalog.");
					busy_diag.close();

				}else{
					fn_get_catalog();
					fn_show_notification_message("Failed to delete catalog");
					busy_diag.close();
				}
				
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}
/*
// ================================================================================
// Function to UPDATE APPLICATIONS
// ================================================================================
*/	
	function fn_UPDATE_APPLICATION(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lt_glbmtstc_insert = [];
		var lt_glbmtstc_update = [];
		
		for(var i=0; i<gt_applications.length; i++){
			
			var lv_status = "";
			if(gt_applications[i].STATE == false){
				lv_status = "02";
			}else{
				lv_status = "01"
			}
			
			var lv_insert = true;
			var lv_update = false;
			
			for(var x=0; x < gt_applications_bk.length; x++){
				
				if(gt_applications_bk[x].TSTC === gt_applications[i].TSTC){
					
					if(
						gt_applications_bk[x].TITLE !== gt_applications[i].TITLE ||
						gt_applications_bk[x].TARGETURL !== gt_applications[i].TARGETURL ||
						gt_applications_bk[x].ICON !== gt_applications[i].ICON ||
						gt_applications_bk[x].IMAGE !== gt_applications[i].IMAGE ||
						gt_applications_bk[x].STATUS !== lv_status
					){
						lv_update = true;
					}
					
					lv_insert = false;
					break;
				}
				
			}
			
			if(lv_insert == true){
				lt_glbmtstc_insert.push({
					TSTC			: gt_applications[i].TSTC,
					TITLE			: gt_applications[i].TITLE,
					TARGETURL		: gt_applications[i].TARGETURL,
					ICON			: gt_applications[i].ICON,
					IMAGE			: gt_applications[i].IMAGE,
					STATUS			: lv_status
				});
			}else if(lv_update == true){
				lt_glbmtstc_update.push({
					TSTC			: gt_applications[i].TSTC,
					TITLE			: gt_applications[i].TITLE,
					TARGETURL		: gt_applications[i].TARGETURL,
					ICON			: gt_applications[i].ICON,
					IMAGE			: gt_applications[i].IMAGE,
					STATUS			: lv_status
				});
			}
		}
		
		var lt_array = {
			APP_ID			: SelectedAppID,
			glbmtstc_insert	: lt_glbmtstc_insert,
			glbmtstc_update	: lt_glbmtstc_update,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/update_user_glbmtstc",
			"POST",
			lt_array,
			function(result){
			
				if(result.status == "01"){
					fn_get_applications();
					fn_application_set_button_to_default();
					fn_show_notification_message(gt_Global_Message.T40);
					busy_diag.close();
					
				}else if(result.status == "02"){
					
					fn_show_notification_message(result.message);
					busy_diag.close();
					
				}else{
					
					console.log(result.message)
					busy_diag.close();
					
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
	}
	
/*
// ================================================================================
// Function to delete application
// ================================================================================
*/	
	function fn_DELETE_APPLICATION(lv_tstc){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		var record = {
			items :lv_tstc,
		}
		
		fn_ajax_call(
			"/admin/users/management/delete_user_glbmtstc",
			"POST",
			record,
			function(result){	
			
				if(result.status == "01"){

					fn_get_applications();
					fn_application_set_button_to_default();
					fn_show_notification_message(gt_Global_Message.T42);
					busy_diag.close();
					
				}else if(result.status == "02"){
					fn_show_notification_message(gt_Global_Message.T43);
					busy_diag.close();
				}else{
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		

	}
	
	
/*
// ================================================================================
// Function to UPDATE ASSIGNMENT
// ================================================================================
*/	
	function fn_UPDATE_GLBCCATASSIGN(){
		
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		//var lt_glbccatassign = fn_GET_ASSIGNMENT_DATA();
		
		var lt_glbccatassign_insert = [];
		var lt_glbccatassign_update = [];
	
		for(var i=0; i<gt_assignment.length; i++){
			
			var lv_id = gt_assignment[i].ID;
			var lv_status = "";
			
			if(gt_assignment[i].STATE== false){
				lv_status = "02";
			}else{
				lv_status = "01"
			}
			
			if( lv_id != ""){
				
				for(var x=0; x < gt_assignment_bk.length; x++){
					
					if(gt_assignment_bk[x].ID === gt_assignment[i].ID){
						
						if(
							gt_assignment_bk[x].CATALOG_ID 	!== gt_assignment[i].CATALOG_ID ||
							gt_assignment_bk[x].CONTENT 	!== gt_assignment[i].CONTENT ||
							gt_assignment_bk[x].SEQUENCE 	!== gt_assignment[i].SEQUENCE ||
							gt_assignment_bk[x].STATUS 		!== lv_status
						
						){
							lt_glbccatassign_update.push({
								ID				: gt_assignment[i].ID,
								CATALOG_ID		: gt_assignment[i].CATALOG_ID,
								CONTENT			: gt_assignment[i].CONTENT,
								SEQ				: gt_assignment[i].SEQUENCE,
								STATUS			: lv_status
							});
							
							break;
						}
					}
					
				}
				
			}else{
				
				lt_glbccatassign_insert.push({
					ID				: gt_assignment[i].ID,
					CATALOG_ID		: gt_assignment[i].CATALOG_ID,
					CONTENT			: gt_assignment[i].CONTENT,
					SEQ				: gt_assignment[i].SEQUENCE,
					STATUS			: lv_status
				});
				
			}
		}
		
		var lt_array = {
			APP_ID					: SelectedAppID,
			glbccatassign_insert	: lt_glbccatassign_insert,
			glbccatassign_update	: lt_glbccatassign_update,
			deleted_items			: gt_assignment_deleted,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/update_user_glbccatassign",
			"POST",
			lt_array,
			function(result){
			
				if(result.status == "01"){
					
					fn_get_assignment(gv_selected_assignment);
					fn_assignment_set_button_to_default();
					fn_show_notification_message(gt_Global_Message.T46);
					busy_diag.close();
					
				}else if(result.status == "02"){
					
					fn_show_notification_message(result.message);
					busy_diag.close();
					
				}else{
					console.log(result.message)
					busy_diag.close();
				}
			
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
	}
	
	function fn_bind_valuehelp_announcement(data){

		var lv_item_tstc;
		var lt_data_tstc = [];

		lt_data_tstc.push({

			ID:"DASHBOARD",
			description:"Dashboard"
		});

		console.log(lt_data_tstc)
		
		ui("app_id_create").destroyItems();
		ui("app_id_edit").destroyItems();
		
		for(var i =0; i < lt_data_tstc.length; i++){

			lv_item_tstc = new sap.ui.core.Item({text:lt_data_tstc[i].description, key:lt_data_tstc[i].ID});
			ui('app_id_create').addItem(lv_item_tstc);

		}

		for(var i =0; i < lt_data_tstc.length; i++){

			lv_item_tstc = new sap.ui.core.Item({text:lt_data_tstc[i].description, key:lt_data_tstc[i].ID});
			ui('app_id_edit').addItem(lv_item_tstc);
			
		}
		
	}

	//Display this saved editor details
	function fn_request_editor_listing(){
	    var gt_editor_listing=[];
	    
	    var lv_busy = fn_show_busy_dialog("Loading .. Please wait");
	    lv_busy.open();
	    
	    fn_ajax_call(
	        GURL_EDITOR_GET,
	        "GET",
			{},
	        function(response){
	            fn_populate_editor_data(response);
	            lv_busy.close();
	            
	        },
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText);
				busy_diag.close();
			}
	    );
	}
	
	function fn_populate_editor_data(response){
		setTimeout(function(){fn_freeze_table_header();}, 1000);

		gv_mode = "display";
		
		var lt_editor_listing = response.result_data;
		for(var i = 0 ; i < lt_editor_listing.length; i++){
			if(lt_editor_listing[i].ACTIVE ==='1'){
				lt_editor_listing[i].ACTIVE = true;
			}else{
			   lt_editor_listing[i].ACTIVE = false; 
			}
		}
		
		var lv_item_template = new sap.m.ColumnListItem({
			type : sap.m.ListType.Navigation,
			cells : [
				new sap.m.Label({text:"{TITLE}"}),
				new sap.m.Label({text:"{LANGUAGE}"}),
				new sap.m.Label({text:"{START_DATE_DSP}"}),
				new sap.m.Label({text:"{END_DATE_DSP}"}),
				new sap.m.HBox({              
					items:[
						new sap.m.Switch({state:'{ACTIVE}',
							change:function(eVt){
								var context =eVt.getSource().getBindingContext();
								fn_submit_editor_data_state(context.getProperty().GUID,this.getState());
							}
						}).addStyleClass('class_button_padding'),
						new sap.m.Button({icon:'sap-icon://delete',tooltip:'Remove Announcement',
							press:function(eVt){
								var context =eVt.getSource().getBindingContext(); 
								GV_EDIT_GUID =context.getProperty().GUID;  
								fn_information('Are you sure you want to remove this announcement ?','gv_remove')
							}
						}).addStyleClass('class_button_padding')]    
				}).addStyleClass("class_hbox_action_buttons"), 
				new sap.m.Label({text:"{created_by}"}),
				new sap.m.Label({text:"{updated_date}"}),
			],
			press:function(eVt){
				
				console.log("Column");
				var context =eVt.getSource().getBindingContext();
			   
				GV_EDIT_GUID = context.getProperty().GUID;
				gv_edit_sub_app = context.getProperty().SUB_APP;
				gv_edit_app_id = context.getProperty().APP_ID;

				console.log(gv_edit_sub_app);
				console.log(gv_edit_app_id);

				gv_mode = "edit";
				gv_selected_id = context.getProperty().ID;
				
				if(context.getProperty().ACTIVE === true){
					context.getProperty().ACTIVE ='1';
				}else{
					context.getProperty().ACTIVE ='0';
				}
				console.log(context.getProperty());

				fn_editor_edit_add(context.getProperty());
			
			}
		});
		
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(lt_editor_listing.length);
			lo_model.setData(lt_editor_listing);
		
		//var lo_table = $('#EDITOR_TABLE-listUl');
			//lo_table.floatThead('destroy');
		
		ui('EDITOR_TABLE').setModel(lo_model).bindRows('/');
			
	}


	function fn_submit_editor_data(gt_editor_data) {
		
		fn_ajax_call(
	        GURL_EDITOR_POST,
	        "POST",
	        gt_editor_data,
	        function(response){
	            
	            if(response.status == "01"){

					go_App_Right.to('editor_display');
					fn_show_notification_message("Successfully created announcement");
	            	fn_request_editor_listing();
	           
	            }else{

	            	fn_show_notification_message("Failed to create announcement");
	            }
			},
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
	}

	function fn_submit_editor_data_edit(gt_editor_data) {
		
	    fn_ajax_call(
	        GURL_EDITOR_POST_EDIT,
	        "POST",
	        gt_editor_data,
	        function(response){
	            
	            if(response.status == "01"){

					go_App_Right.to('editor_display');
					fn_show_notification_message("Successfully updated announcement");
	            	fn_request_editor_listing();
	           
	            }else{
	            	fn_show_notification_message("Failed to update announcement");
	            }
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
	    
		);
	}


	function fn_submit_editor_data_state(lv_guid,lv_state){
	    
	    var lv_state_temp;
	    if(lv_state === true){
	        lv_state_temp ='1';
	    }else{
	        lv_state_temp ='0';
	    }
	    var lt_data ={
	    	'GUID':lv_guid,
	    	'ACTIVE':lv_state_temp,
	    	'POSITION':'0'
	    	
	    };
	    
	    fn_ajax_call(
	        GURL_EDITOR_POST_STATE,
	        "POST",
	        lt_data,
	        function(response){
	           
	            if(response.status == "01"){
					fn_show_notification_message("Successfully updated announcement");
	            	fn_request_editor_listing();
	            }else{
	            	fn_show_notification_message("Failed to update announcement");
	            }
				
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
	    );  
	}

	function  fn_editor_delete(lv_guid) {
	    
	    var lt_data ={
	    	'GUID':lv_guid,
	    	'DEL_FLAG':'1',
	    	'POSITION':'0'
	    };
	    
	    fn_ajax_call(
	        GURL_EDITOR_POST_DELETE,
	        "POST",
	        lt_data,
	        function(response){
	            
	             if(response.status == "01"){

					fn_show_notification_message("Successfully updated announcement");

	            	fn_request_editor_listing();
	           
	            }else{

	            	fn_show_notification_message("Failed to update announcement");
	            }
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
	        
	    );
	}
	
/*
// ================================================================================
// Function to UPDATE USER STATUS
// ================================================================================
*/	
	function fn_UPDATE_USER_STATUS(lv_status){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		
		var lv_id 		= ui("ACCT_MNT_DSP_TEXT_ID").getText();
		var lv_username = ui("OBJECTHEADER_ACCT_MNT_DSP").getObjectTitle();
		var lv_message 	= "";
			
		switch(lv_status){
			
			case "ACTIVE" :
				var lt_user = {
					username	: lv_username,
					status		: "01",
					del_flag 	: "",
				}
				lv_message = "User successfully set to active.";
			break;
			
			case "INACTIVE" :
				var lt_user = {
					username	: lv_username,
					status		: "02",
					del_flag 	: "",
				}
				lv_message = "User successfully set to inactive.";
			break;
			
			case "DELETED" :
				var lt_user = {
					username	: lv_username,
					status		: "03",
					del_flag 	: "X",
				}
				lv_message = "User successfully deleted.";
			break;
			
			case "LOCKED" :
				var lt_user = {
					username	: lv_username,
					status		: "04",
					del_flag 	: "",
				}
				lv_message = "User successfully locked.";
			break;
			
			case "INITIAL" :
				var lt_user = {
					username	: lv_username,
					status		: "05",
					del_flag 	: "",
				}
				lv_message = "User successfully set to initial.";
			break;
			
			case "NOT_INITIAL" :
				var lt_user = {
					username	: lv_username,
					status		: "06",
					del_flag 	: "",
				}
				lv_message = "User successfully set to not initial.";
			break;
			
			case "EXPORT_BLOCKED" :
				var lt_user = {
					username	: lv_username,
					status		: "07",
					del_flag 	: "",
				}
				lv_message = "User successfully export to blocked.";
			break;
			
			case "FILE_CREATED" :
				var lt_user = {
					username	: lv_username,
					status		: "08",
					del_flag 	: "",
				}
				lv_message = "User successfully file created.";
			break;
			
		}
			
		
		
		var record = {
			APP_ID		: SelectedAppID,
			key			: lv_id,
			items 		: lt_user,
		}
	
		fn_ajax_call(
			"/admin/users/management_v2/update_user_status",
			"POST",
			record,
			function(result){

				if(result.status == "01") {
					var evtDesc 	= lv_message + " " + lv_username ;
					$.get( "/admin/event/trace", { app: SelectedAppID, fn: "USRACC_STATUS" ,at: "USER_ACTION", evt: evtDesc});	
					
					fn_prep_data_to_set_account_details(lv_id,lv_username);
					ui("SECTION_ACCT_MNT_DSP_ACCTDETAILS").getCustomAnchorBarButton().firePress();
					
					fn_show_notification_message(lv_message);
					busy_diag.close();
					
				}else if(result.status == "02") {

					fn_show_notification_message("Failed to modify this user.");
					busy_diag.close();
					
				}else{

					busy_diag.close();
				}
					
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}

/*
// ================================================================================
// Function to GET LIST OF LOCK ENTRIES
// ================================================================================
*/
	function fn_get_lock_entries(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		var gt_data_lockentries = [];

		fn_ajax_call(
			"/admin/unlock_entry/get_entries",
			"GET",
			{},
			function(result){
				
				if (result.status=="01") {
					gt_data_lockentries = result.lockentries;
					fn_bind_lock_entries(gt_data_lockentries);
					busy_diag.close();
				}else {
					fn_show_notification_message(result.message);
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);

	}

/*
// ================================================================================
// Function to BIND LIST OF LOCK ENTRIES
// ================================================================================
*/	
	function fn_bind_lock_entries(data){
		setTimeout(function(){

			fn_freeze_table_header();

		},500);

		//ui('LOCK_ENTRY_TABLE').destroyItems();

		var lo_model = new sap.ui.model.json.JSONModel();
		lo_model.setSizeLimit(data.length);
		lo_model.setData(data);

		var lo_table = $('#LOCK_ENTRY_LABEL-listUl');
			lo_table.floatThead('destroy');	 

		ui('LOCK_ENTRY_TABLE').setModel(lo_model).bindRows('/');
			//path:"/",
			//template:ui('LOCK_ENTRY_TEMPLATE'),
		//});

		ui('LOCK_ENTRY_LABEL').setText("My Locked Entries ("+data.length+")");

		go_App_Right.to('LOCK_ENTRY_PAGE');

	}


/*
// ================================================================================
// Function to REMOVE LOCK ENTRY 
// ================================================================================
*/	
	function fn_remove_lockentry_multiple(lt_data){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		var lt_array = {
			lt_data:lt_data
		};

		fn_ajax_call(
			"/admin/users/management/remove_lockentry_multiple",
			"POST",
			lt_array,
			function(result){

				if(result.return.status == "01"){
					fn_get_lock_entries();
					fn_show_notification_message("Successfully deleted");
				}else{
					fn_show_notification_message("Failed to delete");
				}
				
				console.log(result);
				busy_diag.close();
			
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
		);
		
	}
	
	
//========================
// MANAGE USER GUIDE
//========================
	function fn_get_user_guide_listing(){
		
	    gt_user_guide = [];
	    
	    var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
	    
	    fn_ajax_call(
	        "/admin/main/get_user_guide",
	        "GET",
			{},
	        function(result){
				
				for(i=0; i<result.dataitem.length; i++){
					
					var lv_state = false;
					
					if(result.dataitem[i].ACTIVE === "1"){lv_state = true;}
					
					gt_user_guide.push({
						
						ACTIVE          : result.dataitem[i].ACTIVE,     
						APP_ID          : result.dataitem[i].APP_ID,       
						DEL_FLAG        : result.dataitem[i].DEL_FLAG,
						DESCRIPTION     : result.dataitem[i].DESCRIPTION,
						END_DATE        : result.dataitem[i].END_DATE,  
						END_DATE_DSP    : result.dataitem[i].END_DATE_DSP,
						GUID            : result.dataitem[i].GUID, 
						ID              : result.dataitem[i].ID,         
						LANGUAGE        : result.dataitem[i].LANGUAGE,
						LAST_MODIFY     : result.dataitem[i].LAST_MODIFY,
						POSITION        : result.dataitem[i].POSITION,  
						START_DATE      : result.dataitem[i].START_DATE,
						START_DATE_DSP  : result.dataitem[i].START_DATE_DSP,
						SUB_APP         : result.dataitem[i].SUB_APP,
						TITLE           : result.dataitem[i].TITLE,      
						VERSION         : result.dataitem[i].VERSION,
						STATE			: lv_state,
						created_by		: result.dataitem[i].created_by,
						updated_date	: result.dataitem[i].updated_date,
						
					});
				}
				
				fn_bind_user_guide_listing(gt_user_guide);
				busy_diag.close();
				
			},
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText); 
				busy_diag.close();
			}
		);
	}
	
	function fn_bind_user_guide_listing(data){
		
		gv_mode = "display";
		
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);
			
		var lo_item_template = new sap.m.ColumnListItem({
			type : sap.m.ListType.Navigation,
			cells : [
				new sap.m.Label({text:"{TITLE}"}),
				new sap.m.Label({text:"{LANGUAGE}"}),
				new sap.m.Label({text:"{START_DATE_DSP}"}),
				new sap.m.Label({text:"{END_DATE_DSP}"}),
				new sap.m.HBox({             
					items:[
						new sap.m.Switch({state:'{STATE}',
							change:function(eVt){
								
								var lv_selected_value = eVt.getSource().getBindingContext().getProperty();
								var lv_guid	 = lv_selected_value.GUID;
								var lv_state = this.getState();
								
								fn_update_selected_user_guide_active(lv_guid,lv_state);
							}
						}).addStyleClass('class_button_padding'),
						
						new sap.m.Button({icon:'sap-icon://delete',tooltip:'Remove Announcement',
							press:function(eVt){
								
								var lv_selected_value = eVt.getSource().getBindingContext().getProperty();
								var lv_guid	 = lv_selected_value.GUID;
								
								new sap.m.Dialog({
									title:"Confirmation",
									contentWidth : "100px",
									contentHeight : "50px",
									content: [
										new sap.m.Label({
											text: "Are you sure you want to remove this user guide?"
										})
									],
									beginButton: new sap.m.Button({
										text:"Ok", 
										type:"Accept",
										icon:"sap-icon://accept",
										press: function(oEvt){
											
											fn_delete_selected_user_guide(lv_guid);
											oEvt.getSource().getParent().close();
											
										}
									}),
									endButton: new sap.m.Button({
										text:"Cancel",
										type:"Reject",
										icon:"sap-icon://decline",
										press: function(oEvt){
										   oEvt.getSource().getParent().close();
										}
									}),
								}).open().addStyleClass('sapUiSizeCompact')
								
							}
						}).addStyleClass('class_button_padding')
					]    
				}).addStyleClass("class_hbox_action_buttons"), 
				new sap.m.Label({text:"{created_by}"}),
				new sap.m.Label({text:"{updated_date}"}),
			],
			press:function(oEvt){
				
				gv_mode = "edit";
				
				var lv_selected_value = oEvt.getSource().getBindingContext().getProperty();
				GV_EDIT_GUID = lv_selected_value.GUID;
				
				console.log(lv_selected_value);
			    fn_display_selected_user_guide(lv_selected_value);
				
			}
		});
		
		
		//var lo_table = $('#DISPLAY_USER_GUIDE_TABLE-listUl');
			//lo_table.floatThead('destroy');
		
		ui('DISPLAY_USER_GUIDE_TABLE').setModel(lo_model).bindRows('/');
			//path : "/",
			//template : lo_item_template,
		//});
		
		setTimeout(function(){fn_freeze_table_header();}, 1000);
		
	}
	
	function fn_get_valuehelp_user_guide(){

		var busy_diag = fn_show_busy_dialog("");
			busy_diag.open();

		gt_valuehelp_tstc = [];
		
		var lv_data = {
			APP_ID 		: SelectedAppID
		};

		fn_ajax_call(
	        "/admin/users/management_v2/get_valuehelp_announcement",
	        "GET",
			lv_data,
	        function(response){
	           
				fn_bind_valuehelp_user_guide(response);
				busy_diag.close();
				
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
	    );
	}


	function fn_bind_valuehelp_user_guide(data){
		
		var lv_item_tstc;
		var lt_data_tstc = data.glbmtstc;
		
		gt_glbmfunctiontxt	= data.glbmfunctiontxt;
		
		ui('CREATE_USER_GUIDE_APP_ID').destroyItems();
		ui('EDIT_USER_GUIDE_APP_ID').destroyItems();
		
		for(var i =0; i < lt_data_tstc.length; i++){

			lv_item_tstc = new sap.ui.core.Item({
				text	: lt_data_tstc[i].description, 
				key		: lt_data_tstc[i].ID
			});
			ui('CREATE_USER_GUIDE_APP_ID').addItem(lv_item_tstc);
		}

		for(var i =0; i < lt_data_tstc.length; i++){

			lv_item_tstc = new sap.ui.core.Item({
				text	: lt_data_tstc[i].description, 
				key		: lt_data_tstc[i].ID
			});
			ui('EDIT_USER_GUIDE_APP_ID').addItem(lv_item_tstc);
			
		}
	}
	
	function fn_save_create_user_guide(){
		
		var lv_title       	= sap.ui.getCore().byId("CREATE_USER_GUIDE_TITLE").getValue();
		var lv_language    	= sap.ui.getCore().byId("CREATE_USER_GUIDE_LANGUAGE").getSelectedKey();
		var lv_startdate   	= fn_format_datetime(sap.ui.getCore().byId("CREATE_USER_GUIDE_DATE").getDateValue() , "YYYY-MM-DD");
		var lv_enddate     	= fn_format_datetime(sap.ui.getCore().byId("CREATE_USER_GUIDE_DATE").getSecondDateValue() , "YYYY-MM-DD");
		var lv_text        	= sap.ui.getCore().byId("CREATE_USER_GUIDE_TEXTAREA").getValue();
		var lv_app_id		= (sap.ui.getCore().byId("CREATE_USER_GUIDE_APP_ID").getSelectedItem() === null) ? "" : sap.ui.getCore().byId("CREATE_USER_GUIDE_APP_ID").getSelectedKey();
		var lv_sub_app		= (sap.ui.getCore().byId("CREATE_USER_GUIDE_SUB_APP").getSelectedItem() === null) ? "" : sap.ui.getCore().byId("CREATE_USER_GUIDE_SUB_APP").getSelectedKey();

		var lt_data = {
			APP_ID 		: lv_app_id,
			SUB_APP		: lv_sub_app,
			LANGUAGE	: lv_language ,
			TITLE		: lv_title ,
			DESCRIPTION	: lv_text ,
			VERSION		: '1',
			START_DATE	: lv_startdate,
			END_DATE	: lv_enddate,
			ACTIVE		: '1',
			POSITION	: 0,
			USER_GUIDE	: "X"
		};
		
	    fn_ajax_call(
	        GURL_EDITOR_POST,
	        "POST",
	        lt_data,
	        function(response){
	            
	            if(response.status == "01"){

	            	fn_get_user_guide_listing();
					
					fn_show_notification_message("Successfully created new user guide.");
					go_App_Right.to('PAGE_RIGHT_DISPLAY_USER_GUIDE');
	           
	            }else{
	            	fn_show_notification_message("Failed to create new user guide.");
	            }
	        },
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
			}
	    );
	}

	function fn_save_edit_user_guide(){
		
		var lv_title       	= sap.ui.getCore().byId("EDIT_USER_GUIDE_TITLE").getValue();
		var lv_language    	= sap.ui.getCore().byId("EDIT_USER_GUIDE_LANGUAGE").getSelectedKey();
		var lv_startdate   	= fn_format_datetime(sap.ui.getCore().byId("EDIT_USER_GUIDE_DATE").getDateValue() , "YYYY-MM-DD");
		var lv_enddate     	= fn_format_datetime(sap.ui.getCore().byId("EDIT_USER_GUIDE_DATE").getSecondDateValue() , "YYYY-MM-DD");
		var lv_app_id		= (sap.ui.getCore().byId("EDIT_USER_GUIDE_APP_ID").getSelectedItem() === null) ? "" : sap.ui.getCore().byId("EDIT_USER_GUIDE_APP_ID").getSelectedKey();
		var lv_sub_app		= (sap.ui.getCore().byId("EDIT_USER_GUIDE_SUB_APP").getSelectedItem() === null) ? "" : sap.ui.getCore().byId("EDIT_USER_GUIDE_SUB_APP").getSelectedKey();
		var lv_text        	= sap.ui.getCore().byId("EDIT_USER_GUIDE_TEXTAREA").getValue();
		
		var lv_active = "";
		
		if(ui("EDIT_USER_GUIDE_STATUS").getState() === true){
			lv_active = "1";
		}else{
			lv_active = "0";
		}
		
		var lt_data = {
			GUID 		: GV_EDIT_GUID,
			APP_ID 		: lv_app_id,
			SUB_APP		: lv_sub_app,
			LANGUAGE	: lv_language ,
			TITLE		: lv_title ,
			DESCRIPTION	: lv_text ,
			VERSION		: '1',
			START_DATE	: lv_startdate,
			END_DATE	: lv_enddate,
			ACTIVE		: lv_active,
			POSITION	: 0
		};
		
		console.log(lt_data);
		
	    fn_ajax_call(
	        GURL_EDITOR_POST_EDIT,
	        "POST",
	        lt_data,
	        function(response){
				
	            if(response.status == "01"){

					fn_get_user_guide_listing();
					
					fn_show_notification_message("Successfully updated selected user guide.");
					go_App_Right.to('PAGE_RIGHT_DISPLAY_USER_GUIDE');
	           
	            }else{
	            	fn_show_notification_message("Failed to update selected user guide");
	            }
				
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
			}
	    );
	}
	
	function fn_update_selected_user_guide_active(lv_guid,lv_state){
	    
		var lv_active = "";
		
		if(lv_state === true){
			lv_active = "1";
		}else{
			lv_active = "0";
		}
		
		var lt_data = {
			'GUID'		: lv_guid,
			'ACTIVE'	: lv_active,
			'POSITION'	: '0'
		};

	    fn_ajax_call(
	        GURL_EDITOR_POST_STATE,
	        "POST",
	        lt_data,
	        function(response){
	           
	            if(response.status == "01"){

					fn_show_notification_message("Successfully updated user guide active status.");
	            	fn_get_user_guide_listing();
	           
	            }else{
	            	fn_show_notification_message("Failed to update user guide active status.");
	            }
				
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
			}
	    );  
	}

	function fn_delete_selected_user_guide(lv_guid){
		
	    var lt_data = {
	    	'GUID'		: lv_guid,
	    	'DEL_FLAG'	: '1',
	    	'POSITION'	: '0'
	    };
	    
	    fn_ajax_call(
			GURL_EDITOR_POST_DELETE,
			"POST",
			lt_data,
	        function(response){
	            
	            if(response.status == "01"){

					fn_show_notification_message("Successfully deleted user guide.");
	            	fn_get_user_guide_listing();
	           
	            }else{
	            	fn_show_notification_message("Failed to delete user guide.");
	            }
				
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText);
			}
	    );
	}

	
//=======================
// OBJECT MAINTENANCE
//=======================	
	
	function fn_get_glbmobject(){

		var busy_diag = fn_show_busy_dialog("");
			busy_diag.open();
		
		gt_glbmobject_deleted = [];
		gt_glbmobject = [];
		gt_glbmobject_valuehelp = [];
		
		var record = {
			APP_ID		: SelectedAppID,
		}
		
		fn_ajax_call(
	        "/admin/users/management_v2/get_glbmobject",
	        "GET",
			record,
	        function(response){
	           
				if (response.return.status=="01") {
				   
					gt_glbmobject = response.lt_data;
					gt_glbmobject_bk = JSON.parse(JSON.stringify(gt_glbmobject));
					
					for(i=0; i<response.lt_data.length; i++){
						
						gt_glbmobject_valuehelp.push({
							ID			: response.lt_data[i].object,
							description	: response.lt_data[i].description
						});
						
					}
				   
				   fn_bind_display_object_maintenance(gt_glbmobject);
				   busy_diag.close();
				
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
	        },
	        function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
	    );
	}
	
	function fn_bind_display_object_maintenance(data){
		
		var model = new sap.ui.model.json.JSONModel();
			model.setData(data);
		
		//var lo_table = $('#OBJECT_MAINTENANCE_TABLE-listUl');
			//lo_table.floatThead('destroy');	
		
		//ui('OBJECT_MAINTENANCE_TABLE').setModel(model).bindAggregation("items", {
			//path: "/",
			//template: ui('OBJECT_MAINTENANCE_TEMPLATE_TXT')
		//});
		ui('OBJECT_MAINTENANCE_TABLE').setModel(model).bindRows("/");
		setTimeout(function(){fn_freeze_table_header();}, 500);
		
		ui('OBJECT').setTemplate(new sap.m.Text({text : "{object}", textAlign:"Right"}),);
		ui('DESCRIPTION').setTemplate(new sap.m.Text({text : "{description}", textAlign:"Right"}),);
		ui("DELETE_OBJECT").setVisible(false);
		ui("OBJECT_MAINTENANCE_LABEL").setText("Items" + "(" + data.length + ")");
		
		gv_mode = "display";
		
		ui("OBJECT_MAINTENANCE_ADD_BUTTON").setVisible(false);
		ui("OBJECT_MAINTENANCE_SAVE_BUTTON").setVisible(false);
		ui("OBJECT_MAINTENANCE_CANCEL_BUTTON").setVisible(false);
		ui("OBJECT_MAINTENANCE_EDIT_BUTTON").setVisible(true);		
	}
	
	function fn_bind_object_maintenance_inp(data){
		
		var model = new sap.ui.model.json.JSONModel();
			model.setData(data);
		
		//var lo_table = $('#OBJECT_MAINTENANCE_TABLE-listUl');
			//lo_table.floatThead('destroy');
		
		//ui('OBJECT_MAINTENANCE_TABLE').setModel(model).bindAggregation("items", {
			//path: "/",
		//	template: ui('OBJECT_MAINTENANCE_TEMPLATE_INP')
		//});
		ui('OBJECT_MAINTENANCE_TABLE').setModel(model).bindRows("/");
		setTimeout(function(){fn_freeze_table_header();}, 500);
		
		//ui("OBJECT_MAINTENANCE_TABLE").setMode("Delete");
		
		ui('OBJECT').setTemplate(new sap.m.Input({value : "{object}", textAlign:"Right"}),);
		ui('DESCRIPTION').setTemplate(new sap.m.Input({value : "{description}", textAlign:"Right"}),);
		ui("DELETE_OBJECT").setVisible(true);
		
		
		
		ui("OBJECT_MAINTENANCE_LABEL").setText("Items" + "(" + data.length + ")");
		
		gv_mode = "edit";
	
		ui("OBJECT_MAINTENANCE_ADD_BUTTON").setVisible(true);
		ui("OBJECT_MAINTENANCE_SAVE_BUTTON").setVisible(true);
		ui("OBJECT_MAINTENANCE_CANCEL_BUTTON").setVisible(true);
		ui("OBJECT_MAINTENANCE_EDIT_BUTTON").setVisible(false);	
	}

	function fn_save_object_maintenance(){
		
		var busy_diag = fn_show_busy_dialog("");
			busy_diag.open();
		
		var lt_items_insert = [];
		var lt_items_update = [];
		
		for(var i=0; i<gt_glbmobject.length; i++){
			
			var lv_insert = true;
			var lv_update = false;
			
			if(gt_glbmobject[i].object.trim() != ""){
				
				for(var x=0; x < gt_glbmobject_bk.length; x++){
				
					if(gt_glbmobject_bk[x].object === gt_glbmobject[i].object){
					
						if(gt_glbmobject_bk[x].description !== gt_glbmobject[i].description){
							lv_update = true;
						}
						
						lv_insert = false;
						break;
					}
					
				}
				
				if(lv_insert == true){
					lt_items_insert.push({
						OBJECT		: gt_glbmobject[i].object.trim().toUpperCase(),
						DESCRIPTION	: gt_glbmobject[i].description,
					})
				}else if(lv_update == true){
					lt_items_update.push({
						OBJECT		: gt_glbmobject[i].object.trim().toUpperCase(),
						DESCRIPTION	: gt_glbmobject[i].description,
					})
				}
			}
			
			
			
		}
		
		var lt_data = {
			items_insert	: lt_items_insert,
			items_update	: lt_items_update,
			deleted_items	: gt_glbmobject_deleted
		};
		
		fn_ajax_call(
	        "/admin/users/management_v2/save_glbmobject",
	        "POST",
	        lt_data,
	        function(response){
	            
	            if(response.status == "01"){

	            	fn_get_glbmobject();
					fn_show_notification_message("Object was successfully updated.");
					busy_diag.close();
					
	            }else if(response.status == "02"){
					fn_show_notification_message(response.message);
					busy_diag.close();
				}else{
	            	fn_show_notification_message("Object was NOT successfully updated.");
					busy_diag.close();
	            }
	        },
			function(XHR, textStatus, errorThrown){   
				var errorText = JSON.parse(XHR.responseText);
				console.log(errorText); 
				busy_diag.close();
			}
	    );
	}

	
//=======================
// VALUEHELP
//=======================	

	function fn_get_object_valuehelp(lv_open){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_glbmobject_valuehelp = [];
			
		var lt_array = {
			APP_ID	: SelectedAppID,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbmobject",
			"GET",
			lt_array,
			function(response){
			
				if (response.return.status=="01") {
					
					for(var i=0; i<response.lt_data.length; i++){
						gt_glbmobject_valuehelp.push({
							ID			: response.lt_data[i].object,
							description	: response.lt_data[i].description
						});
					}
					
					if(lv_open){
						fn_bind_object_valuehelp(gt_glbmrole_valuehelp,true);
					}else{
						fn_bind_object_valuehelp(gt_glbmrole_valuehelp,false);
					}
					
					busy_diag.close();
					
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}
	
	function fn_bind_object_valuehelp(data,lv_open){
		
		ui('SELECT_DIALOG_ITEMS').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_template_list_valuehelp = new sap.m.StandardListItem({
			type:"Active",
			title:"{ID}",
			description:"{description}"
		
		});
		
		ui('SELECT_DIALOG_ITEMS').setModel(model).bindAggregation("items", {
			path: "/",
			template: lo_template_list_valuehelp
		});
		
		if(lv_open){
			ui('SELECT_DIALOG_ITEMS').setTitle("Select Object");
			ui("SELECT_DIALOG_ITEMS").open();
		}
	}
	
	function fn_get_fieldname_valuehelp(lv_open){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_glbmfieldname_valuehelp = [];
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbmfieldname_valuehelp",
			"GET",
			{},
			function(response){
			
				if (response.return.status=="01") {
					
					gt_glbmfieldname_valuehelp = response.lt_data;
					
					if(lv_open){
						fn_bind_fieldname_valuehelp(gt_glbmfieldname_valuehelp,true);
					}else{
						fn_bind_fieldname_valuehelp(gt_glbmfieldname_valuehelp,false);
					}
					
					busy_diag.close();
				
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}
	
	function fn_bind_fieldname_valuehelp(data,lv_open){
		
		ui('SELECT_DIALOG_ITEMS').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_template_list_valuehelp = new sap.m.StandardListItem({
			type:"Active",
			title:"{ID}",
			description:"{description}"
		
		});
		
		ui('SELECT_DIALOG_ITEMS').setModel(model).bindAggregation("items", {
			path: "/",
			template: lo_template_list_valuehelp
		});
		
		if(lv_open){
			ui('SELECT_DIALOG_ITEMS').setTitle("Select Fieldname");
			ui("SELECT_DIALOG_ITEMS").open();
		}
	}
	
	function fn_get_role_valuehelp(lv_open){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_glbmrole_valuehelp = [];
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbmrole_valuehelp",
			"GET",
			{},
			function(response){
				
				if (response.return.status=="01") {
				
					gt_glbmrole_valuehelp = response.lt_data;
					
					if(lv_open){
						fn_bind_role_valuehelp(gt_glbmrole_valuehelp,true);
					}else{
						fn_bind_role_valuehelp(gt_glbmrole_valuehelp,false);
					}
					
					busy_diag.close();
					
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}
	
	function fn_bind_role_valuehelp(data,lv_open){
		
		ui('SELECT_DIALOG_ITEMS').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_template_list_valuehelp = new sap.m.StandardListItem({
			type:"Active",
			title:"{ID}",
			description:"{description}"
		
		});
		
		ui('SELECT_DIALOG_ITEMS').setModel(model).bindAggregation("items", {
			path: "/",
			template: lo_template_list_valuehelp
		});
		
		ui('SELECT_DIALOG_ITEMS').setTitle("Select Role");
		
		if(lv_open){
			ui("SELECT_DIALOG_ITEMS").open();
		}
		
	}
	
	function fn_get_param_valuehelp(lv_open){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_glbmparamtxt_valuehelp = [];
		
		fn_ajax_call(
			"/admin/users/management_v2/get_glbmparamtxt_valuehelp",
			"GET",
			{},
			function(response){
			
				if (response.return.status=="01") {
					
					gt_glbmparamtxt_valuehelp = response.lt_data;
					
					if(lv_open){
						fn_bind_param_valuehelp(gt_glbmparamtxt_valuehelp,true);
					}else{
						fn_bind_param_valuehelp(gt_glbmparamtxt_valuehelp,false);
					}
					
					busy_diag.close();
				
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}
	
	function fn_bind_param_valuehelp(data,lv_open){
		
		ui('SELECT_DIALOG_ITEMS').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_template_list_valuehelp = new sap.m.StandardListItem({
			type:"Active",
			title:"{ID}",
			description:"{description}"
		
		});
		
		ui('SELECT_DIALOG_ITEMS').setModel(model).bindAggregation("items", {
			path: "/",
			template: lo_template_list_valuehelp
		});
		
		ui('SELECT_DIALOG_ITEMS').setTitle("Select Parameter");
		
		if(lv_open){
			ui("SELECT_DIALOG_ITEMS").open();
		}
		
	}

	function fn_get_partner_no_vhelp(bp_type,lv_open){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_glbmparamtxt_valuehelp = [];
		
		var lv_data = {

			bp_type : bp_type
		}

		fn_ajax_call(
			"/admin/users/management_v2/get_partner_no_valuehelp",
			"GET",
			lv_data,
			function(response){
			
				if (response.return.status=="01"){
					
					gt_partner_no_vhelp = response.lt_data;
					gt_partner_no_vhelp.BP_TYPE = bp_type;

					if(lv_open){
						fn_bind_partner_no_vhelp(response.lt_data,true);
					}else{
						// fn_bind_partner_no_vhelp(response.lt_data,false);
						go_check_existing_value.data = gt_partner_no_vhelp;
						go_check_existing_value.checkData();
					}
					
					busy_diag.close();
				
				}else {			
					fn_show_notification_message("Failed to get data.");
					busy_diag.close();
				}
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}

	function fn_bind_partner_no_vhelp(data,lv_open){
		
		ui('SELECT_DIALOG_ITEMS').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_template_list_valuehelp = new sap.m.StandardListItem({
			type:"Active",
			title:"{ID}",
			description:"{description}"
		
		});
		
		ui('SELECT_DIALOG_ITEMS').setModel(model).bindAggregation("items", {
			path: "/",
			template: lo_template_list_valuehelp
		});
		
		ui('SELECT_DIALOG_ITEMS').setTitle("Select Partner No");
		
		if(lv_open){
			ui("SELECT_DIALOG_ITEMS").open();
		}
		
	}
	
/*
// ================================================================================
// Function to GET MOBILE DATA
// ================================================================================
*/	
	function fn_get_mobile_maintenance(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_mobile_maintenance = [];
		
		fn_ajax_call(
			"/admin/users/management_v2/get_mobile_maintenance",
			"GET",
			{},
			function(response){
				
				if(response.return.status == "01" || response.return.status == "02"){
					
					gt_mobile_maintenance = response.lt_data;
					
					fn_bind_moblie_maint_header(gt_mobile_maintenance,"","");
					busy_diag.close();
					
				}else{
					busy_diag.close();
				}

			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}

	//BIND MOBILE MAINTAINANCE HEADER
	function fn_bind_moblie_maint_header(data,type,oFilter){
		
		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);
		
		if(type!="SEARCH"){
			
			//var lo_table = $('#GO_TABLE_MOBILE_MAINTENANCE-listUl');
				//lo_table.floatThead('destroy');
			
			//sap.ui.getCore().byId("GO_TABLE_MOBILE_MAINTENANCE").setModel(lo_model).bindAggregation("items", {
				//path: "/",
				//template: ui("GO_TABLE_MOBILE_MAINTENANCE_TEMP"),					
			//}); 
			ui('GO_TABLE_MOBILE_MAINTENANCE').setModel(lo_model).bindRows("/");
			sap.ui.getCore().byId("GO_TABLE_MOBILE_MAINTENANCE_LABEL").setText("Mobile Registration (" + data.length + ")");
			go_App_Right.to("PAGE_MOBILE_MAINTENANCE_RIGHT");
			
			setTimeout(function(){fn_freeze_table_header();}, 500);
		 
		}else{
			
			//var lo_table = $('#GO_TABLE_MOBILE_MAINTENANCE-listUl');
				//lo_table.floatThead('destroy');
			
			//sap.ui.getCore().byId("GO_TABLE_MOBILE_MAINTENANCE").bindAggregation("items", {
				//path: "/",
				//template: ui("GO_TABLE_MOBILE_MAINTENANCE_TEMP"),	
				//filters: oFilter
			//}); 
			ui('GO_TABLE_MOBILE_MAINTENANCE').setModel(lo_model).bindRows("/");
			sap.ui.getCore().byId("GO_TABLE_MOBILE_MAINTENANCE_LABEL").setText("Mobile Registration (" + data.length + ")");
			setTimeout(function(){fn_freeze_table_header();}, 500);

		}
	}
	
/*
// ================================================================================
// Function to RELEASED ID 
// ================================================================================
*/	
	
	function fn_released_id(lv_selected_id){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var record = {
			APP_ID		: SelectedAppID,
			USER_ID 	: lv_selected_id,
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/delete_user_epmtdevreg",
			"POST",
			record,
			function(result){
				if(result.status == "01"){
					
					fn_get_mobile_maintenance();
					fn_show_notification_message("Successfully released id.");
					busy_diag.close();
					
				}else{
					
					fn_show_notification_message("Failed to released id.");
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
		
	}

	function fn_get_invite_conf_valuehelp(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		fn_ajax_call(
			"/admin/users/management_v2/get_invite_config_valuehelp",
			"GET",
			{},
			function(result){
				if(result.msg.status == "01"){
					
					gv_invite_conf_vhelp = result.invite_conf_vhelp;
					gv_invite_role_vhelp = result.invite_role_vhelp;
					gv_roles_vhelp = result.roles_vhelp;
					gv_mail_obj_type_vhelp = result.mail_obj_type;
					gv_mail_obj_id_vhelp = result.mail_obj_id;
					gv_mail_event_type_vhelp = result.mail_event_type;
					busy_diag.close();	
				}
				else{
					
					fn_show_notification_message("Failed to retrived");
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.message);	
				busy_diag.close();
			}
		);
	}

	function fn_bind_invite_conf_valuehelp(){

		// var lv_status_field = ui("GO_TABLE_INVITE_CONF").getColumns()[2].getTemplate().getItems()[0];
		// lv_status_field.destroyItems();
		// for(var z=0;z<gv_invite_conf_vhelp.length;z++){
		// 	var lv_vhelp = new sap.ui.core.Item({
				
		// 		key : gv_invite_conf_vhelp[z].ID,
		// 		text : gv_invite_conf_vhelp[z].description
		// 	});
		// 	lv_status_field.addItem(lv_vhelp);
		// }

		// var lv_obj_type_field = ui("GO_TABLE_INVITE_CONF").getColumns()[6].getTemplate().getItems()[0];
		// lv_obj_type_field.destroyItems();
		// lv_obj_type_field.setForceSelection(false);
		// for(var a=0;a<gv_mail_obj_type_vhelp.length;a++){
		// 	var lv_vhelp = new sap.ui.core.Item({
				
		// 		key : gv_mail_obj_type_vhelp[a],
		// 		text : gv_mail_obj_type_vhelp[a]
		// 	});
		// 	lv_obj_type_field.addItem(lv_vhelp);
		// }

		var table_row = ui("GO_TABLE_INVITE_CONF").getRows();
		for(var i=0;i<table_row.length;i++){

			var lv_status_field = table_row[i].getCells()[2].getItems()[0];
			lv_status_field.destroyItems();
			for(var z=0;z<gv_invite_conf_vhelp.length;z++){
				var lv_vhelp = new sap.ui.core.Item({
					
					key : gv_invite_conf_vhelp[z].ID,
					text : gv_invite_conf_vhelp[z].description
				});
				lv_status_field.addItem(lv_vhelp);
			}

			var lv_obj_type_field = table_row[i].getCells()[5].getItems()[0];
			lv_obj_type_field.destroyItems();
			lv_obj_type_field.setForceSelection(false);
			for(var a=0;a<gv_mail_obj_type_vhelp.length;a++){
				var lv_vhelp = new sap.ui.core.Item({
					
					key : gv_mail_obj_type_vhelp[a],
					text : gv_mail_obj_type_vhelp[a]
				});
				lv_obj_type_field.addItem(lv_vhelp);
			}

			var lv_selected_obj_type = table_row[i].getCells()[5].getItems()[0].getSelectedKey();
			var lv_mail_obj_id_field = table_row[i].getCells()[6].getItems()[0];
			lv_mail_obj_id_field.destroyItems();
			lv_mail_obj_id_field.setForceSelection(false);
			if(gv_mail_obj_id_vhelp != ""){
				if(gv_mail_obj_id_vhelp[lv_selected_obj_type] != undefined){
					for(var x=0;x<gv_mail_obj_id_vhelp[lv_selected_obj_type].length;x++){
						var lv_vhelp = new sap.ui.core.Item({
							
							key : gv_mail_obj_id_vhelp[lv_selected_obj_type][x].ID,
							text : gv_mail_obj_id_vhelp[lv_selected_obj_type][x].ID
						});
						lv_mail_obj_id_field.addItem(lv_vhelp);
					}
				}
			}

			var lv_selected_obj_id = table_row[i].getCells()[6].getItems()[0].getSelectedKey();
			var lv_mail_event_type_field = table_row[i].getCells()[7].getItems()[0];
			lv_mail_event_type_field.destroyItems();
			lv_mail_event_type_field.setForceSelection(false);
			if(gv_mail_event_type_vhelp != ""){
				if(gv_mail_event_type_vhelp[lv_selected_obj_id] != undefined){
					for(var y=0;y<gv_mail_event_type_vhelp[lv_selected_obj_id].length;y++){
						var lv_vhelp = new sap.ui.core.Item({
							
							key : gv_mail_event_type_vhelp[lv_selected_obj_id][y].ID,
							text : gv_mail_event_type_vhelp[lv_selected_obj_id][y].ID
						});
						lv_mail_event_type_field.addItem(lv_vhelp);
					}
				}
			}
		}
	}

	function fn_get_invite_conf(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		fn_ajax_call(
			"/admin/users/management_v2/get_invite_config",
			"GET",
			{},
			function(result){
				if(result.msg.status == "01"){
					
					gt_invite_conf = [];
					gt_invite_roles = [];
					gt_invite_conf = result.invite_conf;
					gt_invite_roles = result.invite_roles;
					// gt_invite_conf_bk = JSON.parse(JSON.stringify(gt_invite_conf));
					// gt_invite_roles_bk = JSON.parse(JSON.stringify(gt_invite_roles));
					fn_generate_backup_data();
					// fn_show_notification_message(result.msg.message);
					fn_set_invite_conf_data_mode(result.invite_conf,"display",function(data){
						fn_bind_invite_conf(data);
					});
					setTimeout(function(){
						fn_bind_invite_conf_valuehelp();
					},500);
					gv_mode = "display";
					busy_diag.close();	
				}
				else{
					
					// fn_show_notification_message("");
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.message);	
				busy_diag.close();
			}
		);
	}

	function fn_save_invite_conf(data){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		var lv_data = {

			invite_conf_create : data.invite_conf_create,
			invite_conf_update : data.invite_conf_update,
			invite_roles_create : data.invite_roles_create,
			invite_roles_update : data.invite_roles_update,
		};

		fn_ajax_call(
			"/admin/users/management_v2/save_invite_conf",
			"POST",
			lv_data,
			function(result){
				console.log(result)
				if(result.msg.status == "01"){

					fn_get_invite_conf();
					fn_show_notification_message("Successfully");
					busy_diag.close();
					
				}else{
					
					fn_show_notification_message("Retrived failed.");
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}

	function fn_get_invite_valuehelp(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		fn_ajax_call(
			"/admin/users/management_v2/get_invite_valuehelp",
			"GET",
			{},
			function(result){
				console.log(result)
				if(result.return.status == "01"){
					
					fn_bind_invite_valuehelp(result);
					// fn_show_notification_message("Successfully");
					busy_diag.close();
					
				}else{
					
					// fn_show_notification_message("Retrived failed.");
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);		
	}

	function fn_bind_invite_valuehelp(data){

		ui("INPUT_INVITE_ID").destroyItems();
		ui("INPUT_INVITE_STATUS").destroyItems();

		// var lv_invite_id = [];
		// var lv_invite_status = [];

		for(var i=0;i<data.invite_conf.length;i++){
			var lv_invite_id = new sap.ui.core.Item({
				key : data.invite_conf[i].ID,
				text : data.invite_conf[i].description
			});
			ui("INPUT_INVITE_ID").addItem(lv_invite_id);
		}

		for(var i=0;i<data.invite_status.length;i++){
			var lv_invite_status = new sap.ui.core.Item({
				key : data.invite_status[i].ID,
				text : data.invite_status[i].description
			});
			ui("INPUT_INVITE_STATUS").addItem(lv_invite_status);
		}
	}

	function fn_get_invite_listing(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		var lv_created_at_from = ui("INPUT_INVITE_CREATED_DATE_FROM").getValue();
		var lv_created_at_to = ui("INPUT_INVITE_CREATED_DATE_TO").getValue();
		var created_at = [];

		if(lv_created_at_from != ""){
			created_at.push(lv_created_at_from);
		}
		if(lv_created_at_to != ""){
			created_at.push(lv_created_at_to);
		}

		var record = {

			invite_id : ui("INPUT_INVITE_ID").getSelectedKeys(),
			created_at : created_at,
			status : ui("INPUT_INVITE_STATUS").getSelectedKeys(),
		}
		
		fn_ajax_call(
			"/admin/users/management_v2/get_invite_listing",
			"GET",
			record,
			function(result){
				console.log(result)
				if(result.return.status == "01"){
					
					fn_bind_invite_listing(result.invite_listing);
					// fn_bind_invite_id(result.invite_conf);
					// fn_show_notification_message("Successfully retrived.");
					busy_diag.close();
					
				}else{
					
					// fn_show_notification_message("Retrived failed.");
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}

	function fn_bind_invite_id(data){

		ui("INVITE_ID").destroyItems();

		var lv_invite_id = [];

		for(var i=0;i<data.length;i++){
			var invites_vhelp = new sap.ui.core.Item({
				key : data[i].ID,
				text : data[i].description
			});
			ui("INVITE_ID").addItem(invites_vhelp);
		}
	}

	function fn_resend_invitation(token,inviteID,email){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		var record = {

			token 	: token,
			ivInvID : inviteID,
			ivEmail : email,
		}

		fn_ajax_call(
			"/admin/api/invites/resent",
			"POST",
			record,
			function(result){
				console.log(result)
				if(result.status == "01"){
					
					fn_show_notification_message(result.message);
					fn_get_invite_listing();
					busy_diag.close();
				}
				else{
					
					fn_show_notification_message(result.message);
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.message);	
				busy_diag.close();
			}
		);
	}

	function fn_deactivate_invitation(token){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		var record = {

			token 	: token
		}

		fn_ajax_call(
			"/admin/api/invites/deactivate",
			"POST",
			record,
			function(result){
				console.log(result)
				if(result.status == "01"){
					
					fn_show_notification_message(result.message);
					fn_get_invite_listing();
					busy_diag.close();
				}
				else{
					
					fn_show_notification_message(result.message);
					busy_diag.close();
				}
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}

	//05-05-2019 start

	function fn_get_function_text () { 
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		gt_function_text = [];
		gt_glbmtstc = [];

		ui('ADD_ACTION_FOR_FUNCTION_TEXT').setVisible(false);
		ui('SAVE_ACTION_FOR_FUNCTION_TEXT').setVisible(false);
		ui('DECLINE_ACTION_FOR_FUNCTION_TEXT').setVisible(false);
		ui('EDIT_ACTION_FOR_FUNCTION_TEXT').setVisible(true);
		
		$.get("{{ url('admin/users/management_v2/get_function_text') }}", function (data) {
			
			fn_bind_data_to_input_fn_txt('default');

			if (data.glbmfunctiontxt.length) {
				gt_function_text = data.glbmfunctiontxt;
				var lo_model = new sap.ui.model.json.JSONModel();
				lo_model.setSizeLimit(gt_function_text.length);
				lo_model.setData(gt_function_text);

				ui('GO_TABLE_FUNCTION_TEXT').setModel(lo_model).bindRows("/");
				ui("GO_TABLE_FUNCTION_TEXT_LABEL").setText("Function Text (" + gt_function_text.length + ")");
				setTimeout(function(){fn_freeze_table_header();}, 500);
				busy_diag.close();
				go_App_Right.to('PAGE_FUNCTION_TEXT_RIGHT');
			}

			if (data.glbmtstc.length) {
				gt_glbmtstc = data.glbmtstc;
			}

				
		}, "json");
		
	}

	function fn_bind_data_to_input_fn_txt (type = 'default') { 

		var lv_function_desc,
			lv_tstc,
			lv_icon,
			lv_sort_ind,
			lv_delete_btn;

		if (type === 'default') {
			lv_function_desc = new sap.m.Text({text:"{FUNCTION_DESC}"});
			lv_tstc = new sap.m.Text({text:"{TSTC}"});
			lv_icon = new sap.m.Button({icon: "{ICON}", width:"100%",textAlign:"Left"});
			lv_sort_ind = new sap.m.Text({text:"{SORT_IND}"});

			ui('fn_txt_ICON').setWidth("50px");
		} else if (type === 'edit') {
			lv_function_desc = new sap.m.Input({value:"{FUNCTION_DESC}"});
			lv_tstc = new sap.m.Input({
				value: "{TSTC}",
				enabled: true,
				showValueHelp: true,
				valueHelpRequest: function (oEvt) {
					gv_confirm_input = oEvt.getSource().getId();
					fn_bind_function_text_valuehelp(gt_glbmtstc, true);
				},
				change: function (oEvt) {
					var lv_value = oEvt.getSource().getValue().trim();
					oEvt.getSource().setValue(lv_value.toUpperCase());
				}
			});
			lv_icon = new sap.m.Input({value: "{ICON}",textAlign: "Left"});
			lv_sort_ind = new sap.m.Input({value:"{SORT_IND}"});


			ui('fn_txt_ICON').setWidth("");
		}

		lv_delete_btn = new sap.m.Button({
			icon: 'sap-icon://delete',
			press : function (oEvt) {
				var lo_index = String(oEvt.getSource().getBindingContext().getPath());
				var lv_index = lo_index.split("/")[1];
				gv_deleted_fn_txt = lv_index;
				ui('DELETE_FUNCTION_TEXT_DIALOG_CONFIRM').open();
			}
		});

		ui('fn_txt_FUNCTION_DESC').setTemplate(lv_function_desc);
		ui('fn_txt_TSTC').setTemplate(lv_tstc);
		ui('fn_txt_ICON').setTemplate(lv_icon);
		ui('fn_txt_SORT').setTemplate(lv_sort_ind);
		ui('fn_txt_Delete').setTemplate(lv_delete_btn);
	}

	function fn_bind_function_text_valuehelp (data, lv_open) { 
		
		ui('SELECT_DIALOG_ITEMS').destroyItems();
		
		var model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(data.length);
			model.setData(data);
		
		var lo_template_list_valuehelp = new sap.m.StandardListItem({
			type:"Active",
			title:"{ID}",
			description:"{description}"
		});
		
		ui('SELECT_DIALOG_ITEMS').setModel(model).bindAggregation("items", {
			path: "/",
			template: lo_template_list_valuehelp
		});
		
		if(lv_open){
			ui('SELECT_DIALOG_ITEMS').setTitle("Select Object");
			ui("SELECT_DIALOG_ITEMS").open();
		}
	}

	function fn_save_update_fn_txt (lv_fn_txt_data) { 

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		var data = {
			_token : "{!! csrf_token() !!}",
			lv_fn_txt_data:lv_fn_txt_data
		};

		var request = $.ajax({
			type:"post",
			data:data,
			url:"{{ url('admin/users/management_v2/save_update_fn_txt') }}",
			dataType:"json",
			async:false,
		});

		request.done(function(data){
			fn_bind_data_to_input_fn_txt('default');
			busy_diag.close();

			ui('ADD_ACTION_FOR_FUNCTION_TEXT').setVisible(false);
			ui('SAVE_ACTION_FOR_FUNCTION_TEXT').setVisible(false);
			ui('DECLINE_ACTION_FOR_FUNCTION_TEXT').setVisible(false);
			ui('EDIT_ACTION_FOR_FUNCTION_TEXT').setVisible(true);

			fn_show_notification_message(data.message);
		});

	}

	function fn_delete_function_txt (lv_function) { 

		var data = {
			_token : "{!! csrf_token() !!}",
			FUNCTION:lv_function
		};

		return $.ajax({
			type:"post",
			data:data,
			url:"{{ url('admin/users/management_v2/delete_function_text') }}",
			dataType:"json",
			async:false,
		});

	}

	function fn_add_data_and_bind(lv_inputs) 
	{
		var lv_data = ui('GO_TABLE_FUNCTION_TEXT').getModel().getData();

		lv_data.push(lv_inputs);

		var model = new sap.ui.model.json.JSONModel();
			model.setData(lv_data);

		ui('GO_TABLE_FUNCTION_TEXT').setModel(model).bindRows("/");
		ui("GO_TABLE_FUNCTION_TEXT_LABEL").setText("Function Text (" + lv_data.length + ")");

		ui('fn_txt_FUNCTION_DESC').setTemplate(new sap.m.Input({value : "{FUNCTION_DESC}", textAlign:"Left"}),);
		ui('fn_txt_TSTC').setTemplate(new sap.m.Input({
			value:"{TSTC}",
			textAlign:"Left",
			showValueHelp: true,
			valueHelpRequest: function (oEvt) {
				gv_confirm_input = oEvt.getSource().getId();
				fn_bind_function_text_valuehelp(gt_glbmtstc, true);
			},
			change: function (oEvt) {
				var lv_value = oEvt.getSource().getValue().trim();
				oEvt.getSource().setValue(lv_value.toUpperCase());
			}
		}));
		ui('fn_txt_ICON').setTemplate(new sap.m.Input({value: "{ICON}",textAlign: "Left"}));
		ui('fn_txt_SORT').setTemplate(new sap.m.Input({value : "{SORT_IND}", textAlign:"Left"}));
	}

	function fn_check_if_function_name_exists(lv_fn_name) 
	{
		var lv_response = false;
		
		var lv_data = {
			_token : "{!! csrf_token() !!}",
			FUNCTION:lv_fn_name
		};

		$.ajax({
			type:"post",
			data:lv_data,
			url:"{{ url('admin/users/management_v2/check_if_function_name_exists') }}",
			dataType:"json",
			async:false,
		}).done(function(data){
			lv_response = data.response;
		});

		return lv_response;
		
	}

	//05-05-2019 end

	//06-07-2019-start
	function fn_get_user_roles()
	{
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		$.ajax({
			type:"GET",
			url: "{{ url('admin/user_roles/get_users_roles_status') }}",
			dataType:"json",
			contentType: "application/json; charset=utf-8",
		}).done(function(data){

			if (data.users.length) {
				gt_userids = data.users.map(function(user){
					return user.USER_ID;
				});
			}

			for(var i = 0; i < data.roles.length; i++) {
				var roles = new sap.ui.core.Item({text:data.roles[i].DESCRIPTION, key:data.roles[i].ROLE});
				ui('INPUT_ROLE').addItem(roles);
			}

			for(var i = 0; i < data.status.length; i++) {
				var status = new sap.ui.core.Item({text:data.status[i].DESCRIPTION, key:data.status[i].STATUS});
				ui('INPUT_STATUS').addItem(status);
			}

			busy_diag.close();

			go_App_Right.to("USER_ROLES_SELECTION_SCREEN");

		});
	}

	var ajaxRequest = function (type = 'GET', data, url, async = false) {

		return $.ajax({
			type:type,
			data:data,
			url:url,
			dataType:"json",
			async:async
		});

	}

	function getFiteredUserRoles (data) {

		return ajaxRequest('POST', data, "{{ url('admin/user_roles/get_user_roles') }}");

	}
	//06-07-2019-end
	
	function fn_validate_account_upload(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
		
		var record = {
			items : gt_upload_users
		}

		fn_ajax_call(
			"/admin/upload_user/validate_account_upload",
			"POST",
			record,
			function(response){
				
				busy_diag.close();
				
				if(response.return.status == "01"){
					setTimeout(function(){ui("WIZARD-nextButton").setText("Confirm Upload");},1);
					ui("WIZARD_STEP_2").setValidated(true);
					fn_bind_account_upload_validation(response);
				}else if(response.return.status == "02"){
					ui("WIZARD_STEP_2").setValidated(false);
					fn_bind_account_upload_validation(response);
				}else{
					ui("MESSAGE_STRIP_UPLOAD_ACCCOUNT").addItem(
						new sap.m.MessageStrip({
							text: "Incorrect file format.",
							type:"Error",
							showIcon: true,
							showCloseButton: true,
							close:function(){
								ui('INPUT_UPLOADER_COMPLEX').setValueState("None")
							}
						})
					);
				}
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}
	
	function fn_bind_account_upload_validation(response){
		
		gt_validate_user = response.validate_user.lt_return;
		gt_validate_auth = response.validate_auth.lt_return;
		gt_validate_param = response.validate_param.lt_return;
		gt_validate_bizpart = response.validate_bizpart.lt_return;
		
		var lo_user_model = new sap.ui.model.json.JSONModel();
			lo_user_model.setSizeLimit(gt_validate_user.length);
			lo_user_model.setData(gt_validate_user);
		
		var lo_auth_model = new sap.ui.model.json.JSONModel();
			lo_auth_model.setSizeLimit(gt_validate_auth.length);
			lo_auth_model.setData(gt_validate_auth);
			
		var lo_param_model = new sap.ui.model.json.JSONModel();
			lo_param_model.setSizeLimit(gt_validate_param.length);
			lo_param_model.setData(gt_validate_param);
			
		var lo_bizpart_model = new sap.ui.model.json.JSONModel();
			lo_bizpart_model.setSizeLimit(gt_validate_bizpart.length);
			lo_bizpart_model.setData(gt_validate_bizpart);
		
		
		ui('TABLE_USER_VALIDATION').setModel(lo_user_model).bindRows("/");
		ui('TABLE_AUTH_VALIDATION').setModel(lo_auth_model).bindRows("/");
		ui('TABLE_PARAM_VALIDATION').setModel(lo_param_model).bindRows("/");
		ui('TABLE_BIZPART_VALIDATION').setModel(lo_bizpart_model).bindRows("/");
		
		ui("LABEL_VALIDATION_USER_COUNT").setText("Users (" + gt_validate_user.length + ")");
		ui("LABEL_VALIDATION_AUTH_COUNT").setText("Authorization Role (" + gt_validate_auth.length + ")");
		ui("LABEL_VALIDATION_PARAM_COUNT").setText("Parameters (" + gt_validate_param.length + ")");
		ui("LABEL_VALIDATION_BIZPART_COUNT").setText("BIZ Parners (" + gt_validate_bizpart.length + ")");
		
	}
	
	function fn_process_account_upload(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var lv_status = ui("INPUT_ACCOUNT_UPLOAD_STATUS").getState();
		var lv_send = ui("INPUT_ACCOUNT_UPLOAD_SEND").getState();
		
		var record = {
			items	: gt_upload_users,
			STATUS	: (lv_status == true) ? "01" : "02",
			SEND	: lv_status,
		}

		fn_ajax_call(
			"/admin/upload_user/process_account_upload",
			"POST",
			record,
			function(response){
				
				if(response.return.status == "01"){
					
					var lt_user_data = response.lt_users.dataitem;
					console.log(lt_user_data);
					
					var model = new sap.ui.model.json.JSONModel();
						model.setSizeLimit(lt_user_data.length);
						model.setData(lt_user_data);
						
					ui('TABLE_ACCOUNT_UPLOAD_USER_SUMMARY').setModel(model).bindRows("/");
					
					setTimeout(function(){ui("WIZARD-nextButton").setText("Done");},1);
					ui("WIZARD_STEP_4").setValidated(true);
					
				}
				
				busy_diag.close();
				
			},
			function(XHR, textStatus, errorThrown){
				var errorText = JSON.parse(XHR.responseText);
				fn_show_notification_message(errorText.notify_message);	
				busy_diag.close();
			}
		);
	}
	
</script>

	
