<?php
class ProspectMapAdmin extends ModelAdmin {

    private static $managed_models = array(
        'ProspectMap'
    );

    private static $url_segment = 'research';

    private static $menu_title = 'Research';

    private static $menu_icon = 'mysite/images/research-icon.png';

    public function init() {
        parent::init();
        Requirements::customScript(<<<JS
            (function($) {
                $.entwine('ss', function($){
                    $('a.gfSearchLink').entwine({
                        onclick: function(e) {
                            window.open($(this).attr('href'));
                            e.preventDefault();
                            return false;
                        }
                    });                    
                });
            })(jQuery);
            
JS
        );

    }

    public function getList() {
    	$list = parent::getList();

    	$list = $list->filter(array(
    		'MemberID' => Member::currentUserID()
    	));

    	return $list;
    }
}
