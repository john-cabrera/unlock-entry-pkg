<script type="text/javascript">

	var gv_Acct_MNT_CRUD_WIDTH		 = "200px";
	var gv_Acct_MNT_CRUD_INPUT_WIDTH = "300px";

	var gv_Acct_MNT_CRUD_INPUT_WIDTH_LEFT = "200px";
	var gv_Acct_MNT_CRUD_INPUT_WIDTH_RIGHT_SUB = "200px";

	var gv_ACCT_MNT_DSP_ICNTABAR_AD_VISBLE = false;

	var gt_USER_DATA = new Array();
	var gt_USER_DATA_INDEX = new Array();

	var gt_user_role = new Array();
	var gt_user_role_INDEX  = new Array();

	var gt_ROLE = new Array();
	var gt_ROLE_INDEX  = new Array();

	var gt_ROLEOBJV = new Array();
	var gt_ROLEOBJV_INDEX  = new Array();

	var gt_COUNTRY = new Array();
	var gt_COUNTRY_INDEX  = new Array();


	var gt_REGION		= new Array();
	var gt_REGION_INDEX = new Array();
	var gt_REGION_SELECT_DIAG = "";

	var gv_roleobjv_role = "";

	var gt_EMPLOYEE_DATA = new Array();
	var gt_EMPLOYEE_DATA_INDEX  = new Array();

	var gt_GLBMOBJECT_DATA = new Array();
	var gt_GLBMOBJECT_DATA_INDEX  = new Array();

	var gt_GLBMFIELDNAME_DATA = new Array();
	var gt_GLBMFIELDNAME_DATA_INDEX  = new Array();

	var gv_mode_role = "Add"; //this is used for user role
	var gv_data_userrole = []; //this is used for saving the id for user role
	var gv_data_roleobject = []; //this is used for saving the id for role object
	var gv_data_userparam = [];

	var gv_selected_role = ""; //this is used in creating new role
	var gv_selected_role_desc = "";	//this is used in creating new role

	var gv_upmoid = "";

	var gt_USER_ID = [];

	var gt_COPY_EMPLOYEE = [];
	var gt_COPY_USER = [];
	var gt_PWDPOLICY = [];
	var gv_input_id = "";
	var gv_selected_catalog = "";
	var gv_selected_assignment = "";
	var gt_catalog = [];
	var gt_valuehelp_tstc = [];
	var gt_glbmfunctiontxt = [];
	var gt_valuehelp_contract_mngmnt_subapp = [];
	var gt_valuehelp_rebate_mngmnt_subapp = [];
	var gt_valuehelp_user_mngmnt_subapp = [];
	var gt_valuehelp_rebate_calc_subapp = [];
	var gt_valuehelp_rebate_prop_subapp = [];
	

	//Begin of insert KorYuen 04/11/2015
	var gt_CLNT_CONF		= [];
	var gt_CLNT_CONF_INDEX	= [];
	var gt_CLNT_CONF_LEFT	= [];
	var gt_CLNT_CONF_RIGHT	= [];
	var gt_CLNT_CONF_RIGHT_COMPARE = [];
	var READ   = "1";
	var CREATE = "2";
	var UPDATE = "3";
	var counter =0;

	var gv_selected_roleid = "";

	var GV_EDITOR_CREATE_COUNTER = 0;
	var GV_EDITOR_EDIT_COUNTER = 0;
	var GV_EDIT_GUID;
	var gv_edit_sub_app = "";
	var gv_edit_app_id = "";
	var gv_selected_id = "";

	var GURL_EDITOR_GET ="/admin/main/get_editor";
	var GURL_EDITOR_POST="/admin/users/management_v2/create_announcement";
	var GURL_EDITOR_POST_EDIT = "/admin/users/management_v2/edit_announcement";
	var GURL_EDITOR_POST_STATE = "/admin/users/management_v2/edit_state_announcement";
	var GURL_REDACTOR_FILE='/admin/epriority/upload/ticket/attachment?_token=' +  '{{ csrf_token() }}';
	var GURL_REDACTOR_IMAGE='/admin/epriority/upload/ticket/attachment?_token=' +  '{{ csrf_token() }}';
	var GURL_EDITOR_GET_LANGUAGE ="";
	var GURL_EDITOR_POST_DELETE ="/admin/users/management_v2/edit_del_announcement";
	var gv_cancel = 'CANCEL';
	var gv_ok     = 'OK';
	var gv_create_ticket = 'CREATE_TICKET';
	
	//manage user guide
	var gt_user_guide = [];
	
	//object maintenance
	var gt_glbmobject = [];
	var gt_glbmobject_deleted = [];
	var gv_deleted_object_index = 0;

	//account management v2
	var gt_list = [];
	var gt_user_info = [];
	var gv_screen_session_csrf_token = "{!! csrf_token() !!}";
	var gt_user_role_deleted = [];
	var gt_user_param_deleted = [];
	var gt_roleobjv_deleted = [];
	var gt_assignment_deleted = [];
	var gt_assignment = [];
	var gv_confirm_input = "";
	var gt_glbmobject_valuehelp = [];
	var gt_glbmfieldname_valuehelp = [];
	var gt_glbmrole_valuehelp = [];
	var gt_glbmparamtxt_valuehelp = [];
	var gt_upload_photo = [];
	var gt_function_valuehelp = [];
	var gt_parameter_valuehelp = [];
	var gt_user_data_bk = [];
	var gt_country_code = [];
	
	var gv_mode = "";
	var gv_flag_cancel_from = "";
	var gv_selected_menu = "";
	var gv_selected_menu_id = "";
	var gv_check_email = true;
	var fn_interval_check_email;
	var gv_partner_type_vhelp = [];
	var gt_biz_partner = [];
	var gt_biz_partner_bk = [];
	var gt_delete_biz_partner = [];
	var gt_partner_no_vhelp = [];

	var go_value_checking = {};
	var gt_invite_conf = [];
	var gt_invite_conf_bk = [];

	var gt_invite_roles = [];
	var gt_invite_roles_bk = [];

	var gv_invite_conf_vhelp = [];
	var gv_invite_role_vhelp = [];
	var gv_roles_vhelp = [];
	var gv_email_vhelp = [];
	var gv_mail_obj_type_vhelp = [];
	var gv_mail_obj_id_vhelp = [];
	var gv_mail_event_type_vhelp = [];

	var gt_message_strip     = [];

	//05-05-2019 start
	var gt_function_text = [];
	var gt_glbmtstc = [];
	var gt_function_txt = [];
	var gv_deleted_fn_txt;
	var gt_fn_txt_backup = [];
	//05-05-2019 end

	var gt_userids = []; //06-07-2019
	
	//End of insert KorYuen 04/11/2015
	
	function ui(element){
		return sap.ui.getCore().byId(element);
	}


	var gt_NEWACCT_INPUT = [
		{INP:"ACCT_MNT_INPT_USERID",ISREQUIRED:true},
		{INP:"ACCT_MNT_INPT_FNAME",ISREQUIRED:false},
		{INP:"ACCT_MNT_INPT_LNAME",ISREQUIRED:false},
		{INP:"ACCT_MNT_INPT_DSPNAME",ISREQUIRED:false},
		{INP:"ACCT_MNT_INPT_EMAIL",ISREQUIRED:true},
	]

	var gt_NEWACCTDSP_INPUT = [
		{INP:"ACCT_MNT_DSP_INPT_USERID",ISREQUIRED:true,ID:"USERID"},
		{INP:"ACCT_MNT_DSP_INPT_FNAME",ISREQUIRED:false,ID:"FNAME"},
		{INP:"ACCT_MNT_DSP_INPT_LNAME",ISREQUIRED:false,ID:"LNAME"},
		{INP:"ACCT_MNT_DSP_INPT_DSPNAME",ISREQUIRED:false,ID:"DSPNAME"},
		{INP:"ACCT_MNT_DSP_INPT_EMAIL",ISREQUIRED:true,ID:"EMAIL"},
	]

	var gt_Global_Message = {
		T01:"Invalid Input",
		T02:"Field is required",
		T03:"Successfully created new user account",
		T04:"Successfully updated user account",
		T05:"Successfully updated user role",
		T06:"Successfully created new role",
		T07:"Successfully updated role object",
		T08:"Successfully added new authorization",
		T09:"Successfully added new role object",
		T10:"Failed to create new role",
		T11:"Failed to create new role object",
		T12: "Failed to add new authorization",
		T13:"Successfully deleted user role",
		T14:"Failed to delete user role",
		T15:"Successfully deleted user role object",
		T16:"Failed to delete user role object",
		T17:"Successfully updated role",
		T18:"Failed to update role",
		T19:"Successfully deleted role",
		T20:"Failed to delete role",
		T21:"Failed to update user role",
		T21:"Successfully created new preference",
		T22:"Failed to create preference",
		T23:"Successfully updated preference",
		T24:"Failed to update preference",
		T25:"Successfully deleted user",
		T26:"Failed to delete user",
	//Begin of insert KorYuen 5/11/2015
		T27:"Successfully update client administration",
		T28:"Fail to update client administration",
	//End of insert KorYuen 5/11/2015
		T29:"Successfully Lock User",
		T30:"Successfully Unlock User",
		T31:"Failed to update user status",
		T32:"Password is required",
		T33:"Password doesn't match",
	//2016.02.18 BEGIN - Nahor added this notification message.
		T34:"Successfully update password policy",
		T35:"Fail to update password policy",
	//2016.02.18 END - Nahor
		T36:"Successfully added new catalog",
		T37:"Failed to create new catalog",
		T38:"Successfully added new application",
		T39:"Failed to create new application",
		T40:"Successfully updated application",
		T41:"Failed to update application",
		T42:"Successfully deleted application",
		T43:"Failed to delete application",
		T44:"Successfully added new assignment",
		T45:"Failed to create new assignment",
		T46:"Successfully updated assignment",
		T47:"Failed to update assignment",
		T48:"Successfully updated catalog",
		T49:"Failed to update catalog",
		T50:"Successfully deleted application",
		T51:"Failed to delete application",
	}	


	function fn_rndom_id(){
		var lv_id = Math.floor((Math.random()*3000)+1) + Math.floor((Math.random()*2000)+1);
		return lv_id;
	}

</script>
