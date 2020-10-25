<?php
include("init.php");

$user = (object) new user();

$action = getArrayVal($_GET, "action");
$id = getArrayVal($_GET, "id");
$mode = getArrayVal($_GET, "mode");

if (!in_array($action, array("login", "logout", "forgot", "getnewpass", "activate")))
{
    if (!isset($_SESSION["userid"]))
    {
        $template->assign("loginerror", 0);
        $template->display("login.tpl");
        die();
    }
}

$cloud = new tags();
$cloud->cloudlimit = 1;
$thecloud = $cloud->getTagcloud($id);
if (strlen($thecloud) > 0)
	$template->assign("cloud", $thecloud);

$name = getArrayVal($_POST, "name");
$realname = getArrayVal($_POST, "realname");
$role = getArrayVal($_POST, "role");
$email = getArrayVal($_POST, "email");
$tel1 = getArrayVal($_POST, "tel1");
$tel2 = getArrayVal($_POST, "tel2");
$company = getArrayVal($_POST, "company");
$address1 = getArrayVal($_POST, "address1");
$address2 = getArrayVal($_POST, "address2");
$state = getArrayVal($_POST, "state");
$country = getArrayVal($_POST, "country");
$locale = getArrayVal($_POST, "locale");
$tags = getArrayVal($_POST, "tags");
$oldpass = getArrayVal($_POST, "oldpass");
$newpass = getArrayVal($_POST, "newpass");
$repeatpass = getArrayVal($_POST, "repeatpass");
$admin = getArrayVal($_POST, "admin");
$turl = getArrayVal($_POST, "web");
$gender = getArrayVal($_POST, "gender");
$zip = getArrayVal($_POST, "zip");
$taski = getArrayVal($_GET, "task");
$fproject = getArrayVal($_GET, "project");

$template->assign("mode", $mode);
// get the available languages
$languages = getAvailableLanguages();
$template->assign("languages", $languages);

$project = array();
$project['ID'] = $id;
$template->assign("project", $project);
// set css classes for menue buttons
$classes = array("overview" => "overview", "msgs" => "msgs", "tasks" => "tasks", "miles" => "miles", "files" => "files", "users" => "users_active", "tracker" => "tracking");

$mainclasses = array("desktop" => "",
    "profil" => "active",
    "admin" => ""
    );
$template->assign("mainclasses", $mainclasses);
$template->assign("classes", $classes);

if ($action == "login")
{
    $mode = getArrayVal($_GET, "openid_mode");
    $username = getArrayVal($_POST, "username");
    $pass = getArrayVal($_POST, "pass");
    // openid validation
    if ($mode == "id_res")
    {
        $openid = new openid();
        $openid->SetIdentity($_GET['openid_identity']);
        $openid_validation_result = $openid->ValidateWithServer();
        if ($openid_validation_result == true)
        {
            $name = getArrayVal($_REQUEST, "openid_sreg_fullname");
            $lang = getArrayVal($_REQUEST, "openid_sreg_language");
            $email = getArrayVal($_REQUEST, "openid_sreg_email");
            // try to login using openid username w/o Pass
            if ($user->login($name, ""))
            {
                $loc = $url . "index.php?mode=login";
                header("Location: $loc");
            }
            else
            {
                // if user doesnt exist, create it and log it in
                if ($user->add($name, $email, 0, "", 1, $lang))
                {
                    if ($user->login($name, ""))
                    {
                        $loc = $url . "index.php?mode=login";
                        header("Location: $loc");
                    }
                }
            }
        }
        else
        {
            $template->assign("loginerror", 1);
            $template->display("login.tpl");
        }
    }
    else
    {
        // normal login
        if ($user->login($username, $pass))
        {
            $loc = $url . "index.php?mode=login";
            header("Location: $loc");
        }
        else
        {
            // openid
            if (empty($pass) and stristr($username, "http"))
            {
                $openid = new openid();
                $openid->SetIdentity($username);
                $url = substr($url, 0, strlen($url)-1);
                $openid->SetTrustRoot($url);
                $openid->SetRequiredFields(array('email', 'fullname'));
                $openid->SetOptionalFields(array('language', 'timezone'));
                if ($openid->GetOpenIDServer())
                {
                    $openid->SetApprovedURL($url . "/manageuser.php?action=login"); // Send Response from OpenID server to this script
                    $openid->Redirect(); // This will redirect user to OpenID Server
                }
            }
            else
            {
                $template->assign("loginerror", 1);
                $template->display("login.tpl");
            }
        }
    }
} elseif ($action == "logout")
{
    if ($user->logout())
    {
        header("Location: index.php?mode=logout");
    }
} elseif ($action == "forgot")
{
    $template->display("forgot.tpl");
} elseif ($action == "getnewpass")
{
    $userobj = new user();
    if($profile = $userobj->getProfileByEmail($email)) {
        if($password = $userobj->setNewPassword($profile["ID"])) {
            $themail = new emailer($settings);
            $mail_body = "{$langfile["hello"]},<br><br>{$langfile["login"]}: %s<br>{$langfile["newpass"]}: %s";
            $themail->send_mail($profile["email"], $langfile["newpass"], sprintf($mail_body, $profile["email"], $password));
            $template->assign("loginmessage", "The new password has been sent to the your E-Mail address.");
        }
        $template->display("login.tpl");
    } else {
        $template->assign("forgoterror", 1);
        $template->display("forgot.tpl");
    }
}elseif ($action == "activate")
{
    $hash = getArrayVal($_GET, "hash");
    $userobj = new user();
    if($userobj->activate($hash))
        $template->assign("loginmessage", "Your account is activated. You can login now!");
    else
        $template->assign("loginerrormessage", "Sorry, your account cannot be activated.");
    $template->display("login.tpl");
}elseif ($action == "addform")
{
    $title = $langfile['adduser'];
    $template->assign("title", $title);
    $template->display("adduserform.tpl");
} elseif ($action == "editform")
{
    $clientobj = new client();

    $languages_fin = array();
    foreach($languages as $lang)
    {
        $lang2 = $langfile[$lang];
        $fin = countLanguageStrings($lang);

        if (!empty($lang2))
        {
            $lang2 .= " (" . $fin . "%)";
            $fin = array("val" => $lang, "str" => $lang2);
        }
        else
        {
            $lang2 = $lang . " (" . $fin . "%)";
            $fin = array("val" => $lang, "str" => $lang2);
        }
        array_push($languages_fin, $fin);
    }
    $template->assign("languages_fin", $languages_fin);

    $title = $langfile['edituser'];

    $template->assign("title", $title);
    $euser = $user->getProfile($userid);
    $clients = $clientobj->getAllClients();

    $template->assign("clients", $clients);
    $template->assign("user", $euser);

    $template->display("edituserform.tpl");
} elseif ($action == "edit")
{
    $_SESSION['userlocale'] = $locale;
    $_SESSION['username'] = $name;

    if (!empty($_FILES['userfile']['name']))
    {
        $fname = $_FILES['userfile']['name'];
        $typ = $_FILES['userfile']['type'];
        $size = $_FILES['userfile']['size'];
        $tmp_name = $_FILES['userfile']['tmp_name'];
        $error = $_FILES['userfile']['error'];
        $root = "./";

        $desc = $_POST['desc'];
        $teilnamen = explode(".", $fname);
        $teile = count($teilnamen);
        $workteile = $teile - 1;
        $erweiterung = $teilnamen[$workteile];

        $subname = "";
        if ($typ != "image/jpeg" and $typ != "image/png" and $typ != "image/gif" and $typ != "image/pjpeg")
        {
            $loc = $url . "manageuser.php?action=profile&id=$userid";
            header("Location: $loc");
            die();
        }

        for ($i = 0; $i < $workteile; $i++)
        {
            $subname .= $teilnamen[$i];
        }
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        srand($seed);
        $randval = rand(1, 999999);

        $subname = preg_replace("/[^-_0-9a-zA-Z]/", "_", $subname);
        $subname = preg_replace("/\W/", "", $subname);

        if (strlen($subname) > 200)
        {
            $subname = substr($subname, 0, 200);
        }

        $fname = $subname . "_" . $randval . "." . $erweiterung;

        $clientobj = new client();
        $clientpath = $clientobj->getClientPath($userid);
        if(!file_exists(CL_ROOT . "/files/" . CL_CONFIG . "$clientpath/avatar"))
            mkdir(CL_ROOT . "/files/" . CL_CONFIG . "$clientpath/avatar");
        $datei_final = CL_ROOT . "/files/" . CL_CONFIG . "$clientpath/avatar/" . $fname;

        if (move_uploaded_file($tmp_name, $datei_final))
        {
            $avatar = $fname;
        }

        /**
         * Resize Image on upload
         * $imagehw = GetImageSize($pic);
         * $imagewidth = $imagehw[0];
         * $imageheight = $imagehw[1];
         * $myThumb = new hft_image($datei_final);
         * if ($imageheight > $imagewidth)
         * {
         * $myThumb->resize(105, 105, "+");
         * }
         * else
         * {
         * $myThumb->resize(105, 105, "-");
         * }
         * $myThumb->output_resized($datei_final);
         */

        if ($user->edit($userid, $name, $realname, $email, $tel1, $tel2, $company, $zip, $gender, $turl, $address1, $address2, $state, $country, "", $locale, $avatar, 0))
        {
            if (!empty($oldpass) and !empty($newpass) and !empty($repeatpass))
            {
                $user->editpass($userid, $oldpass, $newpass, $repeatpass);
            }
            $loc = $url . "manageuser.php?action=profile&id=$userid&mode=edited";
            header("Location: $loc");
        }
    }
    else
    {
        if ($user->edit($userid, $name, $realname, $email, $tel1, $tel2, $company, $zip, $gender, $turl, $address1, $address2, $state, $country, "", $locale, "", 0))
        {
            if (isset($oldpass) and isset($newpass) and isset($repeatpass))
            {
                $user->editpass($userid, $oldpass, $newpass, $repeatpass);
            }
            $loc = $url . "manageuser.php?action=profile&id=$userid&mode=edited";
            header("Location: $loc");
        }
    }
} elseif ($action == "del")
{
    if ($user->del($id))
    {
        $loc = $url . "admin.php?action=users&mode=deleted";
        header("Location: $loc");
    }
} elseif ($action == "profile")
{
    $thetag = new tags();
    $start = getArrayVal($_GET, "start");
    $end = getArrayVal($_GET, "end");
    $proj = (object) new project();
    if ($userpermissions["admin"]["add"])
    {
        $projects = $proj->getMyProjects($id);
        $i = 0;
        if (!empty($projects))
        {
            foreach($projects as $opro)
            {
                $membs = $proj->getProjectMembers($opro["ID"], 1000);
                $projects[$i]['members'] = $membs;
                $i = $i + 1;
            }
            $template->assign("opros", $projects);
        }
    }
    $tracker = (object) new timetracker();

    if (!empty($start) and !empty($end))
    {
        $track = $tracker->getUserTrack($id, $fproject, $taski , $start, $end);
    }
    else
    {
        $track = $tracker->getUserTrack($id, $fproject, $taski);
    }
    if (!empty($track))
    {
        $totaltime = $tracker->getTotalTrackTime($track);
        $template->assign("totaltime", $totaltime);
        $template->assign("fproject", $fproject);
        $template->assign("start", $start);
        $template->assign("end", $end);
    }
    $template->assign("tracker", $track);
    SmartyPaginate::assign($template);
    $profile = $user->getProfile($id);

    $title = $langfile['userprofile'];
    $template->assign("title", $title);
    $template->assign("user", $profile);

    $template->display("userprofile.tpl");
} elseif ($action == "showproject")
{
    if (!chkproject($userid, $id))
    {
        $errtxt = $langfile["notyourproject"];
        $noperm = $langfile["accessdenied"];
        $template->assign("mode", "error");
        $template->assign("errortext", "$errtxt<br>$noperm");
        $template->display("error.tpl");
        die();
    }
    $mainclasses = array("desktop" => "desktop",
        "profil" => "profil",
        "admin" => "admin"
        );
    $template->assign("mainclasses", $mainclasses);
    $proj = (object) new project();
    $alluser = $user->getAllUsers(10000);
    $users = array();

    foreach($alluser as $all)
    {
        if (!chkproject($all['ID'], $id))
        {
            array_push($users, $all);
        }
    }
	SmartyPaginate::disconnect();

    $members = $proj->getProjectMembers($id, 14);
    $pro = $proj->getProject($id);

    $projectname = $pro['name'];
    $title = $langfile['members'];

    $template->assign("title", $title);
    $template->assign("projectname", $projectname);
    SmartyPaginate::assign($template);
    $template->assign("members", $members);
    $template->assign("users", $users);
    $template->display("projectmembers.tpl");
} elseif ($action == "vcard")
{
    $theuser = $user->getProfile($id);

    $vCard = (object) new vCard($theuser["locale"]);

    $vCard->setFirstName($theuser["name"]);
    // $vCard->setMiddleName('Mobil');
    // $vCard->setLastName('Mustermann');
    // $vCard->setEducationTitle('Doctor');
    // $vCard->setDepartment('Product Placement');
    // $vCard->setJobTitle('CEO');
    $vCard->setCompany($theuser["company"]);
    $vCard->setOrganisation($theuser["company"]);
    $vCard->setPostalStreet($theuser["adress"]);
    $vCard->setPostalZIP($theuser["zip"]);
    $vCard->setWorkZIP($theuser["zip"]);
    $vCard->setHomeZIP($theuser["zip"]);
    $vCard->setPostalCity($theuser["adress2"]);
    $vCard->setHomeCity($theuser["adress2"]);
    $vCard->setPostalRegion($theuser["state"]);
    $vCard->setPostalCountry($theuser["country"]);
    $vCard->setWorkStreet($theuser["adress"]);
    $vCard->setWorkCity($theuser["adress2"]);
    $vCard->setWorkRegion($theuser["state"]);
    $vCard->setWorkCountry($theuser["country"]);
    $vCard->setUrlWork($theuser["url"]);
    $vCard->setEMail($theuser["email"]);
    // $vCard->outputFile('vcf');
    header('Content-Type: text/x-vcard');
    header('Content-Disposition: inline; filename=' . $theuser["name"] . '_' . date("d-m-Y") . '.vcf');
    echo $vCard->getCardOutput();
}

?>