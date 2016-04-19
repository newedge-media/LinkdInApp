<?php
class ProspectCampaignAdmin extends ModelAdmin {

    private static $managed_models = array(
        'ProspectCampaign'
    );

    private static $url_segment = 'campaigns';

    private static $menu_title = 'Campaigns';

    private static $menu_icon = 'mysite/images/megaphone-icon.png';

    public function init() {
        parent::init();

        Requirements::customScript(<<<JS
            (function($) {
                $.entwine('ss', function($) {
                    $('.ss-gridfield button.ss-button-message-link').entwine({
                        onclick: function (e) {
                            window.open($(this).attr('data-message-link-target'));
                            e.preventDefault();
                            return false;
                        }
                    });

                    $('.ss-gridfield button.ss-button-profile-link').entwine({
                        onclick: function (e) {
                            window.open($(this).attr('data-profile-link-target'));
                            e.preventDefault();
                            return false;
                        }
                    });

                    $('select#Form_ItemEditForm_PlaybookTemplate').entwine({
                        onchange: function(e){
                            var obj = $(this);
                            $('input#Form_ItemEditForm_Title').attr('disabled', true);
                            $('textarea#Form_ItemEditForm_Content').attr('disabled', true);

                            $.getJSON('playbook/GetTemplate/' + obj.val(), function(data){
                                if (data.success) {
                                    $('input#Form_ItemEditForm_Title').val(data.title);
                                    $('textarea#Form_ItemEditForm_Content').text(data.content);
                                }

                                $('input#Form_ItemEditForm_Title').attr('disabled', false);
                                $('textarea#Form_ItemEditForm_Content').attr('disabled', false);
                                
                            });
                        }
                    });
                });
            })(jQuery);
JS
        );
    }

    /**
     * Get the edit form
     * 
     * @param  Int $id
     * @param  FieldList $fields
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm();

        $campaignFieldName = $this->sanitiseClassName($this->modelClass);
        $campaignGridField = $form->Fields()->fieldByName($campaignFieldName);

        $bulkManager = new GridFieldBulkManager();
        $bulkManager->removeBulkAction('bulkEdit');
        $bulkManager->removeBulkAction('unLink');
        $campaignGridField
            ->getConfig()
            ->addComponent($bulkManager);

        return $form;
    }

    public function getList() {
    	$list = parent::getList();

    	$list = $list->filter(array(
    		'MemberID' => Member::currentUserID()
    	));

    	return $list;
    }
}
