<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
  }//constructor

  public function index()
  {
    $this->view( 'examplesite', 'chinese', 'studyTours', 'tab_1', 'button_1' );
  }//index

  //this function calls all the necessary parses and call _getData method to
  //get necessary content to display the pages
  public function view( $siteName, $language, $pageName, $tab_n, $button_n)
  {
    $data = $this->getdata_model->getAllData( $siteName, $language, $pageName, $tab_n, $button_n );

    //pass the data to all part of the web page
    $this->parser->parse("includes/header", $data);
    $this->parser->parse("includes/sideBar", $data);
    $this->parser->parse("includes/headingImage", $data);
    $this->parser->parse("includes/navigationBar", $data);

    //tabs
    $tabs = $this->getdata_model->getTabsData( $siteName, $language, $pageName, $tab_n );
    if( ! empty($tabs) && ! empty($tabs['tabs']) )
        $this->parser->parse("includes/tabs", $tabs );

    //buttons
    $buttons = $this->getdata_model->getButtonsData( $siteName, $language, $pageName, $tab_n, $button_n );
    if( ! empty($buttons) && ! empty($buttons['buttons']) )
        $this->parser->parse("includes/buttons", $buttons );

    //breadcrumbs
    $breadcrumbs = $this->getdata_model->getBreadcrumbsData( $siteName, $language, $pageName, $tab_n, $button_n  );
    $this->parser->parse("includes/breadcrumbs", $breadcrumbs );

    //photoRondell
    $photos = $this->getdata_model->getPhotoData($workingFolder, $pageName, $tab_n, $button_n);
    if( ! empty($photos) && ! empty($photos['photos']) )
        $this->parser->parse("includes/photoRondell", $photos );

    //if the file content does exist then show the page content, else display error message to users
    if( file_exists( 'application/views/' . $workingFolder . '/' . $language . "_mainContent/" . $pageName . "/" . $tab_n . "_" . $button_n . "_content.php" )  )
        $this->parser->parse( $workingFolder . "/" . $language . "_mainContent/" . $pageName . "/"  . $tab_n . "_" . $button_n . "_content" , $data );
    else
    {
        $errorMessage = array(
                  'missingFile'   => 'application/views/' . $workingFolder . '/' . $language . "_mainContent/" . $pageName . "/" . $tab_n . "_" . $button_n . "_content.php"
                  , 'message' => 'The file does not exist, please create ' . $tab_n . "_" . $button_n . "_content.php"
                                  . ' at ' . 'application/views/' . $workingFolder . '/' . $language . "_mainContent/" . $pageName . ', and add your content into this file.'
                                  . ' If folder "' . $pageName . '" does not exist please ceate one.' );

        $this->parser->parse( $workingFolder . "/" . $language . "_mainContent/pageContentNotFound", $errorMessage);
    }//catch

    $this->parser->parse("includes/rightSideContactDetail", $data);
    $this->parser->parse("includes/footer", $data);

  }//view

  public function download()
  {
    $this->load->model( 'download_model' );
    $this->download_model->download( 'welcomePack.doc' );
  }//download

  public function sendEmail( $siteName, $language )
  {
    $this->load->model('Sendemail_model');

    $name = $this->input->post('name');
    $from = $this->input->post('email');
    $message = $this->input->post('message');

    if( $this->Sendemail_model->validatingInputs( $language, $name, $from, $message ) )
        $this->Sendemail_model->send( $siteName, $language, $name, $from, $message );

    $this->view( $siteName, $language, 'page-9', 'tab_1', 'button_1' );
  }//sendEmail
}//class

