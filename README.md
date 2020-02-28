# Unlock Entry Packages
## For Self Service - to unlock
	+ Pull latest from ePriority Dev
	+ Update composer.json under psr-4
		"Emobility\\CronJobManagement\\": "packages/cronjob-management-pkg/src/"
	+ Run composer update
	+ Update config/app.php under Application Service Providers...
		Emobility\CronJobManagement\CronJobManagementServiceProvider::class
	+ Run migrate files
		php artisan migrate --path=packages/cronjob-management-pkg/src/Migrations/
	+ Run seeding files
		php artisan db:seed --class="Emobility\CronJobManagement\Seeds\TimeInfoVHelp"
		php artisan db:seed --class="Emobility\CronJobManagement\Seeds\NotiWhenVHelp"

## Step to install Cronjob Management from github
	1. Add below code into composer.json after "autoload"
		"repositories":[
			{
				"type": "vcs",
				"url": "git@github.com:eoa-emobility/cronjob-management-pkg.git"
			}
		],
	2. Run composer require emobility/cronjob-management-pkg
	3. Add below code into config/app.php under "Application Service Providers..."
		Emobility\CronJobManagement\CronJobManagementServiceProvider::class
		
	** If all code already there after pull, directly run composer update
	
	4. Run migration file
		php artisan migrate --path=vendor/emobility/cronjob-management-pkg/src/Migrations/
	5. Run seeding file
		php artisan db:seed --class="Emobility\CronJobManagement\Seeds\TimeInfoVHelp"
		php artisan db:seed --class="Emobility\CronJobManagement\Seeds\NotiWhenVHelp"
	6. Configure catalog
		TSTC: 		CRONJOB_MGT
		Target Url:	cronjob/management
		Function: 	CR_DISPLAY = Cronjob Definition
				CR_SCHEDULE = Schedule Cronjob
				REG_CTRL = Register Controller
