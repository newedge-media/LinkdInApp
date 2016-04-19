<?php
class CustomLeftAndMainExtension extends LeftAndMainExtension {
	function onAfterInit() {
		CMSMenu::remove_menu_item('Help');
	}
}