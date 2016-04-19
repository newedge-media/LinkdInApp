<?php
class ProspectMessagePlaybookAdmin extends ModelAdmin {

    private static $managed_models = array(
        'ProspectMessage_Playbook'
    );

    private static $url_segment = 'playbooks';

    private static $menu_title = 'Playbooks';

    private static $menu_icon = 'mysite/images/template-icon.png';

    public function getList() {
    	$list = parent::getList();

    	$list = $list->filter(array(
    		'MemberID' => Member::currentUserID()
    	));

    	return $list;
    }

}
