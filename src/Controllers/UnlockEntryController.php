<?php

namespace Emobility\UnlockEntry\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

use Cron\CronExpression;
use Carbon\Carbon;

use Auth;
use DateTimeZone;
use DateTime;
use Log;
use DB;
use AWS;
use Storage;

use App\Classes\AuthorizationClass;
use App\Classes\eventClass;
use App\Classes\ChangeHistory;
use App\Classes\S3Class;
use App\Classes\FileHandlerClass;
use App\Classes\CalendarClass;
use App\Classes\EmailGenerator;

use App\Http\Controllers\Admin\UserAccount\SessionController;

use Emobility\UnlockEntry\Models\glbtlockentry;

class UnlockEntryController extends Controller
{
	public function fn_show_page(){
    	//return page user management;
    	(new eventClass)->writeEvent('1','APPLICATION','unlock_entry','APP_ACCESS','Unlock Entry App');

		$lv_app_id = (new SessionController)->fn_get_appid_based_on_url_v2();

		return view('unlock-entry-views::unlockentry',["APP_ID"=>$lv_app_id]);
		
    }
	
	public function fn_get_entries(){
			
			$lockedDoc = [];
			
			$lockedDoc = glbtlockentry::join('users','users.USERNAME','=','glbtlockentry.USERNAME')
							->select('glbtlockentry.ID', 'glbtlockentry.USERNAME', 'users.NAME', 'glbtlockentry.LOCK_CLASS', 'glbtlockentry.OBJID', 'glbtlockentry.MODE', 'glbtlockentry.created_by', 'glbtlockentry.created_at')
							->where('glbtlockentry.USERNAME','=',Auth::user()->username)
							->get();

			foreach($lockedDoc as $k => $v){
          
                $lockedDoc[$k]['USERNAME'] = trim( $lockedDoc[$k]['NAME']. " (".$lockedDoc[$k]['USERNAME'].")" );
				
                $lockedDoc[$k]['CREATION_DATE'] ="";
                $lockedDoc[$k]['CREATION_TIME'] ="";
                      
                if($v['created_at'] <> ""){
                       
                   $date = new \DateTime($v['created_at']);
                   $date->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));
                   $lockedDoc[$k]['CREATION_DATE'] =$date->format('j M Y');
                   $lockedDoc[$k]['CREATION_TIME'] =$date->format('h:i:s A');
                      
                }
            }		
			
			$return['error'] = false;
			$return['message'] ="All Entries";
			$return['status'] = "01";	
			$return['lockentries'] = $lockedDoc;

		return $return;
	}

	private function fn_get_username(){

		return (Auth::check()) ? Auth::user()->username : 'CRON_JOB';
	}

	
	public function fn_get_authorization(){
        
		$role = new AuthorizationClass();
		$data['object'] = 'CRONJOB_MGT';
        $lt_role_prj = $role->authObject($data);
			
		// get functions or left menu items for this application
        $lv_object = "CRONJOB_MGT";
        $lv_fieldname_apps = "FUNCT";
		$lv_username = Auth::user()->username; 
        
        try{
            $apps = glbcuserrole::join('glbcroleobjv', 'glbcuserrole.ROLE', '=', 'glbcroleobjv.ROLE')
                    ->select('glbcroleobjv.FIELDNAME', 'glbcroleobjv.VALUE')
                    ->where('glbcuserrole.STATUS', '=', '01')
                    ->where('glbcuserrole.USER_ID', '=', $lv_username)
                    ->where('glbcroleobjv.STATUS', '=', '01')
					->where('glbcroleobjv.OBJECT', '=', $lv_object)
                    ->where('glbcroleobjv.FIELDNAME', '=', $lv_fieldname_apps)
                    ->get();

            $return['error'] = false;
            $return['message'] ="Success";
            $return['status'] = "01";

        }
		catch(\Exception $e){
			Log::error($e);
            $return['error'] = true;
            $return['message'] =$e->getMessage();
            $return['status'] = "02";
        }
        
		return compact('return', 'apps');       
    }


}
