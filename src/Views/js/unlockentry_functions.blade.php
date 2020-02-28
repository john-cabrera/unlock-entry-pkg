<script type="text/javascript">

	function fn_CHECK_IFREQUIRED_B4UPDATE(){

		ui("MESSAGE_STRIP_ACCT_MAINTENANCE").destroyContent();

		var lv_isValid = true;

		for(var i = 0; i<gt_NEWACCTDSP_INPUT.length; i++){

			if(gt_NEWACCTDSP_INPUT[i].ISREQUIRED == true){

				if(ui(gt_NEWACCTDSP_INPUT[i].INP).getValue().trim()==""){

					lv_isValid = false;
					ui(gt_NEWACCTDSP_INPUT[i].INP).setValueState("Error");
					ui(gt_NEWACCTDSP_INPUT[i].INP).setValueStateText("Field is required");

					if(gt_NEWACCTDSP_INPUT[i].ID === "USERID"){
						var lv_message = fn_show_message_strip("User ID is required");
						ui('MESSAGE_STRIP_ACCT_MAINTENANCE').addContent(lv_message);
						ui(gt_NEWACCTDSP_INPUT[i].INP).focus();
					}else if(gt_NEWACCTDSP_INPUT[i].ID === "EMAIL"){
						var lv_message = fn_show_message_strip("Email is required.");
						ui('MESSAGE_STRIP_ACCT_MAINTENANCE').addContent(lv_message);
						ui(gt_NEWACCTDSP_INPUT[i].INP).focus();
					}

					break;

				}
			}
		}

		if(lv_isValid == true){
			if(ui('ACCT_MNT_DSP_INPT_EMAIL').getValueState() !== "Error"){
				lv_isValid = true;
			}else{
				lv_isValid = false;
				ui("SECTION_ACCT_MNT_DSP_ACCTDETAILS").getCustomAnchorBarButton().firePress();
				ui('ACCT_MNT_DSP_INPT_EMAIL').focus();
				var lv_message = fn_show_message_strip(ui('ACCT_MNT_DSP_INPT_EMAIL').getValueStateText());
				ui('MESSAGE_STRIP_ACCT_MAINTENANCE').addContent(lv_message);
			}
		}

		if(lv_isValid == true){

			var lv_model = ui("GO_TABLE_CREATE_PARTNER").getModel();
			var lv_data = lv_model.getData();
			var lv_count_error = 0;

			for(var i=0;i<lv_data.length;i++){

				if(lv_data[i].BP_TYPE == ""){
					lv_count_error++;
					lv_data[i].BP_TYPE_VALUESTATE = "Error";
				}
				if(lv_data[i].PARTNER_NO == ""){
					lv_data[i].PARTNER_NO_VALUESTATE = "Error";
					lv_count_error++;
				}

				if(lv_count_error > 0){

					lv_isValid = false;
					var lv_message = fn_show_message_strip("Please fill in required fields");
					ui('MESSAGE_STRIP_ACCT_MAINTENANCE').addContent(lv_message);
					ui('MESSAGE_STRIP_ACCT_MAINTENANCE').setVisible(true);
					lv_model.refresh();
					break;
				}

				if(lv_data[i].PARTNER_NO_VALUESTATE == "Error"){

					lv_isValid = false;
					go_check_existing_value.value1 = lv_data[i].BP_TYPE;
					go_check_existing_value.value2 = lv_data[i].PARTNER_NO;
					go_check_existing_value.data = gt_partner_no_vhelp;
					go_check_existing_value.array = lv_data[i];
					go_check_existing_value.getValueHelp();
					lv_model.refresh();
					break;
				}
			}
		}

		return lv_isValid;
	}

	function fn_BIND_ACCT_DETAIL_DISPLAY(userID){

		var lt_Acct_Detail 	= fn_GET_ACCOUNT_DETAIL(userID);
		var lv_userphoto = (lt_Acct_Detail.guid) ? "/admin/download/file/"+ lt_Acct_Detail.guid : userphoto_path;

		if(lt_Acct_Detail!=false){

			ui("OBJECTHEADER_ACCT_MNT_DSP").setObjectTitle(lt_Acct_Detail.user_id);
			ui("OBJECTHEADER_ACCT_MNT_DSP").setObjectImageURI(lv_userphoto);

			setTimeout(function() {
				$('.sapUxAPObjectPageHeaderObjectImage').click(function(){
					var lv_url = ui("ACCT_MNT_DISPLAY_PHOTO").getText();
					ui("SHOW_USER_PHOTO").setSrc(lv_url);
					ui("DIALOG_SHOW_PHOTO").open();
				});
			}, 500)

			ui("ACCT_MNT_DSP_TEXT_ID").setText(lt_Acct_Detail.id);

			ui("ACCT_MNT_DSP_INPT_USERID").setValue(lt_Acct_Detail.user_id);
			ui("ACCT_MNT_DSP_TEXT_USERID").setText(lt_Acct_Detail.user_id);

			ui("ACCT_MNT_DSP_INPT_DSPNAME").setValue(lt_Acct_Detail.display_name);
			ui("ACCT_MNT_DSP_TEXT_DSPNAME").setText(lt_Acct_Detail.display_name);

			ui("ACCT_MNT_DSP_INPT_FNAME").setValue(lt_Acct_Detail.firstname);
			ui("ACCT_MNT_DSP_TEXT_FNAME").setText(lt_Acct_Detail.firstname);

			ui("ACCT_MNT_DSP_INPT_LNAME").setValue(lt_Acct_Detail.lastname);
			ui("ACCT_MNT_DSP_TEXT_LNAME").setText(lt_Acct_Detail.lastname);

			ui("ACCT_MNT_DSP_INPT_EMAIL").setValue(lt_Acct_Detail.email);
			ui("ACCT_MNT_DSP_TEXT_EMAIL").setText(lt_Acct_Detail.email);

			ui("ACCT_MNT_DISPLAY_PHOTO").setText(lv_userphoto);


			if(lt_Acct_Detail.status === "01"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(lt_Acct_Detail.status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);

			}else if(lt_Acct_Detail.status === "02"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(lt_Acct_Detail.status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);

			}else if(lt_Acct_Detail.status === "03"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(lt_Acct_Detail.status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(false);


			}else if(lt_Acct_Detail.status === "04"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(lt_Acct_Detail.status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);

			}

			var lv_del_flag = lt_Acct_Detail.del_flag;
			if(lv_del_flag === "X"){
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(true);
			}else{
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(false);
			}

			ui("ACCT_MNT_DSP_TEXT_CREATED_BY").setText(lt_Acct_Detail.created_by);
			ui("ACCT_MNT_DSP_TEXT_CREATED_AT").setText(lt_Acct_Detail.created_at);


			//fn_SET_DISPLAY_MODE("ACCT_MTN_READ");
			fn_set_to_display_account_details();

		}
	}

	function fn_BIND_USER_INFO(data){

		ui("MESSAGE_STRIP_ACCT_MAINTENANCE").destroyContent();
		var lv_userphoto = (data[0].guid) ? "/admin/download/file/"+ data[0].guid : userphoto_path;

		if(data.length > 0){

			ui("OBJECTHEADER_ACCT_MNT_DSP").setObjectTitle(data[0].user_id);
			ui("OBJECTHEADER_ACCT_MNT_DSP").setObjectImageURI(lv_userphoto);

			ui("ACCT_MNT_DSP_TEXT_ID").setText(data[0].id);

			ui("ACCT_MNT_DSP_INPT_USERID").setValue(data[0].user_id);
			ui("ACCT_MNT_DSP_TEXT_USERID").setText(data[0].user_id);

			ui("ACCT_MNT_DSP_INPT_DSPNAME").setValue(data[0].display_name);
			ui("ACCT_MNT_DSP_TEXT_DSPNAME").setText(data[0].display_name);

			ui("ACCT_MNT_DSP_INPT_FNAME").setValue(data[0].firstname);
			ui("ACCT_MNT_DSP_TEXT_FNAME").setText(data[0].firstname);

			ui("ACCT_MNT_DSP_INPT_LNAME").setValue(data[0].lastname);
			ui("ACCT_MNT_DSP_TEXT_LNAME").setText(data[0].lastname);

			ui("ACCT_MNT_DSP_INPT_EMAIL").setValue(data[0].email);
			ui("ACCT_MNT_DSP_TEXT_EMAIL").setText(data[0].email);

			ui("ACCT_MNT_DSP_SELECT_MOBILE_NO").setSelectedKey(data[0].icc_code).setValue(data[0].icc_code);
			ui("ACCT_MNT_DSP_INPT_MOBILE_NO").setValue(data[0].input_mobile_no);
			ui("ACCT_MNT_DSP_TEXT_MOBILE_NO").setText(data[0].mobile_no);

			ui("ACCT_MNT_DISPLAY_PHOTO").setText(lv_userphoto);


			if(data[0].status === "01"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(data[0].status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);

			}else if(data[0].status === "02"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(data[0].status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);

			}else if(data[0].status === "03"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(data[0].status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(false);


			}else if(data[0].status === "04"){

				ui("ACCT_MNT_DSP_TEXT_STATUS").setText(data[0].status_desc);
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);

			}

			var lv_del_flag = data[0].del_flag;
			if(lv_del_flag === "X"){
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(true);
			}else{
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(false);
			}

			ui("ACCT_MNT_DSP_TEXT_CREATED_BY").setText(data[0].created_by);
			ui("ACCT_MNT_DSP_TEXT_CREATED_AT").setText(data[0].created_at);


			//fn_SET_DISPLAY_MODE("ACCT_MTN_READ");
			fn_set_to_display_account_details();

		}
	}


	function fn_GET_ACCOUNT_DETAIL(userID){
		var lv_userdata = [];
			if(typeof(gt_USER_DATA_INDEX[userID]) != "undefined"){
				lv_userdata = gt_USER_DATA_INDEX[userID];
			}else{
				lv_userdata = false;
			}

		return lv_userdata;
	}

	function fn_CLEAR_NEWACCT_INPUTFIELD(){

		for(var i=0; i<gt_NEWACCT_INPUT.length; i++){
			ui(gt_NEWACCT_INPUT[i].INP).setValue("");

		}
	}

	function fn_SET_DISPLAY_MODE(mode,lt_Acct_Detail){

		switch(mode){

			case "ROLEOBJV_READ":

				gv_mode = "display";

				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ").setVisible(true);
				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ_SAVE").setVisible(false);
				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ_CANCEL").setVisible(false);
				ui("ROLEOBJV_MNT_RIGHT_BTN_ADDOBJV").setVisible(false);

				//ui('ROLEOBJV_MNT_TABLE').setMode("None");
				fn_BIND_ROLEOBJV(gt_ROLEOBJV,"TEXT");

			break;

			case "ROLEOBJV_READ_AFTERUPDATE":

				gv_mode = "display";

				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ").setVisible(true);
				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ_SAVE").setVisible(false);
				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ_CANCEL").setVisible(false);
				ui("ROLEOBJV_MNT_RIGHT_BTN_ADDOBJV").setVisible(false);

				//ui('ROLEOBJV_MNT_TABLE').setMode("None");
				fn_BIND_ROLEOBJV(gt_ROLEOBJV,"TEXT");

			break;

			case "ROLEOBJV_UPDATE":

				gv_mode = "edit";

				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ").setVisible(false);
				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ_SAVE").setVisible(true);
				ui("ROLEOBJV_MNT_RIGHT_BTN_EDITOBJ_CANCEL").setVisible(true);
				ui("ROLEOBJV_MNT_RIGHT_BTN_ADDOBJV").setVisible(true);

				//ui('ROLEOBJV_MNT_TABLE').setMode("Delete");
				fn_BIND_ROLEOBJV(gt_ROLEOBJV,"INPUT");
				fn_get_object_valuehelp(false);
				fn_get_fieldname_valuehelp(false);

			break;

			case "CLIENT_ADMIN_READ":

				gv_mode = "display";

				fn_clear_client_admin();

				ui("CLIENT_ADMIN_ADD_BTN").setVisible(false);
				ui("CLIENT_ADMIN_SAVE_BTN").setVisible(false);
				ui("CLIENT_ADMIN_CANCEL_BTN").setVisible(false);
				ui("CLIENT_ADMIN_EDIT_BTN").setVisible(true);

			break;

			case "CLIENT_ADMIN_EDIT":

				gv_mode = "edit";

				fn_clear_client_admin();

				//show save icon //enable value field .
				ui("CLIENT_ADMIN_ADD_BTN").setVisible(true);
				ui("CLIENT_ADMIN_SAVE_BTN").setVisible(true);
				ui("CLIENT_ADMIN_CANCEL_BTN").setVisible(true);
				ui("CLIENT_ADMIN_EDIT_BTN").setVisible(false);

				fn_get_client_admin_valuehelp(gv_selected_roleid);
				fn_BIND_CLIENT_ADMIN_RIGHT(gt_CLNT_CONF_RIGHT,UPDATE) ;

			break;
		}

	}

	function fn_clear_client_admin(){

		//clear fields
		ui("DIALOG_CLNT_CONF_FUNCTION").setValue("");
		ui("DIALOG_CLNT_CONF_FUNCTION").setValueState("None");
		ui("DIALOG_CLNT_CONF_FUNCTION").setValueStateText("");

		ui("DIALOG_CLNT_CONF_PARAMETER_EXISTING").setSelected(true);
		ui("DIALOG_CLNT_CONF_PARAMETER_NEW").setSelected(false);

		ui("DIALOG_CLNT_CONF_PARAMETER_ID").setValue("");
		ui("DIALOG_CLNT_CONF_PARAMETER_ID").setValueState("None");
		ui("DIALOG_CLNT_CONF_PARAMETER_ID").setValueStateText("");
		ui("DIALOG_CLNT_CONF_PARAMETER_ID").setShowValueHelp(true);

		ui("DIALOG_CLNT_CONF_PARAMETER_DESC").setValue("");
		ui("DIALOG_CLNT_CONF_PARAMETER_DESC").setEditable(false);

		ui("DIALOG_CLNT_CONF_VALUE").setValue("");

	}

	function fn_set_to_display_account_details(){

		gv_mode = "display";

		ui("ACCT_MNT_LABEL").setText("Display Account");

		//account details
		ui("ACCT_MNT_DSP_INPT_USERID").setVisible(false);
		ui("ACCT_MNT_DSP_TEXT_USERID").setVisible(true);

		ui("ACCT_MNT_DSP_INPT_FNAME").setVisible(false);
		ui("ACCT_MNT_DSP_TEXT_FNAME").setVisible(true);

		ui("ACCT_MNT_DSP_INPT_LNAME").setVisible(false);
		ui("ACCT_MNT_DSP_TEXT_LNAME").setVisible(true);

		ui("ACCT_MNT_DSP_INPT_DSPNAME").setVisible(false);
		ui("ACCT_MNT_DSP_TEXT_DSPNAME").setVisible(true);

		ui("ACCT_MNT_DSP_INPT_EMAIL").setVisible(false);
		ui("ACCT_MNT_DSP_INPT_EMAIL").setValueState("None");
		ui("ACCT_MNT_DSP_INPT_EMAIL").setValueStateText("");
		ui("ACCT_MNT_DSP_TEXT_EMAIL").setVisible(true);

		ui("ACCT_MNT_DSP_SELECT_MOBILE_NO").setVisible(false);
		ui("ACCT_MNT_DSP_INPT_MOBILE_NO").setVisible(false);
		ui("ACCT_MNT_DSP_TEXT_MOBILE_NO").setVisible(true);

		//ui("ACCT_MNT_DISPLAY_PHOTO").setVisible(true);
		ui("ACCT_MNT_PHOTO_UPLOADER").setVisible(false);
		ui("ACCT_MNT_PHOTO_UPLOADER-fu_input").setValue("");

		ui("ACCT_MNT_PHOTO_UPLOADER_LABEL").setVisible(false);
		ui("ACCT_MNT_PHOTO_UPLOADER_LABEL").setText("Photo");

		ui("ACCT_MNT_DSP_AD_BTN_EDIT_SAVE").setVisible(false);
		ui("ACCT_MNT_DSP_AD_BTN_EDIT_CANCEL").setVisible(false);


		var lv_status = ui("ACCT_MNT_DSP_TEXT_STATUS").getText();

		switch(lv_status){
			case "Active" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
			break;

			case "Inactive" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
			break;

			case "Deleted" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(false);
			break;

			case "Locked" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
			break;

			default:
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
		}

		ui("ACCT_MNT_DSP_AD_BTN_EDIT").setVisible(true);
		ui("ACCT_MNT_DSP_AD_BTN_SETTINGS").setVisible(true);
		ui("ACCT_MNT_DSP_AD_BTN_PASSWORD").setVisible(true);

		//account preferences
		ui("ACCT_MNT_DSP_AP_BTN_EDIT_ADD").setVisible(false);
		//ui('ACCT_MNT_DSP_TABLE_ACCTPREF').setMode("None");
		fn_BIND_USER_PARAM(gt_user_param,"TEXT");

		//account authorization
		ui("ACCT_MNT_DSP_AA_BTN_EDIT_ADD").setVisible(false);
		//ui('ACCT_MNT_DSP_TABLE_ACCTAUTH').setMode("None");
		fn_BIND_USER_ROLE(gt_user_role,"TEXT");

	}

	function fn_set_to_edit_account_details(){

		gv_mode = "edit";

		ui("ACCT_MNT_LABEL").setText("Edit Account");

		//account details
		ui("ACCT_MNT_DSP_INPT_USERID").setVisible(true);
		ui("ACCT_MNT_DSP_TEXT_USERID").setVisible(false);

		ui("ACCT_MNT_DSP_INPT_FNAME").setVisible(true);
		ui("ACCT_MNT_DSP_TEXT_FNAME").setVisible(false);

		ui("ACCT_MNT_DSP_INPT_LNAME").setVisible(true);
		ui("ACCT_MNT_DSP_TEXT_LNAME").setVisible(false);

		ui("ACCT_MNT_DSP_INPT_DSPNAME").setVisible(true);
		ui("ACCT_MNT_DSP_TEXT_DSPNAME").setVisible(false);

		ui("ACCT_MNT_DSP_INPT_EMAIL").setVisible(true);
		ui("ACCT_MNT_DSP_TEXT_EMAIL").setVisible(false);

		ui("ACCT_MNT_DSP_SELECT_MOBILE_NO").setVisible(true);
		ui("ACCT_MNT_DSP_INPT_MOBILE_NO").setVisible(true);
		ui("ACCT_MNT_DSP_TEXT_MOBILE_NO").setVisible(false);

		//ui("ACCT_MNT_DISPLAY_PHOTO").setVisible(false);
		ui("ACCT_MNT_PHOTO_UPLOADER").setVisible(true);
		ui("ACCT_MNT_PHOTO_UPLOADER-fu_input").setValue("");

		ui("ACCT_MNT_PHOTO_UPLOADER_LABEL").setVisible(true);
		ui("ACCT_MNT_PHOTO_UPLOADER_LABEL").setText("Photo");

		ui("ACCT_MNT_DSP_AD_BTN_EDIT_SAVE").setVisible(true);
		ui("ACCT_MNT_DSP_AD_BTN_EDIT_CANCEL").setVisible(true);

		var lv_status = ui("ACCT_MNT_DSP_TEXT_STATUS").getText();

		switch(lv_status){
			case "Active" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(false);
			break;

			case "Inactive" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(false);
			break;

			case "Deleted" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(false);
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(true);
			break;

			case "Locked" :
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(false);
			break;

			default:
				ui("ACCT_MNT_DSP_AD_BTN_LOCKEDUSER").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_ACTIVATE").setVisible(false);
				ui("ACCT_MNT_DSP_AD_BTN_INACTIVATE").setVisible(true);
				ui("ACCT_MNT_DSP_AD_BTN_DELETE").setVisible(true);
				ui("ACCT_MNT_DSP_INPT_DEL_FLAG").setState(false);
		}

		ui("ACCT_MNT_DSP_AD_BTN_EDIT").setVisible(false);
		ui("ACCT_MNT_DSP_AD_BTN_SETTINGS").setVisible(true);
		ui("ACCT_MNT_DSP_AD_BTN_PASSWORD").setVisible(true);

		//account preferences
		ui("ACCT_MNT_DSP_AP_BTN_EDIT_ADD").setVisible(true);
		//ui('ACCT_MNT_DSP_TABLE_ACCTPREF').setMode("Delete");
		fn_BIND_USER_PARAM(gt_user_param,"INPUT");

		//account authorization
		ui("ACCT_MNT_DSP_AA_BTN_EDIT_ADD").setVisible(true);
		//ui('ACCT_MNT_DSP_TABLE_ACCTAUTH').setMode("Delete");
		fn_BIND_USER_ROLE(gt_user_role,"INPUT");

		//Load valuehelp
		fn_get_role_valuehelp(false);
		fn_get_param_valuehelp(false);

		//biz partner tab
		ui("CREATE_PARTNER_ADD_BUTTON").setVisible(true);
		fn_bind_biz_partner(gt_biz_partner,"INPUT");

	}


/*
// ================================================================================
// 	Message and Busy Dialog Function
// ================================================================================
*/


	function notification_message(message,durability) {


		if(document.getElementsByClassName("ns-box ns-growl ns-effect-slide  ns-type-notice ns-show").length==0){

				new neko.control.Notification({
					message 	: message,
					type 		: "notice",
					layout 		: "growl",
					effect		: "slide ",
					durability	: durability,

				}).show();
		}

	}



/*
// ================================================================================
// Function to show busy dialog
// ================================================================================
*/


	function fn_show_busy_dialog(message){
		return new sap.m.BusyDialog({
					text:message,
					//customIcon:'../image/loader/fiori.gif',
					//customIconRotationSpeed: 2000
				}).addStyleClass('GLOBAL_BUSY_DIALOG');
	}

/*
// ================================================================================
// Function to show notification message
// ================================================================================
*/



	function get_distinct_Role(data){
			var unique = {};
			var distinct = [];

			for( var i in data ){

					if(typeof(unique[data[i].role]) == "undefined"){

						if(typeof(data[i].role)!= "undefined"){
								distinct.push(data[i]);
						}


					}

					unique[data[i].role] = 0;

			}


			return distinct;
	}

	function fn_show_notification_message(message,durability) {
		if(document.getElementsByClassName("ns-box ns-growl ns-effect-slide  ns-type-notice ns-show").length==0){
			new neko.control.Notification({
				message 	: message,
				type 		: "notice",
				layout 		: "growl",
				effect		: "slide ",
				durability	: durability,

			}).show();
		}
	}


	 function convertDate(date){

		var mydate = new Date(date);

		var month = mydate.getMonth() + 1;
		month = (parseInt(month) < 10 ? '0'+month :month);

		var day = mydate.getDate();
		day =(parseInt(day) < 10 ? '0'+day :day);

		var year = 	mydate.getFullYear();


		var newFormat = year + "-" + month + "-" + day;

		return newFormat;


	}

	function fn_format_datetime(p_string_datetime,p_string_format){
		var lv_date = new Date(p_string_datetime);


		//if date is invalid - assign current date
		if(isNaN(lv_date)==true){
			lv_date = new Date();
		}

		var lv_return_string;
		switch(p_string_format){
			case "DD MM YYYY": {
									lv_month_string = lv_date.getMonth() + 1;
									lv_month_string = ( parseInt(lv_month_string) < 10 ? '0'+lv_month_string : lv_month_string );
									lv_return_string = ( lv_date.getDate() < 10 ? '0'+lv_date.getDate() : lv_date.getDate() )
													+ ' '
													+ lv_month_string
													+ ' '
													+  lv_date.getFullYear() ;
								} break;
			case "YYYY-MM-DD": {
									lv_month_string = lv_date.getMonth() + 1;
									lv_month_string = ( parseInt(lv_month_string) < 10 ? '0'+lv_month_string : lv_month_string);
									lv_return_string = lv_date.getFullYear()
													+'-'
													+ lv_month_string
													+'-'
													+ ( lv_date.getDate() < 10 ? '0'+lv_date.getDate() : lv_date.getDate() );
								} break;
			case "DD MMM YYYY": {
									lv_return_string = ( lv_date.getDate() < 10 ? '0'+String(lv_date.getDate()) : lv_date.getDate() )
													+ ' '
													+ (lv_date.toDateString().substring(4)).substring(0,3)
													+ ' '
													+  lv_date.getFullYear() ;
								} break;
			case "HH:MM:SS AMPM":	{
									var lv_hours = lv_date.getHours();
									var lv_minutes = lv_date.getMinutes();
									var lv_seconds = lv_date.getSeconds();
									var lv_ampm = lv_hours >= 12 ? 'PM' : 'AM';
									lv_hours = lv_hours % 12;
									lv_hours = lv_hours ? lv_hours : 12;
									lv_minutes = lv_minutes < 10 ? '0'+lv_minutes : lv_minutes;
									lv_seconds = lv_seconds < 10 ? '0'+lv_seconds : lv_seconds;
									lv_return_string = lv_hours+':'+lv_minutes+':'+lv_seconds+' ' +lv_ampm;
								} break;
			case "HH:MM:SS":	{
									var lv_hours = lv_date.getHours();
									var lv_minutes = lv_date.getMinutes();
									var lv_seconds = lv_date.getSeconds();
									lv_hours = lv_hours < 10 ? '0'+lv_hours : lv_hours;
									lv_minutes = lv_minutes < 10 ? '0'+lv_minutes : lv_minutes;
									lv_seconds = lv_seconds < 10 ? '0'+lv_seconds : lv_seconds;
									lv_return_string = lv_hours+':'+lv_minutes+':'+lv_seconds;
								} break;

			default : lv_return_string = lv_date.toDateString(); break;
		}

		return lv_return_string;
	}

	function Bind_ValueHelp_FieldName(func_title,Element,data,func_pTitle,func_pDesc,Element_ID,Type){

		var lt_Model = new sap.ui.model.json.JSONModel();
			lt_Model.setSizeLimit(data.length);
			lt_Model.setData(data);

		var Dialog_ID = "FieldName_"+fn_rndom_id()+10+fn_rndom_id();

			var SelecDialog = new sap.m.SelectDialog(Dialog_ID,{
					title: func_title,
					search : function(oEvent){
								var filter = [];
								var sVal = oEvent.getParameter("value");
								if(sVal !== undefined) {
									//Get the bound items
									var itemsBinding = oEvent.getParameter("itemsBinding");

									// create the local filter to apply
									var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
									filter.push(selectFilter);

									// and apply the filter to the bound items, and the Select Dialog will update
									itemsBinding.filter(filter);
								}
							},
					liveChange: function(oEvent){
									var filter = [];
									var sVal = oEvent.getParameter("value");
									var sEventType = oEvent.getParameter("eventType");
									if(sVal !== undefined) {
										//Get the bound items
										var itemsBinding = oEvent.getParameter("itemsBinding");

										// create the local filter to apply
										var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
										filter.push(selectFilter);

										// and apply the filter to the bound items, and the Select Dialog will update
										itemsBinding.filter(filter);
									}
								},
				}).addStyleClass('sapUiSizeCompact').addStyleClass('class_valuehelp_no_footer_dialogbox');


			var AppsDiagSelect =  new sap.m.Select({
				width:"130px",
				selectedkey:"index",
				items:[new sap.ui.core.Item({ text: "ID", key: "fieldname"}),new sap.ui.core.Item({ text: "Description", key: "description"})]
			});



			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentLeft(sap.ui.getCore().byId(""+Dialog_ID+"-searchField"));

			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentRight(AppsDiagSelect);



			sap.ui.getCore().byId(""+Dialog_ID+"-dialog-header").addContentRight(new sap.m.Button({icon: "sap-icon://decline",type:sap.m.ButtonType.Reject,press:function(evt){ sap.ui.getCore().byId(""+Dialog_ID+"-cancel").firePress(); }}));
			sap.ui.getCore().byId(""+Dialog_ID+"-cancel").setVisible(false);
			//sap.ui.getCore().byId(""+Dialog_ID+"-dialog-footer").setVisible(false);
			sap.ui.getCore().byId(""+Dialog_ID+"-searchField").setPlaceholder("Search..");

			// set model & bind Aggregation
			//SelecDialog.setModel(Model_VHelp, Model_sPath);
			SelecDialog.setModel(lt_Model);

				// attach close listener
			SelecDialog.attachConfirm(function(evt) {
					 var selectedItem = evt.getParameter("selectedItem");

					 if (selectedItem) {


						 Element.getParent().getCells()[2].setValue(selectedItem.getTitle());
						 Element.getParent().getCells()[2].fireChange();
						//if(typeof(gt_ROLE_INDEX[selectedItem.getTitle()]) != "undefined" ){
							 //Element.getParent().getCells()[2].setText(selectedItem.getDescription());
						//}

					 }
			});

			var temp = new sap.m.StandardListItem({
					 title		:"{"+func_pTitle+"}",
					 description:"{"+func_pDesc+"}",
					 active: true
				 })

				Element.attachValueHelpRequest(
				   function () {


					   SelecDialog.open(Element.getValue());
					   SelecDialog.setModel(lt_Model).bindAggregation("items", {
							path: "/",
							template: temp
						});
					//  SelecDialog.setModel(Model_VHelp, Model_sPath);
					//  SelecDialog.bindAggregation("items", {
					//	   path: ""+Model_sPath+">/"+Model_sPath+"",
					//	   template: temp
					//  });
				   }
			   );

		return Element;

	}

	function Bind_ValueHelp_Role(func_title,Element,data,func_pTitle,func_pDesc,Element_ID,Type){

		var lt_Model = new sap.ui.model.json.JSONModel();
			lt_Model.setSizeLimit(data.length);
			lt_Model.setData(data);

		var Dialog_ID = "Role_"+fn_rndom_id()+10+fn_rndom_id();

			var SelecDialog = new sap.m.SelectDialog(Dialog_ID,{
					title: func_title,
					search : function(oEvent){
								var filter = [];
								var sVal = oEvent.getParameter("value");
								if(sVal !== undefined) {
									//Get the bound items
									var itemsBinding = oEvent.getParameter("itemsBinding");

									// create the local filter to apply
									var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
									filter.push(selectFilter);

									// and apply the filter to the bound items, and the Select Dialog will update
									itemsBinding.filter(filter);
								}
							},
					liveChange: function(oEvent){
									var filter = [];
									var sVal = oEvent.getParameter("value");
									var sEventType = oEvent.getParameter("eventType");
									if(sVal !== undefined) {
										//Get the bound items
										var itemsBinding = oEvent.getParameter("itemsBinding");

										// create the local filter to apply
										var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
										filter.push(selectFilter);

										// and apply the filter to the bound items, and the Select Dialog will update
										itemsBinding.filter(filter);
									}
								},
				}).addStyleClass('sapUiSizeCompact').addStyleClass('class_valuehelp_no_footer_dialogbox');


			var AppsDiagSelect =  new sap.m.Select({
				width:"130px",
				selectedkey:"index",
				items:[new sap.ui.core.Item({ text: "ID", key: "role"}),new sap.ui.core.Item({ text: "Description", key: "role_desc"})]
			});



			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentLeft(sap.ui.getCore().byId(""+Dialog_ID+"-searchField"));

			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentRight(AppsDiagSelect);



			sap.ui.getCore().byId(""+Dialog_ID+"-dialog-header").addContentRight(new sap.m.Button({icon: "sap-icon://decline",type:sap.m.ButtonType.Reject,press:function(evt){ sap.ui.getCore().byId(""+Dialog_ID+"-cancel").firePress(); }}));
			sap.ui.getCore().byId(""+Dialog_ID+"-cancel").setVisible(false);
			//sap.ui.getCore().byId(""+Dialog_ID+"-dialog-footer").setVisible(false);
			sap.ui.getCore().byId(""+Dialog_ID+"-searchField").setPlaceholder("Search..");

			// set model & bind Aggregation
			//SelecDialog.setModel(Model_VHelp, Model_sPath);
			SelecDialog.setModel(lt_Model);

				// attach close listener
			SelecDialog.attachConfirm(function(evt) {
					 var selectedItem = evt.getParameter("selectedItem");

					 if (selectedItem) {


						 Element.getParent().getCells()[1].setValue(selectedItem.getTitle());

						//if(typeof(gt_ROLE_INDEX[selectedItem.getTitle()]) != "undefined" ){
							// Element.getParent().getCells()[2].setText(selectedItem.getDescription());
						//}

					 }
			});

			var temp = new sap.m.StandardListItem({
					 title		:"{"+func_pTitle+"}",
					 description:"{"+func_pDesc+"}",
					 active: true
				 })

				Element.attachValueHelpRequest(
				   function () {


					   SelecDialog.open(Element.getValue());
					   SelecDialog.setModel(lt_Model).bindAggregation("items", {
							path: "/",
							template: temp
						});
					//  SelecDialog.setModel(Model_VHelp, Model_sPath);
					//  SelecDialog.bindAggregation("items", {
					//	   path: ""+Model_sPath+">/"+Model_sPath+"",
					//	   template: temp
					//  });
				   }
			   );

		return Element;

	}

	function Bind_ValueHelp_Parameter(func_title,Element,data,func_pTitle,func_pDesc,Element_ID,Type){

		var lt_Model = new sap.ui.model.json.JSONModel();
			lt_Model.setSizeLimit(data.length);
			lt_Model.setData(data);

		var Dialog_ID = "Parameter_"+fn_rndom_id()+10+fn_rndom_id();

			var SelecDialog = new sap.m.SelectDialog(Dialog_ID,{
					title: func_title,
					search : function(oEvent){
								var filter = [];
								var sVal = oEvent.getParameter("value");
								if(sVal !== undefined) {
									//Get the bound items
									var itemsBinding = oEvent.getParameter("itemsBinding");

									// create the local filter to apply
									var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
									filter.push(selectFilter);

									// and apply the filter to the bound items, and the Select Dialog will update
									itemsBinding.filter(filter);
								}
							},
					liveChange: function(oEvent){
									var filter = [];
									var sVal = oEvent.getParameter("value");
									var sEventType = oEvent.getParameter("eventType");
									if(sVal !== undefined) {
										//Get the bound items
										var itemsBinding = oEvent.getParameter("itemsBinding");

										// create the local filter to apply
										var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
										filter.push(selectFilter);

										// and apply the filter to the bound items, and the Select Dialog will update
										itemsBinding.filter(filter);
									}
								},
				}).addStyleClass('sapUiSizeCompact').addStyleClass('class_valuehelp_no_footer_dialogbox');


			var AppsDiagSelect =  new sap.m.Select({
				width:"130px",
				selectedkey:"index",
				items:[new sap.ui.core.Item({ text: "ID", key: "param_id"}),new sap.ui.core.Item({ text: "Description", key: "param_desc"})]
			});



			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentLeft(sap.ui.getCore().byId(""+Dialog_ID+"-searchField"));

			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentRight(AppsDiagSelect);



			sap.ui.getCore().byId(""+Dialog_ID+"-dialog-header").addContentRight(new sap.m.Button({icon: "sap-icon://decline",type:sap.m.ButtonType.Reject,press:function(evt){ sap.ui.getCore().byId(""+Dialog_ID+"-cancel").firePress(); }}));
			sap.ui.getCore().byId(""+Dialog_ID+"-cancel").setVisible(false);
			//sap.ui.getCore().byId(""+Dialog_ID+"-dialog-footer").setVisible(false);
			sap.ui.getCore().byId(""+Dialog_ID+"-searchField").setPlaceholder("Search..");

			// set model & bind Aggregation
			//SelecDialog.setModel(Model_VHelp, Model_sPath);
			SelecDialog.setModel(lt_Model);

				// attach close listener
			SelecDialog.attachConfirm(function(evt) {
					 var selectedItem = evt.getParameter("selectedItem");

					 if (selectedItem) {


						 Element.getParent().getCells()[1].setValue(selectedItem.getTitle());

						//if(typeof(gt_ROLE_INDEX[selectedItem.getTitle()]) != "undefined" ){
							// Element.getParent().getCells()[2].setText(selectedItem.getDescription());
						//}

					 }
			});

			var temp = new sap.m.StandardListItem({
					 title		:"{"+func_pTitle+"}",
					 description:"{"+func_pDesc+"}",
					 active: true
				 })

				Element.attachValueHelpRequest(
				   function () {


					   SelecDialog.open(Element.getValue());
					   SelecDialog.setModel(lt_Model).bindAggregation("items", {
							path: "/",
							template: temp
						});
					//  SelecDialog.setModel(Model_VHelp, Model_sPath);
					//  SelecDialog.bindAggregation("items", {
					//	   path: ""+Model_sPath+">/"+Model_sPath+"",
					//	   template: temp
					//  });
				   }
			   );

		return Element;

	}

	function Bind_ValueHelp(func_title,Element,data,func_pTitle,func_pDesc,Element_ID,Type){

		var lt_Model = new sap.ui.model.json.JSONModel();
			lt_Model.setSizeLimit(data.length);
			lt_Model.setData(data);

		var Dialog_ID = Element_ID;
		var Template_ID = Element_ID+"_TEMP";

			var SelecDialog = new sap.m.SelectDialog(Dialog_ID,{
					title: func_title,
					search : function(oEvent){
								var filter = [];
								var sVal = oEvent.getParameter("value");
								if(sVal !== undefined) {
									//Get the bound items
									var itemsBinding = oEvent.getParameter("itemsBinding");

									// create the local filter to apply
									var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
									filter.push(selectFilter);

									// and apply the filter to the bound items, and the Select Dialog will update
									itemsBinding.filter(filter);
								}
							},
					liveChange: function(oEvent){
									var filter = [];
									var sVal = oEvent.getParameter("value");
									var sEventType = oEvent.getParameter("eventType");
									if(sVal !== undefined) {
										//Get the bound items
										var itemsBinding = oEvent.getParameter("itemsBinding");

										// create the local filter to apply
										var selectFilter = new sap.ui.model.Filter(sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").getContentRight()[0].getSelectedKey(), sap.ui.model.FilterOperator.Contains , sVal);
										filter.push(selectFilter);

										// and apply the filter to the bound items, and the Select Dialog will update
										itemsBinding.filter(filter);
									}
								},
				}).addStyleClass('sapUiSizeCompact').addStyleClass('class_valuehelp_no_footer_dialogbox');


			var AppsDiagSelect =  new sap.m.Select({
				width:"130px",
				selectedkey:"index",
				items:[new sap.ui.core.Item({ text: "ID", key: func_pTitle}),new sap.ui.core.Item({ text: "Description", key: func_pDesc})]
			});



			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentLeft(sap.ui.getCore().byId(""+Dialog_ID+"-searchField"));

			sap.ui.getCore().byId(""+Dialog_ID+"-subHeader").addContentRight(AppsDiagSelect);



			sap.ui.getCore().byId(""+Dialog_ID+"-dialog-header").addContentRight(new sap.m.Button({icon: "sap-icon://decline",type:sap.m.ButtonType.Reject,press:function(evt){ sap.ui.getCore().byId(""+Dialog_ID+"-cancel").firePress(); }}));
			sap.ui.getCore().byId(""+Dialog_ID+"-cancel").setVisible(false);
			//sap.ui.getCore().byId(""+Dialog_ID+"-dialog-footer").setVisible(false);
			sap.ui.getCore().byId(""+Dialog_ID+"-searchField").setPlaceholder("Search..");

			// set model & bind Aggregation
			//SelecDialog.setModel(Model_VHelp, Model_sPath);
			SelecDialog.setModel(lt_Model);

				// attach close listener
			SelecDialog.attachConfirm(function(evt) {
					 var selectedItem = evt.getParameter("selectedItem");

					 if (selectedItem) {


						 Element.setValue(selectedItem.getTitle());
						 Element.fireChange();


					 }
			});

			var temp = new sap.m.StandardListItem(Template_ID,{
					 title		:"{"+func_pTitle+"}",
					 description:"{"+func_pDesc+"}",
					 active: true
				 })

				Element.attachValueHelpRequest(
				   function () {


					   SelecDialog.open(Element.getValue());
					   SelecDialog.setModel(lt_Model).bindAggregation("items", {
							path: "/",
							template: temp
						});
					//  SelecDialog.setModel(Model_VHelp, Model_sPath);
					//  SelecDialog.bindAggregation("items", {
					//	   path: ""+Model_sPath+">/"+Model_sPath+"",
					//	   template: temp
					//  });
				   }
			   );

		return SelecDialog;

	}


	//function to erase the value when adding new role object
	function fn_new_role_object_set_to_default(){

		ui("ROLEOBJV_MNT_RIGHT_DIALOG_OBJECT").setValue("");
		ui("ROLEOBJV_MNT_RIGHT_DIALOG_FIELDNAME").setValue("");
		ui("ROLEOBJV_MNT_RIGHT_DIALOG_Value").setValue("");
		ui("ROLEOBJV_MNT_RIGHT_DIALOG_STAT").setState(true);

		ui("ROLEOBJV_MNT_RIGHT_DIALOG_OBJECT").setValueState("None");
		ui("ROLEOBJV_MNT_RIGHT_DIALOG_FIELDNAME").setValueState("None");
		ui("ROLEOBJV_MNT_RIGHT_DIALOG_Value").setValueState("None");

		ui("ROLEOBJV_MNT_RIGHT_DIALOG_OBJECT").setValueStateText("");
		ui("ROLEOBJV_MNT_RIGHT_DIALOG_FIELDNAME").setValueStateText("");
		ui("ROLEOBJV_MNT_RIGHT_DIALOG_Value").setValueStateText("");



	}


	//function to erase the value when adding new account authorization
	function fn_new_acct_auth_set_to_default(){

		ui("ACCT_MNT_DSP_DIALOG_ACCTAUTH_ROLE").setValue("");
		ui("ACCT_MNT_DSP_DIALOG_ACCTAUTH_ROLE").setValueState("None");
		ui("ACCT_MNT_DSP_DIALOG_ACCTAUTH_ROLE").setValueStateText("");
		ui("ACCT_MNT_DSP_DIALOG_ACCTAUTH_DESC").setValue("");
		ui("ACCT_MNT_DSP_DIALOG_ACCTAUTH_VLFROM").setDateValue(new Date());
		ui("ACCT_MNT_DSP_DIALOG_ACCTAUTH_VLTO").setValue("9999-12-31");
		ui("ACCT_MNT_DSP_DIALOG_ACCTAUTH_STAT").setState(true);


	}


	//function to erase the value when adding new account authorization
	function fn_new_acct_pref_set_to_default(){

		ui("ACCT_MNT_DSP_DIALOG_ACCTPREF_PARAM_ID").setValue("");
		ui("ACCT_MNT_DSP_DIALOG_ACCTPREF_PARAM_DESC").setValue("");
		ui("ACCT_MNT_DSP_DIALOG_ACCTPREF_VALUE").setValue("");

		ui("ACCT_MNT_DSP_DIALOG_ACCTPREF_PARAM_ID").setValueState("None");
		ui("ACCT_MNT_DSP_DIALOG_ACCTPREF_PARAM_ID").setValueStateText("");
		ui("ACCT_MNT_DSP_DIALOG_ACCTPREF_VALUE").setValueState("None");


	}



	//function to return the current date
	function get_ClientDate(){
		d = new Date();

		day = d.getDate();
				day = day < 10 ? '0'+String(day) : day;
		month = parseInt(d.getMonth()) + 1;
				month = month < 10 ? '0'+String(month) : month;
		year = d.getFullYear();

		return year +'-'+ month  +'-'+ day;
	}


	//GET CURRENT TIME
	function timeNow() {
	  var time = "";
	  var lv_dateToday = new Date();
	  var lv_hour = (lv_dateToday.getHours()<10?'0':'') + lv_dateToday.getHours();
	  var lv_min = (lv_dateToday.getMinutes()<10?'0':'') + lv_dateToday.getMinutes();
	  var lv_sec = (lv_dateToday.getSeconds()<10?'0':'') + lv_dateToday.getSeconds();

	  time = lv_hour + ':' + lv_min + ':' + lv_sec;

	  return time;
	}

	// function to validate e-mail address
	function inputEmailValidator(inputvalue,Element){
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
			if (re.test(inputvalue)) {
				Element.setValueState("Success");
			} else {
				Element.setValueState("Error");
			}
	}

//2016.02.18 BEGIN - Nahor added this to set password policy input fields from active to active

	//ACTIVE
	function fn_SET_PASSWORD_POLICY_ACTIVE(){

		gv_mode = "edit";

		ui("PWD_POLC_DSP_INPT_MINIMUM_LENGTH").setEnabled(true);
        ui("PWD_EXPI_DSP_INPT_MINIMUM_LENGTH").setEnabled(true);
        ui("PWD_HIST_DSP_INPT_MINIMUM_LENGTH").setEnabled(true);
		ui("PWD_POLC_DSP_INPT_UPPERCASE").setEnabled(true);
		ui("PWD_POLC_DSP_INPT_LOWERCASE").setEnabled(true);
		ui("PWD_POLC_DSP_INPT_NUMBER").setEnabled(true);
		ui("PWD_POLC_DSP_INPT_NONALPHA").setEnabled(true);
		ui("PAGE_PWD_POLC_BTN_SAVE").setVisible(true);
		ui("PAGE_PWD_POLC_BTN_CANCEL").setVisible(true);
		ui("PAGE_PWD_POLC_BTN_EDIT").setVisible(false);
	}

	//INACTIVE
	function fn_SET_PASSWORD_POLICY_INACTIVE(){

		gv_mode = "display";

		ui("PWD_POLC_DSP_INPT_MINIMUM_LENGTH").setEnabled(false);
        ui("PWD_EXPI_DSP_INPT_MINIMUM_LENGTH").setEnabled(false);
        ui("PWD_HIST_DSP_INPT_MINIMUM_LENGTH").setEnabled(false);
		ui("PWD_POLC_DSP_INPT_UPPERCASE").setEnabled(false);
		ui("PWD_POLC_DSP_INPT_LOWERCASE").setEnabled(false);
		ui("PWD_POLC_DSP_INPT_NUMBER").setEnabled(false);
		ui("PWD_POLC_DSP_INPT_NONALPHA").setEnabled(false);
		ui("PAGE_PWD_POLC_BTN_EDIT").setVisible(true);
		ui("PAGE_PWD_POLC_BTN_SAVE").setVisible(false);
		ui("PAGE_PWD_POLC_BTN_CANCEL").setVisible(false);
	}
//2016.02.18 END - Nahor


//function to erase the value when adding new catalog
	function fn_new_catalog_set_to_default(){

		ui("CAT_MNT_INPUT_NEW_CAT_CATALOG_ID").setValue("");
		ui("CAT_MNT_INPUT_NEW_CAT_CATALOG_DESC").setValue("");
		ui("CAT_MNT_INPUT_NEW_CAT_TARGET_URL").setValue("");
		ui("CAT_MNT_INPUT_NEW_CAT_ICON").setValue("");
		ui("CAT_MNT_INPUT_NEW_CAT_MAIN").setState(false);

	}


//function to erase the value when adding new application
	function fn_new_application_set_to_default(){

		ui("CAT_MNT_INPUT_NEW_APP_APPLICATION_ID").setValue("");
		ui("CAT_MNT_INPUT_NEW_APP_APPLICATION_DESC").setValue("");
		ui("CAT_MNT_INPUT_NEW_APP_TARGET_URL").setValue("");
		ui("CAT_MNT_INPUT_NEW_APP_ICON").setValue("");
		ui("CAT_MNT_INPUT_NEW_APP_IMAGE").setValue("");
		ui("CAT_MNT_INPUT_NEW_APP_STATUS").setState(true);
		ui("DIALOG_APP_BTN_SAVE").setEnabled(false);

	}

//function to erase the value when adding new application
	function fn_new_assignment_set_to_default(){

		ui("CAT_MNT_INPUT_NEW_ASS_CONTENT").setValue("");
		ui("CAT_MNT_INPUT_NEW_ASS_SEQUENCE").setValue("");
		ui("CAT_MNT_INPUT_NEW_APP_STATUS").setState(true);
		ui("CAT_MNT_INPUT_NEW_ASS_CONTENT").setValueState("None");
		ui("CAT_MNT_INPUT_NEW_ASS_CONTENT").setValueStateText("");
		ui("CAT_MNT_INPUT_NEW_ASS_SEQUENCE").setValueState("None");
		ui("CAT_MNT_INPUT_NEW_ASS_SEQUENCE").setValueStateText("");

	}

//function to set assignment button to default
	function fn_assignment_set_button_to_default(){
		ui("PAGE_ASS_BTN_EDIT").setVisible(true);
		ui("PAGE_ASS_BTN_APP_ASS").setVisible(false);
		ui("PAGE_ASS_BTN_SAVE").setVisible(false);
		ui("PAGE_ASS_BTN_CANCEL").setVisible(false);
		ui("DIALOG_ASS_BTN_SAVE").setEnabled(false);
	}

//function to set application button to default
	function fn_application_set_button_to_default(){
		ui("PAGE_APP_BTN_EDIT").setVisible(true);
		ui("PAGE_APP_BTN_ADD").setVisible(false);
		ui("PAGE_APP_BTN_SAVE").setVisible(false);
		ui("PAGE_APP_BTN_CANCEL").setVisible(false);
	}

	function fn_information(message, function_to_call){
	    var status;
	    var lo_dialog ;
	    var lo_flexbox = new sap.m.FlexBox({
	        visible: false,
	        justifyContent:sap.m.FlexJustifyContent.Center,
	        items:[
	            lo_dialog = new sap.m.Dialog({
	                title:"Confirmation",
	                contentWidth : "100px",
					contentHeight : "50px",
	                content: [
	                    new sap.m.Label({
	                        text: message
	                    })
	                ],
	                beginButton: new sap.m.Button({
	                    icon: "sap-icon://accept",
	                    type: sap.m.ButtonType.Accept,
	                    text: "Ok",
	                    press: function () {
	                        status = gv_ok;
	                        lo_dialog.close();
	                    }
	                }),
	                endButton: new sap.m.Button({
	                    //icon: "sap-icon://accept",
	                    type: sap.m.ButtonType.Reject,
	                    text: "Cancel",
	                    press: function () {
	                        status = gv_cancel;
	                        lo_dialog.close();
	                    }
	                }),
	                afterClose:function(){
	                   if(status === gv_ok){


	                     switch (function_to_call){
	                       case 'gv_create_back':
	                       case 'gv_editor_create_back':
	                       case 'gv_discard_create':

								gv_mode = "display";
								go_App_Right.back();
								sap.ui.getCore().byId("txtArea").setValue('');
								fn_editor_create_destroy();
								fn_freeze_table_header();

	                       break;
	                       case 'gv_editor_edit_back':
	                       case 'gv_discard_edit':

								gv_mode = "display";
								go_App_Right.back();
								sap.ui.getCore().byId("txtArea_edit").setValue('');
								fn_editor_edit_destroy();
								fn_freeze_table_header();

	                       break;
	                       case 'gv_remove':
	                            fn_editor_delete(GV_EDIT_GUID);
	                            fn_freeze_table_header();
	                       break;
	                    }
	                }else{

	                }

	               }
	              }).addStyleClass('sapUiSizeCompact')
	            ]
	    });
	    lo_dialog.open();
	}

	function fn_confirmation(message,function_to_call){
    var status;
    var lo_dialog ;
    var lo_flexbox = new sap.m.FlexBox({
        visible: false,
        justifyContent:sap.m.FlexJustifyContent.Center,
        items:[
            lo_dialog = new sap.m.Dialog({
                title:"Confirmation",
                contentWidth : "100px",
				contentHeight : "50px",
                content: [
                    new sap.m.Label({
                        text: message
                    })
                ],
                beginButton: new sap.m.Button({
                    icon: "sap-icon://accept",
                    type: sap.m.ButtonType.Accept,
                    text: "Ok",
                    press: function () {
                        status = gv_ok;
                        lo_dialog.close();
                    }
                }),
                endButton: new sap.m.Button({
                    icon: "sap-icon://decline",
                    type: sap.m.ButtonType.Reject,
                    text: "Cancel",
                    press: function () {
                        status = gv_cancel;
                        lo_dialog.close();
                    }
                }),

               afterClose:function(){
                   if(status === gv_ok){
                   switch (function_to_call) {
                       case 'gv_create':
                           fn_editor_selected_value();
                           fn_freeze_table_header();
                           break;
                       case 'gv_edit':
                           fn_editor_selected_value_edit();
                           fn_freeze_table_header();
                       default:
                           break;
                   }
                }else{
                       switch (function_to_call){
                           case 'gv_edit_back':
                           go_App_Right.back();
                           sap.ui.getCore().byId("txtArea_edit").setValue('');
                           fn_editor_edit_destroy();
                           fn_freeze_table_header();
                           break;
                       }
                   }
               }
            }).addStyleClass('sapUiSizeCompact')
        ]
    });
    lo_dialog.open();
    //return status;
}





	function fn_editor_preview(lv_title,lv_content){
    var lo_carousel = new sap.m.Carousel({
       width : '100%' ,
        height:"550px"
    });
    lo_carousel.destroyPages();
//Editor title
//Editor Content
    lo_carousel.addPage(new sap.m.ScrollContainer({width:'980px',height:'500px',horizontal: true,
	vertical: true,content:[new sap.ui.core.HTML({  content:[ '<div class=\'class_whats_new_content\'><p><h2>' + lv_title + '</h2></p>' + lv_content+ '</div>']  })]}));
    var lo_dialog_preview = new sap.m.Dialog({
        showHeader: false,
        stretchOnPhone: false,
        stretch: false,
        verticalScrolling: false,
        contentWidth: "1000px",
        contentHeight: "650px",
        align: 'center',
        content: [
            new sap.m.Bar({
                enableFlexBox: false,
                contentMiddle: [new sap.m.Label({ text: "Preview" })],
                contentRight: [new sap.m.Button({ icon: "sap-icon://decline", type: sap.m.ButtonType.Reject, press: function(e) { lo_dialog_preview.close(); } })],
            }),
            //new sap.m.HBox({items:[new sap.m.Label({text:'',width:'20px'}),lo_carousel]}),
            lo_carousel
        ]
    });


    lo_dialog_preview.addStyleClass("class_dialog_padding class_dialog_width sapUiSizeCompact");
    lo_dialog_preview.open();
}
function fn_editor_selected_value(){
     var lv_title       = sap.ui.getCore().byId("title_create").getValue();//title
     var lv_language    = sap.ui.getCore().byId("language_create").getSelectedKey();//language
     var lv_state       = sap.ui.getCore().byId("status_create").getState(); //status
     var lv_startdate   = fn_format_datetime(sap.ui.getCore().byId("date_create").getDateValue() , "YYYY-MM-DD");//sap.ui.getCore().byId("date_create").getDateValue();  //date
     var lv_enddate     = fn_format_datetime(sap.ui.getCore().byId("date_create").getSecondDateValue() , "YYYY-MM-DD");//sap.ui.getCore().byId("date_create").getSecondDateValue();  //date
     var lv_text        = sap.ui.getCore().byId("txtArea").getValue(); //editor

    var lv_app_id = (sap.ui.getCore().byId("app_id_create").getSelectedItem() == null) ? "" : sap.ui.getCore().byId("app_id_create").getSelectedKey();
    var lv_sub_app = (sap.ui.getCore().byId("sub_app_create").getSelectedItem() == null) ? "" : sap.ui.getCore().byId("sub_app_create").getSelectedKey();

    if(lv_state === true){
        state = '1';
    }else{
        state = '0';
    }

  var lt_editor = {
   		'APP_ID' : lv_app_id,
   		'SUB_APP':lv_sub_app,
   		'LANGUAGE':lv_language ,
   		'TITLE':lv_title ,
   		'DESCRIPTION':lv_text ,
   		'VERSION':'1',
   		'START_DATE':lv_startdate,
   		'END_DATE':lv_enddate,
   		'ACTIVE':state,
   		'POSITION':0,
		'USER_GUIDE':""
   	};
   fn_submit_editor_data(lt_editor);
}

function fn_editor_selected_value_edit(){

   	var lv_title        = sap.ui.getCore().byId("title_edit").getValue();//title
    var lv_language     = sap.ui.getCore().byId("language_edit").getSelectedKey();//language
    var lv_state        = sap.ui.getCore().byId("status_edit").getState(); //status
    var lv_startdate    = fn_format_datetime(sap.ui.getCore().byId("date_edit").getDateValue() , "YYYY-MM-DD");//sap.ui.getCore().byId("date_edit").getDateValue();  //date
    var lv_enddate      = fn_format_datetime(sap.ui.getCore().byId("date_edit").getSecondDateValue() , "YYYY-MM-DD");//sap.ui.getCore().byId("date_edit").getSecondDateValue();  //date
    var lv_text         = sap.ui.getCore().byId("txtArea_edit").getValue(); //editor

    var lv_app_id = (sap.ui.getCore().byId("app_id_edit").getSelectedItem() == null) ? "" : sap.ui.getCore().byId("app_id_edit").getSelectedKey();
    var lv_sub_app = (sap.ui.getCore().byId("sub_app_edit").getSelectedItem() == null) ? "" : sap.ui.getCore().byId("sub_app_edit").getSelectedKey();

    var state = '' ;
    if(lv_state === true){
        state = '1';
    }else{t
        state = '0';
    }

   var lt_editor = {
   		'GUID' : GV_EDIT_GUID,
   		'APP_ID' : lv_app_id,
   		'SUB_APP':lv_sub_app,
   		'LANGUAGE':lv_language ,
   		'TITLE':lv_title ,
   		'DESCRIPTION':lv_text ,
   		'VERSION':'1',
   		'START_DATE':lv_startdate,
   		'END_DATE':lv_enddate,
   		'ACTIVE':state,
   		'POSITION':0
   	};


   fn_submit_editor_data_edit(lt_editor);
}

function fn_editor_selected_value_default(){
    var lv_startdate = new Date();
    var lv_enddate = new Date();
    sap.ui.getCore().byId("title_create").setValue('');
    sap.ui.getCore().byId("date_create").setDateValue(lv_startdate);  //date
    sap.ui.getCore().byId("date_create").setSecondDateValue(lv_enddate);  //date
    sap.ui.getCore().byId("language_create").setSelectedKey('E');
    sap.ui.getCore().byId('txtArea').setValue('');
}

function fn_editor_selected_value_set(lv_value){
    var state ;
    if(lv_value.ACTIVE === '1'){
        state = true;
    }else{
        state = false;
    }
    var lv_startdate = new Date(lv_value.START_DATE);
    var lv_enddate = new Date(lv_value.END_DATE);

    sap.ui.getCore().byId("title_edit").setValue(lv_value.TITLE);//title
    sap.ui.getCore().byId("language_edit").setSelectedKey(lv_value.LANGUAGE);//language

    sap.ui.getCore().byId("status_edit").setState(state); //status
    sap.ui.getCore().byId("date_edit").setDateValue(lv_startdate);  //date
    sap.ui.getCore().byId("date_edit").setSecondDateValue(lv_enddate);  //date

    sap.ui.getCore().byId("app_id_edit").setSelectedKey(lv_value.APP_ID);//app id

    var lv_item;

	if(lv_value.APP_ID == "DASHBOARD"){

		ui('sub_app_edit').destroyItems();

		var lv_item1 = new sap.ui.core.Item({text:"What's New", key:"WHATS_NEW"});
		var lv_item2 = new sap.ui.core.Item({text:"EULA", key:"EULA"});

		ui('sub_app_edit').addItem(lv_item1);
		ui('sub_app_edit').addItem(lv_item2);

	}

    sap.ui.getCore().byId("sub_app_edit").setSelectedKey(lv_value.SUB_APP);//sub app



}

function fn_editor_create_add(){
    GV_EDITOR_CREATE_COUNTER++
    if(GV_EDITOR_CREATE_COUNTER > 1){
       GV_EDITOR_CREATE_COUNTER--
      //  $('#txtArea-inner').redactor('core.destroy');
			var editor = tinymce.get('txtArea-inner');

					if(editor !== null){
							editor.remove();
					}
       fn_initRedactor();
    }else{
       fn_initRedactor();
    }
}
function fn_editor_create_destroy(){
    GV_EDITOR_CREATE_COUNTER--
    if(GV_EDITOR_CREATE_COUNTER < 0){
       GV_EDITOR_CREATE_COUNTER++
    }else{
			var editor = tinymce.get('txtArea-inner');

					if(editor !== null){
							editor.remove();
					}
      //  $('#txtArea-inner').redactor('core.destroy');
}
    }
function fn_editor_edit_add(lv_value){
    GV_EDITOR_EDIT_COUNTER++
    if(GV_EDITOR_EDIT_COUNTER > 1){
       GV_EDITOR_EDIT_COUNTER--
      //  $('#txtArea_edit-inner').redactor('core.destroy');
			var editor = tinymce.get('txtArea_edit-inner');

					if(editor !== null){
							editor.remove();
					}

       fn_initRedactor_edit(lv_value);
    }else{
       fn_initRedactor_edit(lv_value);
    }
}
function fn_editor_edit_destroy(){
    GV_EDITOR_EDIT_COUNTER--
    if(GV_EDITOR_EDIT_COUNTER < 0){
       GV_EDITOR_EDIT_COUNTER++
    }else{
      //  $('#txtArea_edit-inner').redactor('core.destroy');
			var editor = tinymce.get('txtArea_edit-inner');

					if(editor !== null){
							editor.remove();
					}
    }
}

function fn_initRedactor() {

    go_App_Right.to('editor_create');

    setTimeout(function(){

	    // 	$('#txtArea-inner').redactor({
		  //   minHeight: 300,
		  //   maxHeight: 300,
		  //   imageUpload:GURL_REDACTOR_IMAGE,
		  //   fileUpload: GURL_REDACTOR_FILE,
		  //   plugins: ['inlinestyle','alignment','table','underline'],
		  //      //plugins: ['advanced'],
	    // });

			tinymce.init({
				selector: '#txtArea-inner',
				height:"500px",
				plugins: [
						'save fullscreen print advlist autolink lists link image charmap preview hr anchor pagebreak',
						'searchreplace wordcount visualblocks visualchars code',
						'insertdatetime media nonbreaking table directionality',
						'emoticons template paste textpattern imagetools codesample noneditable'
				],
				toolbar: 'fullscreen codesample | bold italic  fontselect fontsizeselect | hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image |  undo redo | forecolor backcolor emoticons | code',
				fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
				paste_data_images: true,
				images_upload_url: '/admin/api/tinymce/upload',
				entity_encoding : "named",
				noneditable_noneditable_class: "mceNonEditable",
				relative_urls: false,
				setup:function(editor) {
					//Callback before instance
				},
				init_instance_callback : function(editor) {
					console.log("Editor: " + editor.id + " is now initialized.");
					 editor.on("change", function(e){
							ui('txtArea').setValue(editor.getContent());
						});
					editor.on("keyup", function(){
							ui('txtArea').setValue(editor.getContent());
					});
					if(gv_mode == 'display'){
							editor.setMode('readonly');
						}else{
							editor.setMode('design');
						}
				}
				});

	},100);

    //console.log($('#txtArea-inner').redactor());



}

function fn_initRedactor_edit(lv_value) {

    sap.ui.getCore().byId("txtArea_edit").setValue(lv_value.DESCRIPTION); //editor

    go_App_Right.to('editor_edit');

    setTimeout(function(){
    //     $('#txtArea_edit-inner').redactor({
    //     minHeight: 300,
    //     maxHeight: 300,
    //     imageUpload:GURL_REDACTOR_IMAGE,
    //     fileUpload: GURL_REDACTOR_FILE,
    //     plugins: ['inlinestyle','alignment','table','underline'],
    //    //plugins: ['advanced'],
    // });

		tinymce.init({
				selector: '#txtArea_edit-inner',
				height:"500px",
				plugins: [
						'save fullscreen print advlist autolink lists link image charmap preview hr anchor pagebreak',
						'searchreplace wordcount visualblocks visualchars code',
						'insertdatetime media nonbreaking table directionality',
						'emoticons template paste textpattern imagetools codesample noneditable'
				],
				toolbar: 'fullscreen codesample | bold italic  fontselect fontsizeselect | hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image |  undo redo | forecolor backcolor emoticons | code',
				fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
				paste_data_images: true,
				images_upload_url: '/admin/api/tinymce/upload',
				entity_encoding : "named",
				noneditable_noneditable_class: "mceNonEditable",
				relative_urls: false,
				setup:function(editor) {
					//Callback before instance
				},
				init_instance_callback : function(editor) {
					console.log("Editor: " + editor.id + " is now initialized.");
					 editor.on("change", function(e){
							ui('txtArea_edit').setValue(editor.getContent());
						});
					editor.on("keyup", function(){
							ui('txtArea_edit').setValue(editor.getContent());
					});
					if(gv_mode == 'display'){
							editor.setMode('readonly');
						}else{
							editor.setMode('design');
						}
				}
				});

    },100);

    fn_editor_selected_value_set(lv_value)
}



function getParameterByName(name) {
		return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, ""])[1]);
	}

/*
// ================================================================================
// Function to SEARCH LOCK ENTRIES
// ================================================================================
*/

//function fn_search_lockentries(lv_value, lv_table){
//
//	var filters = new Array();
//	var oFilter = new sap.ui.model.Filter(lv_table, sap.ui.model.FilterOperator.Contains, lv_value)
//	filters.push(oFilter);
//
//	ui('LOCK_ENTRY_TABLE').getBinding('items').filter(filters);
//
//	var lv_list_length = ui('LOCK_ENTRY_TABLE').getBinding("items").iLength;
//
//	ui('LOCK_ENTRY_LABEL').setText("Lock Entry List ("+lv_list_length+")");
//
//}

function fn_search_lockentries(){

    var lv_class_val 	=  ui("LOCK_ENTRY_SEARCH_LOCK").getValue();
    var lv_mode_val 	=  ui("LOCK_ENTRY_SEARCH_MODE").getValue();
    var lv_username_val =  ui("LOCK_ENTRY_SEARCH_USERNAME").getValue();

	var filters = new Array();

	var oFilter_class = new sap.ui.model.Filter("LOCK_CLASS", sap.ui.model.FilterOperator.Contains, lv_class_val);
	filters.push(oFilter_class);

	var oFilter_mode = new sap.ui.model.Filter("MODE", sap.ui.model.FilterOperator.Contains, lv_mode_val )
	filters.push(oFilter_mode );

	var oFilter_username = new sap.ui.model.Filter("USERNAME", sap.ui.model.FilterOperator.Contains, lv_username_val )
	filters.push(oFilter_username );

	ui('LOCK_ENTRY_TABLE').getBinding('items').filter(filters);

	var lv_list_length = ui('LOCK_ENTRY_TABLE').getBinding("items").iLength;

	ui('LOCK_ENTRY_LABEL').setText("Lock Entry List ("+lv_list_length+")");

}

function fn_clear_inputted_values(dialog){

	switch (dialog) {

		case "CREATE_USER":

			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_USERNAME').setValue("");
			sap.ui.getCore().byId("CREATE_USER_INPUT_ADD_USERNAME").setValueState("None");
			sap.ui.getCore().byId("CREATE_USER_INPUT_ADD_USERNAME").setValueStateText("");
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_EMAIL').setValue("");
			sap.ui.getCore().byId("CREATE_USER_INPUT_ADD_EMAIL").setValueState("None");
			sap.ui.getCore().byId("CREATE_USER_INPUT_ADD_EMAIL").setValueStateText("");
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_PASS').setValue("");
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_PASSCN').setValue("");
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_DISPNAME').setValue("");
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_FNAME').setValue("");
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_LNAME').setValue("");
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_STATUS').setState(true);
			sap.ui.getCore().byId('CREATE_USER_INPUT_ADD_WELCOME').setSelected(true);

		break;

		case "COPY_USER":

			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_FROM').setValue("");
			sap.ui.getCore().byId("ACCT_MNT_INPUT_COPY_USER_FROM").setValueState("None");
			sap.ui.getCore().byId("ACCT_MNT_INPUT_COPY_USER_FROM").setValueStateText("");
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_TO').setValue("");
			sap.ui.getCore().byId("ACCT_MNT_INPUT_COPY_USER_TO").setValueState("None");
			sap.ui.getCore().byId("ACCT_MNT_INPUT_COPY_USER_TO").setValueStateText("");
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_EMAIL').setValue("");
			sap.ui.getCore().byId("ACCT_MNT_INPUT_COPY_USER_EMAIL").setValueState("None");
			sap.ui.getCore().byId("ACCT_MNT_INPUT_COPY_USER_EMAIL").setValueStateText("");
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_PASS').setValue("");
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_PASSCN').setValue("");
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_STATUS').setState(true);
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_ACCT_DETAILS').setSelected(true);
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_ACCT_PREF').setSelected(true);
			sap.ui.getCore().byId('ACCT_MNT_INPUT_COPY_USER_ACCT_AUTH').setSelected(true);

		break;

	}
}

//===========================
// MANAGE USER GUIDE
//===========================
function fn_init_create_user_guide_redactor(){

	// $('#CREATE_USER_GUIDE_TEXTAREA-inner').redactor('core.destroy');
	var editor = tinymce.get('CREATE_USER_GUIDE_TEXTAREA-inner');

					if(editor !== null){
							editor.remove();
					}
    go_App_Right.to('PAGE_RIGHT_CREATE_USER_GUIDE');

    setTimeout(function(){

	    // 	$('#CREATE_USER_GUIDE_TEXTAREA-inner').redactor({
			// 	minHeight: 300,
			// 	maxHeight: 300,
			// 	imageUpload:GURL_REDACTOR_IMAGE,
			// 	fileUpload: GURL_REDACTOR_FILE,
			// 	plugins: ['inlinestyle','alignment','table','underline'],
			// });

			tinymce.init({
				selector: '#CREATE_USER_GUIDE_TEXTAREA-inner',
				height:"500px",
				plugins: [
						'save fullscreen print advlist autolink lists link image charmap preview hr anchor pagebreak',
						'searchreplace wordcount visualblocks visualchars code',
						'insertdatetime media nonbreaking table directionality',
						'emoticons template paste textpattern imagetools codesample noneditable'
				],
				toolbar: 'fullscreen codesample | bold italic  fontselect fontsizeselect | hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image |  undo redo | forecolor backcolor emoticons | code',
				fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
				paste_data_images: true,
				images_upload_url: '/admin/api/tinymce/upload',
				entity_encoding : "named",
				noneditable_noneditable_class: "mceNonEditable",
				relative_urls: false,
				setup:function(editor) {
					//Callback before instance
				},
				init_instance_callback : function(editor) {
					console.log("Editor: " + editor.id + " is now initialized.");
					 editor.on("change", function(e){
							ui('CREATE_USER_GUIDE_TEXTAREA').setValue(editor.getContent());
						});
					editor.on("keyup", function(){
							ui('CREATE_USER_GUIDE_TEXTAREA').setValue(editor.getContent());
					});
					if(gv_mode == 'display'){
							editor.setMode('readonly');
						}else{
							editor.setMode('design');
						}
				}
				});

	},100);

}

function fn_create_user_guide_set_default(){

    var lv_startdate = new Date();
    var lv_enddate = new Date();

    sap.ui.getCore().byId("CREATE_USER_GUIDE_TITLE").setValue("");
    sap.ui.getCore().byId("CREATE_USER_GUIDE_LANGUAGE").setSelectedKey('E');
    sap.ui.getCore().byId("CREATE_USER_GUIDE_DATE").setDateValue(lv_startdate);
    sap.ui.getCore().byId("CREATE_USER_GUIDE_DATE").setSecondDateValue(lv_enddate);
    sap.ui.getCore().byId("CREATE_USER_GUIDE_TEXTAREA").setValue("");

}

function fn_edit_user_guide_set_default(){

    var lv_startdate = new Date();
    var lv_enddate = new Date();

    sap.ui.getCore().byId("EDIT_USER_GUIDE_TITLE").setValue("");
    sap.ui.getCore().byId("EDIT_USER_GUIDE_LANGUAGE").setSelectedKey('E');
    sap.ui.getCore().byId("EDIT_USER_GUIDE_DATE").setDateValue(lv_startdate);
    sap.ui.getCore().byId("EDIT_USER_GUIDE_DATE").setSecondDateValue(lv_enddate);
    sap.ui.getCore().byId("EDIT_USER_GUIDE_TEXTAREA").setValue("");

}

function fn_display_selected_user_guide(lv_value){

    var lv_startdate	= new Date(lv_value.START_DATE);
    var lv_enddate		= new Date(lv_value.END_DATE);

    sap.ui.getCore().byId("EDIT_USER_GUIDE_TITLE").setValue(lv_value.TITLE);
    sap.ui.getCore().byId("EDIT_USER_GUIDE_LANGUAGE").setSelectedKey(lv_value.LANGUAGE);
    sap.ui.getCore().byId("EDIT_USER_GUIDE_STATUS").setState(lv_value.STATE);
    sap.ui.getCore().byId("EDIT_USER_GUIDE_DATE").setDateValue(lv_startdate);
    sap.ui.getCore().byId("EDIT_USER_GUIDE_DATE").setSecondDateValue(lv_enddate);
    sap.ui.getCore().byId("EDIT_USER_GUIDE_APP_ID").setSelectedKey(lv_value.APP_ID);

    var lv_item;

	ui('EDIT_USER_GUIDE_SUB_APP').destroyItems();

	console.log(lv_value.APP_ID);
	for(var i =0; i < gt_glbmfunctiontxt.length; i++){

		if(lv_value.APP_ID == gt_glbmfunctiontxt[i].TSTC){

			lv_item = new sap.ui.core.Item({
				text	: gt_glbmfunctiontxt[i].description,
				key		: gt_glbmfunctiontxt[i].ID
			});

			ui('EDIT_USER_GUIDE_SUB_APP').addItem(lv_item);

		}
	}

	sap.ui.getCore().byId("EDIT_USER_GUIDE_SUB_APP").setSelectedKey(lv_value.SUB_APP);
	sap.ui.getCore().byId("EDIT_USER_GUIDE_TEXTAREA").setValue(lv_value.DESCRIPTION);

	// $('#EDIT_USER_GUIDE_TEXTAREA-inner').redactor('core.destroy');
	var editor = tinymce.get('EDIT_USER_GUIDE_TEXTAREA-inner');

					if(editor !== null){
							editor.remove();
					}
	go_App_Right.to('PAGE_RIGHT_EDIT_USER_GUIDE');

    setTimeout(function(){

	    // 	$('#EDIT_USER_GUIDE_TEXTAREA-inner').redactor({
			// 	minHeight: 300,
			// 	maxHeight: 300,
			// 	imageUpload:GURL_REDACTOR_IMAGE,
			// 	fileUpload: GURL_REDACTOR_FILE,
			// 	plugins: ['inlinestyle','alignment','table','underline'],
			// });

			tinymce.init({
				selector: '#EDIT_USER_GUIDE_TEXTAREA-inner',
				height:"500px",
				plugins: [
						'save fullscreen print advlist autolink lists link image charmap preview hr anchor pagebreak',
						'searchreplace wordcount visualblocks visualchars code',
						'insertdatetime media nonbreaking table directionality',
						'emoticons template paste textpattern imagetools codesample noneditable'
				],
				toolbar: 'fullscreen codesample | bold italic  fontselect fontsizeselect | hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image |  undo redo | forecolor backcolor emoticons | code',
				fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
				paste_data_images: true,
				images_upload_url: '/admin/api/tinymce/upload',
				entity_encoding : "named",
				noneditable_noneditable_class: "mceNonEditable",
				relative_urls: false,
				setup:function(editor) {
					//Callback before instance
				},
				init_instance_callback : function(editor) {
					console.log("Editor: " + editor.id + " is now initialized.");
					 editor.on("change", function(e){
							ui('EDIT_USER_GUIDE_TEXTAREA').setValue(editor.getContent());
						});
					editor.on("keyup", function(){
							ui('EDIT_USER_GUIDE_TEXTAREA').setValue(editor.getContent());
					});
					if(gv_mode == 'display'){
							editor.setMode('readonly');
						}else{
							editor.setMode('design');
						}
				}
				});

	},100);
}

	function fn_split_array_to_string(lt_array){
		var lv_string = "";
		for(var a=0; a < lt_array.length; a++){
			 lv_string += lt_array[a];
			 if( (a+1) == lt_array.length - 1){
			  lv_string += " and ";
			 } else if( (a+1) < lt_array.length ){
			  lv_string += ", ";
			 }
		}
		return lv_string;
	}

	function fn_add_object_maintenance(){

		gt_glbmobject.push({
			object: "",
			description: "",
			enabled: true
		})

		fn_bind_object_maintenance_inp(gt_glbmobject);
	}

/*
|--------------------------------------------------------------------------
| GET DESCRIPTION
|--------------------------------------------------------------------------
*/
	function fn_get_desc(lv_value, lv_array){

		var lv_desc = "";

		lv_value = lv_value.trim();

		for(var i = 0; i < lv_array.length; i++){

			if(lv_array[i].ID.toUpperCase() == lv_value.toUpperCase()){

				lv_desc = lv_array[i].description;
				break;

			}

		}
		return lv_desc;
	}

	function fn_get_desc_with_field(lv_id, lv_description, lv_value, lv_array){

		var lv_desc = "";

		lv_value = lv_value.trim();

		for(var i = 0; i < lv_array.length; i++){

			if(lv_array[i][lv_id].toUpperCase() == lv_value.toUpperCase()){

				lv_desc = lv_array[i][lv_description];
				break;

			}

		}
		return lv_desc;
	}
/*
|--------------------------------------------------------------------------
| GET IF EXISTING - USING ID
|--------------------------------------------------------------------------
*/

	function fn_check_if_exists(lv_field,lv_array){

		var found = lv_array.some(function (el) {
      		return el.ID.toUpperCase() === lv_field.toUpperCase();
    	});

    	return found;

	}

	function fn_check_object_if_exists(lv_field,lv_array){

		var lv_count = 0;
		var found = false;
		for(var i=0; i<lv_array.length; i++){
			if(lv_array[i].object.toUpperCase() === lv_field.toUpperCase()){
				lv_count++;
			}
		}

		if(lv_count > 1){
			found = true;
		}else{
			found = false;
		}

    	return found;

	}

	function fn_check_object_table_before_save(){

		lv_error = false;

		var lt_object_table_items = ui("OBJECT_MAINTENANCE_TABLE").getModel();
		var lv_count = 0;

		if(lt_object_table_items.length > 0){

			for(var i=0; i < lt_object_table_items.length; i++){
				if(lt_object_table_items[i].getCells()[0].getValueState() == "Error"){
					lv_count++;
				}
			}

			if(lv_count > 0){
				lv_error = true;
			}else{
				lv_error = false;
			}

		}else{
			lv_error = false;
		}

		return lv_error;

	}

	function fn_check_table_before_save(lv_table,lv_cell_no){

		lv_error = false;

		var lt_table_items = ui(lv_table).getModel();
		var lv_count = 0;

		if(lt_table_items.length > 0){

			for(var i=0; i < lt_table_items.length; i++){
				if(lt_table_items[i].getCells()[lv_cell_no].getValueState() == "Error"){
					lv_count++;
				}
			}

			if(lv_count > 0){
				lv_error = true;
			}else{
				lv_error = false;
			}

		}else{
			lv_error = false;
		}

		return lv_error;

	}

	function fn_check_if_exists_with_field(lv_value,lv_array,lv_field){

		var found = lv_array.some(function (el) {
      		return el[lv_field].toUpperCase() === lv_value.toUpperCase();
    	});

    	return found;

	}

	function fn_check_if_exists_with_field_id(lv_value,lv_array,lv_field){

		var found = lv_array.some(function (el) {
      		return el[lv_field] === lv_value;
    	});

    	return found;

	}

	function fn_validate_assignment_table(lv_value){

		var lv_isvalid = false
		var lv_found = false;
		var lv_count = 0;
		console.log(lv_value);

		if(typeof(gt_applications_data_index[lv_value.toUpperCase()]) != "undefined"){
			lv_isvalid = true;
		}

		console.log(lv_isvalid);

		if(lv_isvalid){
			for(var i=0; i<gt_assignment_bk.length; i++){
				if(gt_assignment_bk[i].CONTENT.toUpperCase() === lv_value.toUpperCase()){
					lv_found = true;
					break;
				}
			}
		}else{
			lv_found = true;
		}

		console.log(lv_found);

    	return lv_found;

	}

	function fn_filter_user_listing(lv_searchval){

		var filters = new Array();
		var lv_search_key = sap.ui.getCore().byId('SEARCH_SELECT_ACCT_MAINT').getSelectedKey();

		var oFilter = new sap.ui.model.Filter(lv_search_key,sap.ui.model.FilterOperator.Contains,lv_searchval);

		filters.push(oFilter);

		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(gt_USER_DATA.length);
			lo_model.setData(gt_USER_DATA);


		var lo_table = $('#GO_TABLE_ACCT_MAINT-listUl');
			lo_table.floatThead('reflow');

		var table = sap.ui.getCore().byId('GO_TABLE_ACCT_MAINT');
		var lo_template = table.getBindingInfo("items").template;

		table.setModel(lo_model).bindAggregation("items", {
			path: "/",
			template: lo_template,
			filters:filters
		});

		var lv_list_length = table.getBinding("items").iLength;

		sap.ui.getCore().byId('LABEL_ITEM_USER_MTN').setText("Items ("+lv_list_length+")");

		setTimeout(function(){fn_freeze_table_header();}, 500);

	}

/*
|--------------------------------------------------------------------------
| SHOW MESSAGE STRIP
|--------------------------------------------------------------------------
*/
	function fn_show_message_strip(lv_message){
		return new sap.m.MessageStrip({
			text: lv_message,
			type: "Error",
			showIcon: true,
			showCloseButton: true,
			close:function(){

				ui("MESSAGE_STRIP_ACCT_MAINTENANCE").destroyContent();
				ui("MESSAGE_STRIP_ACCT_MAINTENANCE").setVisible(false);
			}
		});
	}

	function fn_bind_invite_listing(data){

		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);

		ui("GO_TABLE_INVITE_LISTING").setModel(lo_model).bindRows('/');
		ui("GO_TABLE_INVITE_LISTING_LABEL").setText("Invite List ("+data.length+")");
		fn_clear_table_sorter("GO_TABLE_INVITE_LISTING");
		go_SplitContainer.setShowSecondaryContent(false);
	}

	function fn_set_invite_conf_data_mode(data,mode,callback){

		if(mode == "display"){

			var lv_edit = false;
			var lv_display = true;
			ui("INVITE_CONF_EDIT").setVisible(lv_display);
			ui("INVITE_CONF_ADD").setVisible(lv_edit);
			ui("INVITE_CONF_SAVE").setVisible(lv_edit);
			ui("INVITE_CONF_DECLINE").setVisible(lv_edit);
			ui("GO_TABLE_INVITE_CONF").getColumns()[10].setVisible(lv_edit);

		}
		else{
			var lv_edit = true;
			var lv_display = false;
			ui("INVITE_CONF_EDIT").setVisible(lv_display);
			ui("INVITE_CONF_ADD").setVisible(lv_edit);
			ui("INVITE_CONF_SAVE").setVisible(lv_edit);
			ui("INVITE_CONF_DECLINE").setVisible(lv_edit);
			ui("GO_TABLE_INVITE_CONF").getColumns()[10].setVisible(lv_edit);
		}

		data.forEach(function(key){

			gv_invite_conf_vhelp.forEach(function(i){
				if(i.ID == key.STATUS){
					key.STATUS_DESC = i.description;
				}
			});

			gv_mail_obj_type_vhelp.forEach(function(i1){
				if(i1.ID == key.MAIL_OBJ_TYPE){
					key.MAIL_OBJ_TYPE = i1;
				}
			});
			
			if(gv_mail_obj_id_vhelp != ""){
				if(gv_mail_obj_id_vhelp[key.MAIL_OBJ_TYPE] != undefined){
					gv_mail_obj_id_vhelp[key.MAIL_OBJ_TYPE].forEach(function(i2){
						if(i2.ID == key.MAIL_OBJ_ID){
							key.MAIL_OBJ_ID = i2.ID;
						}
					});
				}
			}

			if(gv_mail_event_type_vhelp != ""){
				if(gv_mail_event_type_vhelp[key.MAIL_OBJ_ID] != undefined){
					gv_mail_event_type_vhelp[key.MAIL_OBJ_ID].forEach(function(i3){
						if(i3.ID == key.MAIL_EVENT_TYPE){
							key.MAIL_EVENT_TYPE = i3.ID;
						}
					});
				}
			}

			switch(key.SEND_EMAIL){
				case "X":
					key.SEND_EMAIL_STATE = true;
				break;
				default :
					key.SEND_EMAIL_STATE = false;
			}

			key.EXPIRY_HR = Math.floor(key.EXPIRY / 60);

			// if(mode == "display"){

			key.INVITE_ID_EDITABLE = false;
			key.INVITE_DESC_EDITABLE = lv_edit;
			key.STATUS_EDITABLE = lv_edit;
			key.EXPIRY_EDITABLE = lv_edit;
			key.CRT_USERNAME_EDITABLE = lv_edit;
			key.SEND_EMAIL_ENABLED = lv_edit;
			key.STATUS_VISIBLE_EDT = lv_edit;
			key.STATUS_VISIBLE_DSP = lv_display;
			key.MAIL_OBJ_TYPE_VISIBLE_EDT = lv_edit;
			key.MAIL_OBJ_TYPE_VISIBLE_DSP = lv_display;
			key.MAIL_OBJ_ID_VISIBLE_EDT = lv_edit;
			key.MAIL_OBJ_ID_VISIBLE_DSP = lv_display;
			key.MAIL_EVENT_TYPE_VISIBLE_EDT = lv_edit;
			key.MAIL_EVENT_TYPE_VISIBLE_DSP = lv_display;
			// }
			// else if(mode == "edit"){

			// 	key.INVITE_ID_EDITABLE = false;
			// 	key.INVITE_DESC_EDITABLE = true;
			// 	key.STATUS_EDITABLE = true;
			// 	key.EXPIRY_EDITABLE = true;
			// 	key.CRT_USERNAME_EDITABLE = true;
			// 	key.SEND_EMAIL_ENABLED = true;
			// 	key.STATUS_VISIBLE_EDT = true;
			// 	key.STATUS_VISIBLE_DSP = false;
			// 	key.MAIL_OBJ_TYPE_VISIBLE_EDT = true;
			// 	key.MAIL_OBJ_TYPE_VISIBLE_DSP = false;
			// 	key.MAIL_OBJ_ID_VISIBLE_EDT = true;
			// 	key.MAIL_OBJ_ID_VISIBLE_DSP = false;
			// 	key.MAIL_EVENT_TYPE_VISIBLE_EDT = true;
			// 	key.MAIL_EVENT_TYPE_VISIBLE_DSP = false;
			// }
		});
		callback(data);
	}

	function fn_generate_backup_data(){

		lt_invite_conf_bk = [];
		lt_invite_roles_bk = [];

		gt_invite_conf.forEach(function(key){
			lt_invite_conf_bk[key.ID] = key;
		});

		gt_invite_roles.forEach(function(key1){
			lt_invite_roles_bk[key1.ID] = key1;
		});

		gt_invite_conf_bk = JSON.parse(JSON.stringify(lt_invite_conf_bk));
		gt_invite_roles_bk = JSON.parse(JSON.stringify(lt_invite_roles_bk));
	}

	function fn_check_data(){

		var lv_invite_conf_create = [];
		var lv_invite_conf_update = [];
		var lv_invite_roles_create = [];
		var lv_invite_roles_update = [];

		for(var i=0;i<gt_invite_conf.length;i++){
			
			if(gt_invite_conf[i].ID == "" && gt_invite_conf[i].DEL_FLAG == ""){

				lv_invite_conf_create.push({

					ID 					: gt_invite_conf[i].ID,
					INVITE_ID 			: gt_invite_conf[i].INVITE_ID,
					DESCRIPTION 		: gt_invite_conf[i].DESCRIPTION,
					STATUS 				: gt_invite_conf[i].STATUS,
					EXPIRY 				: gt_invite_conf[i].EXPIRY,
					CREATE_USERNAME 	: gt_invite_conf[i].CREATE_USERNAME,
					SEND_EMAIL 			: gt_invite_conf[i].SEND_EMAIL,
					MAIL_OBJ_TYPE 		: gt_invite_conf[i].MAIL_OBJ_TYPE,
					MAIL_OBJ_ID 		: gt_invite_conf[i].MAIL_OBJ_ID,
					MAIL_EVENT_TYPE 	: gt_invite_conf[i].MAIL_EVENT_TYPE,
					DEL_FLAG 			: gt_invite_conf[i].DEL_FLAG

				});
			}
			else{
				if(gt_invite_conf[i].ID != ""){
					var lv_id = gt_invite_conf[i].ID;
					if(
						gt_invite_conf[i].DESCRIPTION != gt_invite_conf_bk[lv_id].DESCRIPTION ||
						gt_invite_conf[i].STATUS != gt_invite_conf_bk[lv_id].STATUS ||
						gt_invite_conf[i].EXPIRY != gt_invite_conf_bk[lv_id].EXPIRY ||
						gt_invite_conf[i].CREARE_USERNAME != gt_invite_conf_bk[lv_id].CREARE_USERNAME ||
						gt_invite_conf[i].SEND_EMAIL != gt_invite_conf_bk[lv_id].SEND_EMAIL ||
						gt_invite_conf[i].MAIL_OBJ_TYPE != gt_invite_conf_bk[lv_id].MAIL_OBJ_TYPE ||
						gt_invite_conf[i].MAIL_OBJ_ID != gt_invite_conf_bk[lv_id].MAIL_OBJ_ID ||
						gt_invite_conf[i].MAIL_EVENT_TYPE != gt_invite_conf_bk[lv_id].MAIL_EVENT_TYPE ||
						gt_invite_conf[i].DEL_FLAG != gt_invite_conf_bk[lv_id].DEL_FLAG
					){
						lv_invite_conf_update.push({

							ID 					: gt_invite_conf[i].ID,
							INVITE_ID 			: gt_invite_conf[i].INVITE_ID,
							DESCRIPTION 		: gt_invite_conf[i].DESCRIPTION,
							STATUS 				: gt_invite_conf[i].STATUS,
							EXPIRY 				: gt_invite_conf[i].EXPIRY,
							CREATE_USERNAME 	: gt_invite_conf[i].CREATE_USERNAME,
							SEND_EMAIL 			: gt_invite_conf[i].SEND_EMAIL,
							MAIL_OBJ_TYPE 		: gt_invite_conf[i].MAIL_OBJ_TYPE,
							MAIL_OBJ_ID 		: gt_invite_conf[i].MAIL_OBJ_ID,
							MAIL_EVENT_TYPE 	: gt_invite_conf[i].MAIL_EVENT_TYPE,
							DEL_FLAG 			: gt_invite_conf[i].DEL_FLAG

						});
					}
				}
			}
		}

		for(var i=0;i<gt_invite_roles.length;i++){

			if(gt_invite_roles[i].ID == "" && gt_invite_roles[i].DEL_FLAG == ""){

				lv_invite_roles_create.push({

					ID 					: gt_invite_roles[i].ID,
					INVITE_ID 			: gt_invite_roles[i].INVITE_ID,
					ROLE 				: gt_invite_roles[i].ROLE,
					DESCRIPTION 		: gt_invite_roles[i].DESCRIPTION,
					VALID_FR 			: gt_invite_roles[i].VALID_FR,
					VALID_TO 			: gt_invite_roles[i].VALID_TO,
					STATUS 				: gt_invite_roles[i].STATUS,
					DEL_FLAG 			: gt_invite_roles[i].DEL_FLAG

				});
			}
			else{
				if(gt_invite_roles[i].ID != ""){
					var lv_id = gt_invite_roles[i].ID;
					if(
						gt_invite_roles[i].ROLE != gt_invite_roles_bk[lv_id].ROLE ||
						gt_invite_roles[i].DESCRIPTION != gt_invite_roles_bk[lv_id].DESCRIPTION ||
						gt_invite_roles[i].VALID_FR != gt_invite_roles_bk[lv_id].VALID_FR ||
						gt_invite_roles[i].VALID_TO != gt_invite_roles_bk[lv_id].VALID_TO ||
						gt_invite_roles[i].STATUS != gt_invite_roles_bk[lv_id].STATUS ||
						gt_invite_roles[i].DEL_FLAG != gt_invite_roles_bk[lv_id].DEL_FLAG
					){
						lv_invite_roles_update.push({

							ID 					: gt_invite_roles[i].ID,
							INVITE_ID 			: gt_invite_roles[i].INVITE_ID,
							ROLE 				: gt_invite_roles[i].ROLE,
							DESCRIPTION 		: gt_invite_roles[i].DESCRIPTION,
							VALID_FR 			: gt_invite_roles[i].VALID_FR,
							VALID_TO 			: gt_invite_roles[i].VALID_TO,
							STATUS 				: gt_invite_roles[i].STATUS,
							DEL_FLAG 			: gt_invite_roles[i].DEL_FLAG
						});
					}
				}
			}
		}

		return {

			invite_conf_create : lv_invite_conf_create,
			invite_conf_update : lv_invite_conf_update,
			invite_roles_create : lv_invite_roles_create,
			invite_roles_update : lv_invite_roles_update
		};
	}

	function fn_bind_invite_conf(data){

		var lo_model = new sap.ui.model.json.JSONModel();
			lo_model.setSizeLimit(data.length);
			lo_model.setData(data);

		ui("GO_TABLE_INVITE_CONF").setModel(lo_model).bindRows('/');
		ui("GO_TABLE_INVITE_CONF").getBinding("rows").filter(
			new sap.ui.model.Filter("DEL_FLAG", sap.ui.model.FilterOperator.NE,"X"),
			"Application"
		);
		ui("GO_TABLE_INVITE_CONF_LABEL").setText("Invite Config List ("+data.length+")");
		fn_clear_table_sorter("GO_TABLE_INVITE_CONF");
	}

	function fn_check_if_existsv2(lv_field,lv_property,lt_array){

		var found = lt_array.some(function (el) {
      		return el[lv_property].toUpperCase() === lv_field.toUpperCase();
    	});
    	return found;
	}

	function fn_download_invite_listing(){

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		var lv_contexts = [];
		var lt_content = [];

		var lv_table = ui('GO_TABLE_INVITE_LISTING');
		var lv_model = lv_table.getModel();
		var lv_data = lv_model.getData();
		var lv_indices = lv_table.getBinding().aIndices;

		for(var i = 0; i < lv_indices.length; i++){

			lv_contexts = lv_data[lv_indices[i]];
			lt_content.push(lv_contexts);
		}

		var lt_temp = [];

		for(var i = 0; i < lt_content.length; i++){

			lt_temp.push({

				"Invite"   		: lt_content[i].DESCRIPTION,
				"Email"			: lt_content[i].EMAIL ,
				"Status" 		: lt_content[i].STATUS_DESC,
				"Created On"    : lt_content[i].CREATED_AT_DSP,
				"Created By" 	: lt_content[i].created_by,
				"Accepted On"  	: lt_content[i].ACCEPTED_AT_DSP,
				"Expired On"  	: lt_content[i].EXPIRY_DSP,
				"Deactivate On"	: lt_content[i].DEACTIVATE_AT_DSP,
				"Deactivate By" : lt_content[i].DEACTIVATE_BY,
				"Token" 		: lt_content[i].TOKEN,
			});

		}

		var lv_array_date = typeof lt_temp != 'object' ? JSON.parse(lt_temp) : lt_temp;

		var CSV = "\uFEFF";

		var ShowLabel = true;

		//This condition will generate the Label/Header
		if (ShowLabel) {
			var row = "";
			var modified ="";


			//This loop will extract the label from 1st index of on array
			for (var index in lv_array_date[0]) {

				//covert underscore to space
				modified = index.replace(/_/g, ' ');
				//Now convert each value to string and comma-seprated
				row += modified + ',';
			}

			row = row.slice(0, -1);

			//append Label row with line break
			CSV += row + '\r\n';
		}

		//1st loop is to extract each row
		for (var i = 0; i < lv_array_date.length; i++) {
			var row = "";

			//2nd loop will extract each column and convert it in string comma-seprated
			for (var index in lv_array_date[i]) {
				row += '"' + lv_array_date[i][index] +'",';
				//row += lv_array_date[i][index] +',';
			}

			//remove 1st row
			row.slice(0, row.length - 1);

			//remove last comma
			row = row.replace(/,\s*$/, "");

			//add a line break after each row
			CSV += row + '\r\n';
		}

		if (CSV == '') {
			console.log("Invalid data");
			return;
		}

		//Generate a file name
		var fileName = "";
		var downloadedDate = get_ClientDate();
		//this will remove the blank-spaces from the title and replace it with an underscore
		fileName = "Invite Listing".replace(/ /g,"_") + downloadedDate;


		if(window.navigator.msSaveOrOpenBlob){

			blobObject = new Blob([CSV]);
			window.navigator.msSaveOrOpenBlob(blobObject, fileName + ".csv");

		}else if(navigator.userAgent.search("Trident") >= 0){

			var IEwindow = window.open("application/csv", "replace");
				IEwindow.document.write('sep=,\r\n' + CSV);
				IEwindow.document.close();
				IEwindow.document.execCommand('SaveAs', true, fileName + ".csv");
				IEwindow.close();

		}else{

			//Initialize file format you want csv or xls
			//var uri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(CSV);
			var uri = new Blob([ CSV ], { type : "application/csv;charset=utf-8;" });
			var csvUrl = URL.createObjectURL(uri);

			var link = document.createElement("a");
			link.href = csvUrl;
			link.setAttribute("target", "_blank");

			link.style = "visibility:hidden";
			link.download = fileName + ".csv";

			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);

		}

		busy_diag.close();
	}


	 function fn_generate_msge_strip(vbox,msg_index,msg_text,isvisible){
  	if(typeof(gt_message_strip["MSTRIPNWCRT_"+msg_index]) == "undefined"){
  		gt_message_strip["MSTRIPNWCRT_"+msg_index] = msg_text;
  		ui(vbox).addItem(new sap.m.MessageStrip("MSTRIPNWCRT_"+msg_index,{
  				text: msg_text,
  				type: "Error",
  				showIcon: true,
  				showCloseButton: true,
  				visible:isvisible
  		}));
  	}else{
  		ui("MSTRIPNWCRT_"+msg_index).setText(msg_text);
  		ui("MSTRIPNWCRT_"+msg_index).setVisible(isvisible);
  	}
  }

  	//06-07-2019-start
  	function fn_bind_valuehelp_user_roles (lv_id) {

		gv_confirm_input = lv_id;

		$.ajax({
			type:"GET",
			url: "{{ url('admin/user_roles/get_users_roles_status') }}",
			dataType:"json",
			contentType: "application/json; charset=utf-8",
		}).done(function(data){

			var lo_model = new sap.ui.model.json.JSONModel();
				lo_model.setSizeLimit(data.length);
				lo_model.setData(data.users);

				ui('DIALOG_VALUEHELP').setModel(lo_model).bindAggregation("items", {
					path:"/",
					template:ui('DIALOG_VALUEHELP_ITEMS')
				});

				ui("DIALOG_VALUEHELP").setTitle("User ID");
				ui("DIALOG_VALUEHELP").open();

		});
			
	}

	new sap.m.SelectDialog("DIALOG_VALUEHELP",{
		title:"",
		items:[

		],
		liveChange:function(oEvt){
			var filter = [];
			var sVal = oEvt.getParameter("value");
			var sEventType = oEvt.getParameter("eventType");
			var selectCtr = lv_select_valuehelp.getSelectedKey();
			if(sVal !== undefined) {
				var itemsBinding = oEvt.getParameter("itemsBinding");
				var selectFilter = new sap.ui.model.Filter(selectCtr, sap.ui.model.FilterOperator.Contains , sVal);
				filter.push(selectFilter);
				itemsBinding.filter(filter);
			}
		},
		search:function(oEvt){
			var filter = [];
			var sVal = oEvt.getParameter("value");
			var sEventType = oEvt.getParameter("eventType");
			var selectCtr = lv_select_valuehelp.getSelectedKey();
			if(sVal !== undefined) {
				var itemsBinding = oEvt.getParameter("itemsBinding");
				var selectFilter = new sap.ui.model.Filter(selectCtr, sap.ui.model.FilterOperator.Contains , sVal);
				filter.push(selectFilter);
				itemsBinding.filter(filter);
			}
		},
		confirm:function(oEvt){
			var selectedItem = oEvt.getParameters().selectedItem.getTitle();
			ui(gv_confirm_input).setValue(selectedItem);
			ui(gv_confirm_input).fireChange();
		}

	}).addStyleClass("sapUiSizeCompact");

	var lv_select_valuehelp = new sap.m.Select("CREATE_KEY_SELECTED",{
		width:"130px",
		selectedKey:"ID",
		items:[
			new sap.ui.core.Item({key:"USER_ID", text:"User ID"}),
			new sap.ui.core.Item({key:"USER_NAME", text:"Username"})
		],
		change:function(oEvt){
			ui("DIALOG_VALUEHELP").fireLiveChange();
		},
	});

	setTimeout( function(){
		sap.ui.getCore().byId("DIALOG_VALUEHELP-subHeader").addContentLeft(sap.ui.getCore().byId("DIALOG_VALUEHELP-searchField"));
		sap.ui.getCore().byId("DIALOG_VALUEHELP-subHeader").addContentRight(lv_select_valuehelp);
		sap.ui.getCore().byId("DIALOG_VALUEHELP-cancel").setIcon("sap-icon://decline");
		sap.ui.getCore().byId("DIALOG_VALUEHELP-cancel").setType(sap.m.ButtonType.Reject);
		sap.ui.getCore().byId("DIALOG_VALUEHELP-dialog-header").addContentRight(new sap.m.Button({icon: "sap-icon://decline",type:sap.m.ButtonType.Reject,press:function(evt){ sap.ui.getCore().byId("DIALOG_VALUEHELP-cancel").firePress(); }}));
		sap.ui.getCore().byId("DIALOG_VALUEHELP-cancel").setVisible(false);
		sap.ui.getCore().byId("DIALOG_VALUEHELP-searchField").setPlaceholder("Search..");
	}, 100);


	var lo_template = new sap.m.StandardListItem("DIALOG_VALUEHELP_ITEMS",{
		type:"Active",
		title:"{USER_ID}",
		description:"{USER_NAME}"
	});

	function fn_check_if_exists_user_roles(lv_field,lv_array){

		var found = lv_array.some(function (el) {
			return el.toUpperCase() === lv_field.toUpperCase();
		});

		return found;

	}

	function fn_check_select_fields_user_roles(lv_value, lv_id_from, lv_id_to, lv_type, lt_array, lv_id){
		
		var lv_error = 0;

		ui(lv_id_from).setValueState("None");
		ui(lv_id_from).setValueStateText("");
		ui(lv_id_to).setValueState("None");
		ui(lv_id_to).setValueStateText("");


		if(lv_value !== ""){

			if(lv_type !== "DATE"){
				//check if exists
				if(fn_check_if_exists_user_roles(lv_value,lt_array)){
					
				}else{
					ui(lv_id).setValueState("Error");
					lv_error++;
				}
			}
			
			if(lv_error == 0){
				//both has value
				if(ui(lv_id_from).getValue() !== "" && ui(lv_id_to).getValue() !== ""){

					switch (lv_type){
						case "NUMBER" : {
							var lv_value1 = isNaN(parseInt(ui(lv_id_from).getValue())) ? 0 :parseInt(ui(lv_id_from).getValue());
							var lv_value2 = isNaN(parseInt(ui(lv_id_to).getValue())) ? 0 :parseInt(ui(lv_id_to).getValue());

						}break;
						case "DATE" : {
							var lv_value1 = Date.parse(ui(lv_id_from).getValue());
							var lv_value2 = Date.parse(ui(lv_id_to).getValue());

						}break;
						default:{
							console.log('1');
							var lv_value1 = ui(lv_id_from).getValue().trim().toLowerCase();
							var lv_value2 = ui(lv_id_to).getValue().trim().toLowerCase();
						}
					}

					if(lv_value2 <= lv_value1){
						ui(lv_id_to).setValueState("Error");
						fn_show_notification_message("Lower limit is greater than upper limit.");
						lv_error++;
					}else{

					}
				}
			}

			if(lv_error == 0){
				//the to has value while the from don't have
				if(ui(lv_id_from).getValue() == "" && ui(lv_id_to).getValue() !== ""){
					ui(lv_id_to).setValueState("Error");
					fn_show_notification_message("Please  enter the from value.");
					lv_error++;
				}
			}


			if(lv_error == 0){
				ui(lv_id_from).setValueState("None");
				ui(lv_id_from).setValueStateText("");
				ui(lv_id_to).setValueState("None");
				ui(lv_id_to).setValueStateText("");
			}

		}else{
			//the to has value while the from don't have
			if(ui(lv_id_from).getValue() == "" && ui(lv_id_to).getValue() !== ""){
				ui(lv_id_to).setValueState("Error");
				fn_show_notification_message("Please  enter the from value.");
			}else{
				ui(lv_id_from).setValueState("None");
				ui(lv_id_from).setValueStateText("");
			}

		}

	}

	function fn_download_user_roles_listing () { 

		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();

		var lv_contexts = [];
		var lt_content = [];

		var lv_table = ui('COE_TABLE_LISTING');

		lt_content = lv_table.getModel().getData();
		var lt_temp = [];

		for(var i = 0; i < lt_content.length; i++){
			lt_temp.push({
				"User ID"			: lt_content[i].USER_ID,
				"Display Name" 		: lt_content[i].name != null ? lt_content[i].name : "",
				"Email" 			: lt_content[i].email != null ? lt_content[i].email : "",
				"User Role" 		: lt_content[i].orig_user_status_text != null ? lt_content[i].orig_user_status_text : "",
				"Role" 				: lt_content[i].ROLE != null ? lt_content[i].ROLE : "",
				"Role Description" 	: lt_content[i].DESCRIPTION != null ? lt_content[i].DESCRIPTION : "",
				"Valid From" 	 	: lt_content[i].VALID_FR != null ? lt_content[i].VALID_FR : "",
				"Valid To" 	 		: lt_content[i].VALID_TO != null ? lt_content[i].VALID_TO : "",
				"Role Status" 	 		: lt_content[i].orig_status_text != null ? lt_content[i].orig_status_text : "",
			});
		}

		var lv_array_date = typeof lt_temp != 'object' ? JSON.parse(lt_temp) : lt_temp;

		var CSV = "\uFEFF";

		var ShowLabel = true;

		//This condition will generate the Label/Header
		if (ShowLabel) {
			var row = "";
			var modified ="";


			//This loop will extract the label from 1st index of on array
			for (var index in lv_array_date[0]) {

				//covert underscore to space
				modified = index.replace(/_/g, ' ');
				//Now convert each value to string and comma-seprated
				row += modified + ',';
			}

			row = row.slice(0, -1);

			//append Label row with line break
			CSV += row + '\r\n';
		}

			//1st loop is to extract each row
			for (var i = 0; i < lv_array_date.length; i++) {
				var row = "";

				//2nd loop will extract each column and convert it in string comma-seprated
				for (var index in lv_array_date[i]) {
					row += '"' + lv_array_date[i][index] +'",';
					//row += lv_array_date[i][index] +',';
				}

				//remove 1st row
				row.slice(0, row.length - 1);

				//remove last comma
				row = row.replace(/,\s*$/, "");

				//add a line break after each row
				CSV += row + '\r\n';
			}

		if (CSV == '') {
			console.log("Invalid data");
			return;
		}

		//Generate a file name
		var fileName = "";
		var downloadedDate = get_ClientDate();
		//this will remove the blank-spaces from the title and replace it with an underscore
		fileName = "user_role_listings".replace(/ /g,"_") + downloadedDate;


		if(window.navigator.msSaveOrOpenBlob){

			blobObject = new Blob([CSV]);
			window.navigator.msSaveOrOpenBlob(blobObject, fileName + ".csv");

		}else if(navigator.userAgent.search("Trident") >= 0){

			var IEwindow = window.open("application/csv", "replace");
				IEwindow.document.write('sep=,\r\n' + CSV);
				IEwindow.document.close();
				IEwindow.document.execCommand('SaveAs', true, fileName + ".csv");
				IEwindow.close();

		}else{

			//Initialize file format you want csv or xls
			//var uri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(CSV);
			var uri = new Blob([ CSV ], { type : "application/csv;charset=utf-8;" });
			var csvUrl = URL.createObjectURL(uri);

			var link = document.createElement("a");
			link.href = csvUrl;
			link.setAttribute("target", "_blank");

			link.style = "visibility:hidden";
			link.download = fileName + ".csv";

			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);

		}

		busy_diag.close();
	}

	//06-07-2019-end
	
	function fn_show_upload_account_review(){
		
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
		
		
		ui('TABLE_USER_REVIEW').setModel(lo_user_model).bindRows("/");
		ui('TABLE_AUTH_REVIEW').setModel(lo_auth_model).bindRows("/");
		ui('TABLE_PARAM_REVIEW').setModel(lo_param_model).bindRows("/");
		ui('TABLE_BIZPART_REVIEW').setModel(lo_bizpart_model).bindRows("/");
		
		ui("LABEL_REVIEW_USER_COUNT").setText("Users (" + gt_validate_user.length + ")");
		ui("LABEL_REVIEW_AUTH_COUNT").setText("Authorization Role (" + gt_validate_auth.length + ")");
		ui("LABEL_REVIEW_PARAM_COUNT").setText("Parameters (" + gt_validate_param.length + ")");
		ui("LABEL_REVIEW_BIZPART_COUNT").setText("BIZ Parners (" + gt_validate_bizpart.length + ")");
		
		
		setTimeout(function(){ui("WIZARD-nextButton").setText("Save");},1);
		ui("WIZARD_STEP_3").setValidated(true);
		
	}
	
	function fn_download_account_upload_template(){
		
		var busy_diag = fn_show_busy_dialog("Please wait. Loading...");
			busy_diag.open();
			
		var wb = XLSX.utils.book_new();
		var fileName = "";
		var downloadedDate = get_ClientDate();
		
		//this will remove the blank-spaces from the title and replace it with an underscore
		fileName = "User_Account_Upload".replace(/ /g,"_") + downloadedDate;  
		wb.Props = {
            Title: "SheetJS Tutorial",
            Subject: "Test",
            Author: "Red Stapler",
            CreatedDate: new Date()
        };
		wb.SheetNames.push("USER");
		wb.SheetNames.push("AUTH");
		wb.SheetNames.push("PARAM");
		wb.SheetNames.push("BIZPART");
		
		var ws_data = [['USERNAME','EMAIL','DISPLAY_NAME','FIRST_NAME','LAST_NAME']];
		var ws = XLSX.utils.aoa_to_sheet(ws_data);	
		wb.Sheets["USER"] = ws;	
		
		var ws_data = [['USERNAME','AUTH_ROLE','VALID_FROM','VALID_TO']];
		var ws = XLSX.utils.aoa_to_sheet(ws_data);	
		wb.Sheets["AUTH"] = ws;
		
		var ws_data = [['USERNAME','PARAM_ID','VALUE']];
		var ws = XLSX.utils.aoa_to_sheet(ws_data);	
		wb.Sheets["PARAM"] = ws;
		
		var ws_data = [['USERNAME','BIZ_PARTNER','PARTNER_NO']];
		var ws = XLSX.utils.aoa_to_sheet(ws_data);	
		wb.Sheets["BIZPART"] = ws;
		
		var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
		
		function s2ab(s) { 
            var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
            var view = new Uint8Array(buf);  //create uint8array as viewer
            for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
            return buf; 
		}
		if(window.navigator.msSaveOrOpenBlob){

			blobObject = new Blob([s2ab(wbout)]);
			window.navigator.msSaveOrOpenBlob(blobObject, fileName + ".xlsx");
					
		}else if(navigator.userAgent.search("Trident") >= 0){
					
			var IEwindow = window.open("application/xlsx", "replace");
				IEwindow.document.write('sep=,\r\n' + s2ab(wbout));
				IEwindow.document.close();
				IEwindow.document.execCommand('SaveAs', true, fileName + ".xlsx");
				IEwindow.close();		
					
		}else{
			
			var uri = new Blob([s2ab(wbout)],{type:"application/octet-stream"});
			var csvUrl = window.URL.createObjectURL(uri);
					 
			var link = document.createElement("a");    
			link.href = csvUrl;
			link.setAttribute("target", "_blank");
			
			link.style = "visibility:hidden";
			link.download = fileName + ".xlsx";
			
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link); 		
		}
			
		busy_diag.close();
	}

</script>
