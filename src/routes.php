<?php

Route::group(array('prefix' => 'admin', 'middleware' =>['web','auth']), function () {
	
	Route::get('unlock_entry', 'Emobility\UnlockEntry\Controllers\UnlockEntryController@fn_show_page');
	Route::get('unlock_entry/get_entries', 'Emobility\UnlockEntry\Controllers\UnlockEntryController@fn_get_entries');
	
});