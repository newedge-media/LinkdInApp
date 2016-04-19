<?php

global $project;
$project = 'mysite';

global $database;
$database = 'linkedin_appointmentgeneratorapp';

require_once("conf/ConfigureFromEnv.php");

i18n::set_locale('en_GB');

//CMSMenu::remove_menu_item('SecurityAdmin');
CMSMenu::remove_menu_item('ReportAdmin');
CMSMenu::remove_menu_item('AssetAdmin');
CMSMenu::remove_menu_item('CMSPagesController');
CMSMenu::remove_menu_item('Help');