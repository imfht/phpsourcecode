<?php
/*
 * PlutoFramework
 * Class:Error
 * @author Alien <a457418121@gmail.com>
 */

class Error extends Controller{
    private $errorMessage = NULL;
/**
 * initalizes the view
 * @param $errorOption array  Options for the view
 * @return void
 */
    public function _construct($errorOption){
        if(isset($errorOption[1])){
            $this->errorMessage = $errorOption[1];
        }
    }
/**
 * generate the title of the error page
 *
 * @return string  Title of the error page.
 */
    public function getTitle(){
        return 'Error!';
    }

/**
 * Load and output the error view.
 *
 * @return void
 */
    public function outputErrorView(){
        $errorView = new View('error');
        $errorView->errorMessage = $this->errorMessage;
        $errorView->homeLink = WS_URL;

        $errorView->render();
    }

}