@extends('master')
@section('title', 'Unlock Entry')


@section('content')

@include('unlock-entry-views::css.unlockentry_css')
@include('unlock-entry-views::js.unlockentry_controls')
@include('unlock-entry-views::js.unlockentry_functions')
@include('unlock-entry-views::js.unlockentry_query')
@include('unlock-entry-views::js.unlockentry_global')

<script type="text/javascript">


	$.sap.require("jquery.sap.storage");
	var UI5Storage = $.sap.storage(jQuery.sap.storage.Type.session);

	sap.ui.localResources("js");
	sap.ui.localResources("neko.control");
	jQuery.sap.require("neko.control.Notification");

	jQuery.sap.require("sap.ui.core.format.DateFormat");
	var timeFormat_12H = sap.ui.core.format.DateFormat.getTimeInstance({pattern: "KK:mm:ss a"});
	var timeFormat_24H = sap.ui.core.format.DateFormat.getTimeInstance({pattern: "HH:mm:ss"});
	var dateFormat = sap.ui.core.format.DateFormat.getDateInstance({pattern: "yyyy-MM-dd"});

	CreateContent();

</script>

@endsection
