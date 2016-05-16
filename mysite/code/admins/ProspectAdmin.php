<?php
class ProspectAdmin extends ModelAdmin {

    private static $managed_models = array(
        'Prospect'
    );

    private static $url_segment = 'prospects';

    private static $menu_title = 'Prospects';

    private static $menu_icon = 'mysite/images/prospect-icon.png';

    private static $model_importers = array(
        'Prospect' => 'ProspectsCsvBulkLoader'
    );

    public function init() {
        parent::init();

        Requirements::customScript(<<<JS
            (function($) {
                $.entwine('ss', function($){

                    $('#Form_ImportForm_EmptyBeforeImport_Holder').hide();

                    $('.ss-gridfield button.ss-button-message-link').entwine({
                        onclick: function(e){
                            window.open($(this).attr('data-message-link-target'));
                            e.preventDefault();
                            return false;
                        }
                    });

                    $('.ss-gridfield button.ss-button-profile-link').entwine({
                        onclick: function(e){
                            window.open($(this).attr('data-profile-link-target'));
                            e.preventDefault();
                            return false;
                        }
                    });                    
                });
            })(jQuery);
            
JS
        );        
    }

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm();

        $prospectFieldName = $this->sanitiseClassName($this->modelClass);
        $prospectGridField = $form->Fields()->fieldByName($prospectFieldName);
        $prospectGridField
            ->getConfig()
            ->addComponents(
                new GridFieldExpandableForm(new FieldList(
                    TextField::create('FirstName'), 
                    TextField::create('Lastname', 'Last Name'), 
                    EmailField::create('Email'), 
                    TextField::create('Telephone'), 
                    TextField::create('JobTitle', 'Job Title'), 
                    TextField::create('CompanyName', 'Company Name'), 
                    TextField::create('ProfileLink', 'Profile Link'),
                    TextField::create('MessageLink', 'Message Link'),
                    DropdownField::create('Status', 'Status', singleton('Prospect')->dbObject('Status')->enumValues()), 
                    DropdownField::create(
                        'GroupID', 
                        'Campaign', ProspectCampaign::get()->filter(array(
                            'MemberID' => Member::currentUserID()))->map('ID', 'Title'))->setEmptyString('(no campaign)')
                )),
                new GridFieldCheckboxSelectComponent(), 
                new GridFieldMessageLinkAction(),
                new GridFieldProfileLinkAction(),
                new GridFieldLinkToCampaign(
                    'assigntocampaign', 
                    'Add prospects to campaign'              
                ), 
                new GridFieldUpdateStatus(
                    'updatestatus', 
                    'Update prospect status'
                )
        );


        return $form;
    }    

    public function getSearchContext() {
        $context = parent::getSearchContext();

        if($this->modelClass == 'Prospect') {
            $context->getFields()->push(
                DateField::create('q[DateRangeFrom]', 'Date Created From')
                    ->setConfig('showcalendar', true)
            );
            $context->getFields()->push(
                DateField::create('q[DateRangeTo]', 'Date Created To')
                    ->setConfig('showcalendar', true)
            );

            $context->getFields()->push(
                DropdownField::create('q[GroupID]', 'Campaign', ProspectCampaign::get()->filter(array('MemberID' => Member::currentUserID()))->map('ID', 'Title')
                )->setEmptyString('select one')
            );

            $context->getFields()->push(
                CheckboxField::create('q[NoCampaign]', 'Not in existing campaign')
            );

            $context->getFields()->push(
                CheckboxField::create('q[NoProfileLink]', 'No Profile Link')
            );

            $context->getFields()->push(
                CheckboxField::create('q[NoMessageLink]', 'No Message Link')
            );
        }

        return $context;
    }

    /**
     * Get the list
     * 
     * @return DataList
     */
    public function getList() {
    	$list = parent::getList();

        $list = $list->filter(array(
            'MemberID' => Member::currentUserID(), 
            'Status:not' => 'Hide'
        ));

        $params = $this->request->requestVar('q');
        if (isset($params['NoCampaign']) && $params['NoCampaign']) {
            $list = $list->filter(array('GroupID' => 0));
        }

        if (isset($params['DateRangeFrom']) && $params['DateRangeFrom'] && isset($params['DateRangeTo']) && $params['DateRangeTo']) {
            $list = $list->filter(array(
                'Created:GreaterThan' => $params['DateRangeFrom'] . ' 00:00:00',
                'Created:LessThan' => $params['DateRangeTo'] . ' 23:59:59'
            ));
        } 

        if (isset($params['DateRangeFrom']) && $params['DateRangeFrom'] && (!isset($params['DateRangeTo']) || !$params['DateRangeTo'])) {
            $list = $list->filter(array(
                'Created:GreaterThan' => $params['DateRangeFrom'] . ' 00:00:00', 
                'Created:LessThan' => '9999:01:01 23:59:59'
            ));
        }

        if (isset($params['DateRangeTo']) && $params['DateRangeTo'] && (!isset($params['DateRangeFrom']) || !$params['DateRangeFrom'])) {
            $list = $list->filter(array(
                'Created:GreaterThan' => '1000:01:01 00:00:00', 
                'Created:LessThan' => $params['DateRangeTo'] . ' 23:59:59'
            ));
        }

        if (isset($params['GroupID']) && $params['GroupID']) {
            $list = $list->filter(array(
                'GroupID' => $params['GroupID']
            ));
        }

        if (isset($params['NoProfileLink']) && $params['NoProfileLink']) {
            $list = $list->where("ProfileLink IS NULL");
        }

        if (isset($params['NoMessageLink']) && $params['NoMessageLink']) {
            $list = $list->where("MessageLink IS NULL");
        }

    	return $list;
    }
}
