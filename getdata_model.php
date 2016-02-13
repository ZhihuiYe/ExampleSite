<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Getdata_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }//constructor


    //the return data is in an array, use for codeigiter parser
    public function getAllData( $siteName, $language, $pageName, $tab_n, $button_n )
    {
        if( $siteName == 'educationsite' )
          $workingFolder = 'educationSite';
        else
          $workingFolder = 'healthCareSite';

        $data = array_merge( $this->getPageContent               ( $siteName, $language, $pageName                    )
                            , $this->getNavigationData           ( $siteName, $workingFolder, $language, $pageName, $tab_n, $button_n )
                            , $this->getBreadcrumbsData          ( $siteName, $language, $pageName, $tab_n, $button_n )
                            );
        $email = array();

        if( $this->check_model->isThePageRequireEmailData         ( $pageName, $tab_n ) )
            $email = $this->theDataForEmailSubmiteAction          ( $siteName,                 $language                               );

        return array_merge( $data, $email);
    }//getLinks


    public function getPageContent( $siteName, $language, $pageName )
    {
        $this->lang->load( $siteName . "_tabs_and_buttons", $language );
        return array( 'pageTitle' => $this->lang->line( $pageName . "_pageTitle" ) );
    }//_getPageContent


    public function getNavigationData( $siteName, $workingFolder, $language, $pageName, $tab_n, $button_n )
    {
        $this->lang->load( $siteName . "_tabs_and_buttons", $language );
        $this->lang->load( "common/navigation", $language );
        $this->helper_model->loadLanguageFile( 'landingPage', $language, 'landingPage' );

        $navigationLabels = array(
                    'backToTop'                   => $this->lang->line( 'navigation_backToTop' )
                    , 'siteName'                  => $this->lang->line( 'landingPage_' . $workingFolder . 'LinkLabel' )
                    , 'englishLabel'              => "English"
                    , 'chineseLabel'              => "中文"
                    , 'englishLink'               => "index.php/site/view/" . $siteName . "/english/" . $pageName . "/" . $tab_n . "/" . $button_n
                    , 'chineseLink'               => "index.php/site/view/" . $siteName . "/chinese/" . $pageName . "/" . $tab_n . "/" . $button_n
                    , 'rightSideContactDetail'    => $this->lang->line( 'navigation_contactDetailLeftSide'    )
                    , 'educationSiteLinkLabel'    => $this->lang->line( 'landingPage_educationSiteLinkLabel'  )
                    , 'healthCareSiteLinkLabel'   => $this->lang->line( 'landingPage_healthCareSiteLinkLabel' )
                    , 'backToTopLabel'            => $this->lang->line( 'landingPage_backToTopLabel'          )
                    , 'healthCareSiteLinkLink'    => "/site/view/healthcaresite/" . $language . "/page-1/tab_1/button_1"
                    , 'educationSiteLinkLink'     => "/site/view/educationsite/" . $language . "/page-1/tab_1/button_1"
                    , 'landingPageLink'           => "index.php/landingpage/view/" . $language
                                );

        $links = array( 'links' => array() );

        $pageIndex = 1;
        while( ! $this->lang->line( 'page-' . $pageIndex . '_pageTitle') == '')
        {
            array_push( $links[ 'links' ],
                        array('linkLabel'       => $this->lang->line( 'page-' . $pageIndex . '_pageTitle')
                                , 'linkActive'  => $this->helper_model->linkActive( 'page-' . $pageIndex, $pageName )
                                , 'link'        => "index.php/site/view/" . $siteName . "/" . $language . "/" . 'page-' .  $pageIndex . "/tab_1/button_1" ) );

            $pageIndex++;
        }//while

        return array_merge( $navigationLabels, $links );
    }//_getNavigationData


    public function theDataForEmailSubmiteAction( $siteName, $language )
    {
        $emailData = array(
                    'submissionAction'   => "index.php/site/sendEmail/" . $siteName . "/" . $language
                    , 'submissionResult' => $this->session->userdata( 'sendingEmailResult' ) );

        $this->session->set_userdata(array('sendingEmailResult' => ""));
        return $emailData;
    }//theDataForEmailSubmiteButton


    public function getTabsData( $siteName, $language, $pageName, $tab_n )
    {
        $this->lang->load( $siteName . "_tabs_and_buttons", $language );

        $tabs = array( 'tabs' => array() );

        $tabIndex = 1;
        while ( ! $this->lang->line( $pageName . '_tab_' . (string)$tabIndex ) == "" )
        {
            array_push( $tabs[ 'tabs' ],
                                array('tabLabel'     => $this->lang->line( $pageName . '_tab_' . (string)$tabIndex )
//                                      , 'tabActive' => $this->helper_model->tabActive( $tab_n, "tab_" . (string)$tabIndex )
                                    , 'tabLink'      => "index.php/site/view/" . $siteName . "/" . $language . "/" . $pageName . "/tab_" . $tabIndex . "/button_1") );
            $tabIndex++;
        }//while loop

    //     output example
    //     $tabs = array(
    //               'tabs' => array(
    //                           array('tab' => "tab label one"
    //                                 'tabLink' => "index.php/site/view/educationsite/...."
    //                                )
    //
    //                           array('tab' => "tab label two"
    //                                 'tabLink' => "index.php/site/view/educationsite/...."
    //                                )
    //
    //                           array('tab' => "tab label three")
    //                                 'tabLink' => "index.php/site/view/educationsite/...."
    //                                )
    //                  )

        return $tabs;
    }//getButtonsData


    //loading buttons/main contents (below the tabs)
    public function getButtonsData( $siteName, $language, $pageName, $tab_n, $button_n )
    {
        $this->lang->load( $siteName . "_tabs_and_buttons", $language );

        $buttons = array( 'buttons' => array());

        if( $pageName == 'studyTours' && $tab_n == "tab_1" )
            $additionalData = array( 'optionMainLabel' => $this->lang->line( "studyTours_optionMainLabel" ) );
        else
            $additionalData = array( 'optionMainLabel' => '' );


        $buttonIndex = 1;
        while ( ! $this->lang->line( $pageName . "_" . $tab_n . "_button_" . (string)$buttonIndex ) == "" )
        {
            array_push( $buttons[ 'buttons' ],
                                    array( 'buttonLabel'    => $this->lang->line( $pageName . "_" . $tab_n . "_button_" . (string)$buttonIndex )
                                        //, 'buttonActive'  => $this->helper_model->buttonActive( $button_n, "button_" . (string)$buttonIndex )
                                            , 'buttonLink'  => "index.php/site/view/" . $siteName . "/" . $language . "/" . $pageName . "/" . $tab_n . "/button_" . (string)$buttonIndex) );
            $buttonIndex++;
        }//while

    // output example
    // $buttons = array( 'buttons' => array(
    //                                  'button' => 'button1'
    //                                  'buttonLink' => 'index.php/site/view/educationsite/chinese/professionalTraining/tab_1/button_1'
    //
    //                                  'button' => 'button2'
    //                                  'buttonLink' => 'index.php/site/view/educationsite/chinese/professionalTraining/tab_1/button_1'
    //                                     )
    //                    );

        return array_merge( $buttons, $additionalData );
    }//_getButtonsData


    public function getBreadcrumbsData( $siteName, $language, $pageName, $tab_n, $button_n  )
    {
        $this->lang->load( $siteName . "_tabs_and_buttons", $language );


        $breadcrumbPageName = $this->lang->line( $pageName . "_pageTitle" );
        $breadcrumbTabLabel = $this->lang->line( $pageName . "_" . $tab_n );
        $breadcrumbButtonLabel = $this->lang->line( $pageName . "_" . $tab_n . "_" . $button_n );

        $result = array( 'breadcrumbs' => array() );
        if ( ! $breadcrumbPageName == "" )
            array_push( $result['breadcrumbs'], array( 'breadcrumb'    => $breadcrumbPageName ) );
        if ( ! $breadcrumbTabLabel == "" )
            array_push( $result['breadcrumbs'], array( 'breadcrumb'    => $breadcrumbTabLabel ) );
        if ( ! $breadcrumbButtonLabel == "" )
            array_push( $result['breadcrumbs'], array( 'breadcrumb'  => $breadcrumbButtonLabel ) );

        return $result;
    }//getBreadcrumbsData


    public function getPhotoData( $workingFolder, $pageName, $tab_n, $button_n )
    {
        //'get_filenames' function is from 'file' helper.
        //it will returns an array containing the names of all files contained within the given dir
        //(WARNING image file only)
        $this->load->helper("url");
        $filenames = get_filenames("assets/" . $workingFolder . "/photos/" . $pageName . "/" . $tab_n . "/" . $button_n . "/miniPhoto");
        $data = array( 'photos' => array() );

        if (empty($filenames))
        {
            return $data;
        }

        if (sizeof($filenames) == 0)
        {
            echo("Error!!!!: this page doesnot contain any photos for photo rondell");
            return $data;
        }

        foreach ($filenames as $filename)
            array_push( $data['photos'], array( 'displayPhoto' => "assets/" . $workingFolder . "/photos/"
                                                            . $pageName . "/" . $tab_n . "/"
                                                            . $button_n . "/miniPhoto/" . $filename
                                                , 'photoLink' => "assets/" . $workingFolder . "/photos/"
                                                            . $pageName . "/" . $tab_n . "/"
                                                            . $button_n . "/" . $filename ) );

    //  output example
    // $data = array( 'photo' => array(
    //                                 'displayPhoto' => "assets/educationSite/photos/studyTours/tab_1/button_1/miniPhoto/photo1.jpg"
    //                                 , 'photoLink'  => "assets/educationSite/photos/studyTours/tab_1/button_1/photo1.jpg"
    //
    //                                 'displayPhoto' => "assets/educationSite/photos/studyTours/tab_1/button_1/miniPhoto/photo2.jpg"
    //                                 , 'photoLink'  => "assets/educationSite/photos/studyTours/tab_1/button_1/photo2.jpg")
    //                 );
        return $data;
    }//_getPhotoData


}//class