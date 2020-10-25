<?php
class Request {
  private $_arGetVars;
  private $_arPostVars;
  private $_arCookieVars;
  private $_arRequestVars;
  private $_objOriginalRequestObject;

  private $_redirectFailure;

  function __construct($check_for_cookie=true) {
    // 引入变量
    $this->_arGetVars = $_GET;
    $this->_arPostVars = $_POST;
    $this->_arCookieVars = $_COOKIE;
    $this->_arRequestVars = $_REQUEST;

    if ($check_for_cookie) {
      if (@$this->_arCookieVars["phpOriginalRequestObject"]) {
        $cookieVal = $this->_arRequestVars["phpOriginalRequestObject"];
        $this->_redirectFailure = true;
        if (strlen($cookieVal)>0) {
          setcookie("phpOriginalRequestObject", "", time()-3600);
          $origObj = unserialize(escapeshellarg(stripslashes($cookieVal)));
          $this->_objOriginalRequestObject = &$origObj;
          $this->_arRequestVars["phpOriginalRequestObject"] = "";
          $this->_arGetVars["phpOriginalRequestObject"] = "";
          $this->_arPostVars["phpOriginalRequestObject"] = "";
        };
        $this->_redirectFailure = true;
      } else {
        $this->_redirectFailusre = false;
      };
    } else {
      $this->_redirectFailure = false;
    };
  }

  function redirectFailure() {
    return $this->_redirectFailure;
  }

  function getOriginalRequestObject() {
    return $this->_objOriginalRequestObject;
  }

  function getParameterValue($strParameter) {
    return $this->_arRequestVars[$strParameter];
  }

  function getParameters() {
    return $this->_arRequestVars;
  }

  function getCookies() {
    return $this->_arCookieVars;
  }

  function getPostVariables() {
    return $this->_arPostVars;
  }

  function getGetVariables() {
    return $this->_arGetVars;
  }
}
