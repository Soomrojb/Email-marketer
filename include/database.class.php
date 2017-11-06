<?php

/*

	Created By:		Janib Soomro
	Email / Skype:	SoomroJB@Gmail.com / Soomrojb
	Fiverr profile:	https://www.fiverr.com/janibsoomro
	Upwork profile:	https://www.upwork.com/freelancers/~0180e0b0eab22ae314
	Creation Date:	1st Nov. 2017
	Last Modified:	1st Nov. 2017

*/

require	'Goutte/vendor/autoload.php';
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

class database {
    
        //	Variables for accessing database
        public	$host	=	'localhost';
        public	$dbuser	=	'admin';
        public	$dbpass	=	'admin';
        public	$dbase	=	'interspirev615';
    
        //	General Purpose Variables
        public  $Connection;
        public  $BaseURL    =	"http://localhost/interspire615/leads.php";
        public  $LoginURL   =	"http://localhost/interspire615";
        public  $EmailAdmin =   "http://localhost/interspire615/admin";
        public  $Credential =   ["admin", "admin"];
        public  $SenderName =   "Sender name";
        public  $SenderMail =   "sender@domain.net";
        public  $ReplayMail =   "sender@domain.net";
        public  $BounceMail =   "bounce@domain.net";
        
        public function __construct(){
            $this->connect_database();
        }
        
        public function connect_database(){
            $this->Connection	=	mysqli_connect($this->host, $this->dbuser, $this->dbpass);
            mysqli_set_charset($this->Connection, 'utf8');

            if (!$this->Connection) {
                echo    'Error connecting with database!';
                die();
            }

            //  select database
            mysqli_select_db($this->Connection, $this->dbase) or die("Could not open the database '$this->dbase'");
        }

        public function SyncMembers() {
            require         'simple_html_dom.php';
            $Soup       =	file_get_html($this->BaseURL);
            $Html       =   $Soup->find("//div[@id='divinactiveemail']", 0)->plaintext;
            $Explode    =   explode("},{", $Html);
            if (count($Explode) >= 5) {
                //  make sure there are few results at least
                //  set status=0
                $SQLQry     =   "UPDATE `members` SET `status` = '0' WHERE `status` = '1'";
                $Query      =   mysqli_query($this->Connection, $SQLQry);
                $UpdatdList =   array();
    
                for ($Loop = 0; $Loop < count($Explode); $Loop ++) {
                    $Expld2     =   explode(",", str_replace("&quot;", "", $Explode[$Loop]));
                    $AccountId  =   explode(":", $Expld2[0])[1];
                    $SignupDays =   explode(":", $Expld2[1])[1];
                    $EmailAddr  =   explode(":", $Expld2[2])[1];

                    if (isset(explode(":", $Expld2[3])[1])) {
                        $NumbUsers  =   explode(":", $Expld2[3])[1];
                    } else {
                        $NumbUsers  =   "0";
                    }
                    
                    if (isset(explode(":", $Expld2[4])[1])) {
                        $NumbProjs  =   explode(":", $Expld2[4])[1];
                    } else {
                        $NumbProjs  =   "0";
                    }
                    
                    if (isset(explode(":", $Expld2[5])[1])) {
                        $MaxTmEntry =   explode(":", $Expld2[5])[1];
                    } else {
                        $MaxTmEntry =   "0";
                    }
                    
                    $TmEntCount =   "0";
    
                    if (isset(explode(":", $Expld2[7])[1])) {
                        $ExpiryDate =   explode(":", $Expld2[7])[1];
                    } else {
                        $ExpiryDate =   "0";
                    }
                    
                    //  Check if it's a new or an old record
                    $SQLQry     =   "SELECT * FROM `members` WHERE `accountid` = '$AccountId'";
                    $Query      =   mysqli_query($this->Connection, $SQLQry);
    
                    if ($Query->num_rows == 0) {
                        //  It's a new record
                        $SQLInsQry  =   "INSERT INTO `members` (`accountid`, `status`, `email`, `signupdays`, `users`, `projects`, `mxtimeentdate`, `entriescount`, `expdate`) VALUES ('$AccountId','1','$EmailAddr','$SignupDays','$NumbUsers','$NumbProjs','0','0','0')";
                        $InsQuery   =   mysqli_query($this->Connection, $SQLInsQry);
                        array_push($UpdatdList, $EmailAddr);
                    } else {
                        $DbRow      =   $Query->fetch_row();
                        if ($DbRow[5] != $NumbUsers || $DbRow[6] != $NumbProjs) {
                            //  Update only when NumberofUsers or NumberofProjects changed
                            //  + change status to '2'
                            $SQLUpdQry  =   "UPDATE `members` SET `accountid`='$AccountId',`status`='2',`email`='$EmailAddr',`signupdays`='$SignupDays',`users`='$NumbUsers',`projects`='$NumbProjs',`mxtimeentdate`='0',`entriescount`='0',`expdate`='0' WHERE `accountid`='$AccountId'";
                            $UpdQuery   =   mysqli_query($this->Connection, $SQLUpdQry);
                        } else {
                            //  Update Signup days otherwise
                            $SQLUpdQry  =   "UPDATE `members` SET `status`='1', `signupdays`='$SignupDays' WHERE `accountid`='$AccountId' AND `status` = '0'";
                            $UpdQuery   =   mysqli_query($this->Connection, $SQLUpdQry);
                        }
                    }
                }
                
                //  All Done!
            }
        }

        public function Schedule($title, $segment='all', $status='active') {
            switch ($segment) {
                case "segone":
                    //  3-7 days
                    if ($status == 'active') {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '2' AND `signupdays` <= 7 AND `signupdays` >= 3";
                    } else {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '1' AND `signupdays` <= 7 AND `signupdays` >= 3";
                    }
                    break;
                case "segtwo":
                    //  8-25 days
                    if ($status == 'active') {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '2' AND `signupdays` <= 25 AND `signupdays` >= 8";
                    } else {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '1' AND `signupdays` <= 25 AND `signupdays` >= 8";
                    }
                    break;
                case "segthree":
                    //  26-30 days
                    if ($status == 'active') {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '2' AND `signupdays` <= 30 AND `signupdays` >= 26";
                    } else {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '1' AND `signupdays` <= 30 AND `signupdays` >= 26";
                    }
                    break;
                case "segfour":
                    //  31-40 days
                    if ($status == 'active') {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '2' AND `signupdays` <= 40 AND `signupdays` >= 31";
                    } else {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '1' AND `signupdays` <= 40 AND `signupdays` >= 31";
                    }
                    break;
                case "segfive":
                    //  41-90 days
                    if ($status == 'active') {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '2' AND `signupdays` <= 90 AND `signupdays` >= 41";
                    } else {
                        $SQLQry =   "SELECT * FROM `members` WHERE `status` = '1' AND `signupdays` <= 90 AND `signupdays` >= 41";
                    }
                    break;
                case "all":
                    break;
            }

            $Query  =   mysqli_query($this->Connection, $SQLQry);
            if ($Query->num_rows != 0) {
                $ListTitle  =   date("Y-m-d-G-i-s") . '_' . $segment . '_' . $status;
                
                //  Add new Contact List
                $SQLInsList =   "INSERT INTO `email_lists` (`listid`, `name`, `ownername`, `owneremail`, `bounceemail`, `replytoemail`, `bounceserver`, `bounceusername`, `bouncepassword`, `extramailsettings`, `companyname`, `companyaddress`, `companyphone`, `format`, `notifyowner`, `imapaccount`, `createdate`, `subscribecount`, `unsubscribecount`, `bouncecount`, `processbounce`, `agreedelete`, `agreedeleteall`, `visiblefields`, `ownerid`) VALUES (NULL, '$ListTitle', 'Janib Soomro', 'marketing@domain.net', 'marketing@domain.net', 'bounce@domain.net', '', '', '', '', '', '', '', 'b', '1', '0', '1509692951', '0', '0', '0', '0', '1', '0', 'emailaddress,subscribedate,format,status,confirmed', '1')";
                $QueryList  =   mysqli_query($this->Connection, $SQLInsList);
                if ($QueryList) {
                    //  get 'listid' of newly created contact list
                    $SQLListID  =   "SELECT * FROM `email_lists` WHERE `name` = '$ListTitle'";
                    $QueryLstId =   mysqli_query($this->Connection, $SQLListID);
                    if ($QueryLstId->num_rows !=0) {
                        //  ensure that list was created properly
                        $ListID =   $QueryLstId->fetch_row()[0];

                        //  add contacts in newly created contactlist
                        while ($Row = $Query->fetch_row()) {
                            $EmailID    =   $Row[3];
                            $Domain     =   '@' . explode("@", $EmailID)[1];
                            $AddLst     =   "INSERT INTO `email_list_subscribers` (`subscriberid`, `listid`, `emailaddress`, `domainname`, `format`, `confirmed`, `confirmcode`, `requestdate`, `requestip`, `confirmdate`, `confirmip`, `subscribedate`, `bounced`, `unsubscribed`, `unsubscribeconfirmed`, `formid`) VALUES (NULL, '$ListID', '$EmailID', '$Domain', 'h', '1', 'fe775dde7355c87deb7986c43cac1669', '1509693740', '', '1509693740', '', '1509693740', '0', '0', '0', '0')";
                            $QueryAdLst =   mysqli_query($this->Connection, $AddLst);
                        }

                        //  schedule email
                        $this->SetupEmail($title, $ListTitle);

                    }
                }
            }
        }

        private function SetupEmail($camptitle, $listtitle) {
            $Client		=	new Client();
            $Crawler	=	$Client->request('GET', $this->LoginURL);
            
            //  Login website
            $Form       =   $Crawler->selectButton('Login')->form();
            $Crawler    =   $Client->submit($Form, array('ss_username' => $this->Credential[0], 'ss_password' => $this->Credential[1]));
            
            //  Visit send email page
            // ***********************

            $Crawler    =   $Client->request('GET', $this->EmailAdmin . "/index.php?Page=Send");
            $Form       =   $Crawler->filter('form[name=frmSend]')->form();

            $Option     =   $Crawler->filterXPath("//*[@id='lists']/option[contains(text(),'$listtitle')]/@value")->text();
            $OldHtml    =   $Crawler->html();
            $NewHtml    =   str_replace("<span class=\"HelpToolTip_Title\" style=\"display:none;\">Contact List</span>",
                            "<input name='ISSelectReplacement_lists[][]' value='$Option'><input name='ShowFilteringOptions' value='2'><input name='lists[]' value='$Option'><input  name='search_lists' value='Type here to search...'>",
                            $OldHtml);
            $NewHtml    =   str_replace("<select id=\"lists\" name=\"lists[]\"",
                            "<select id=\"lasts\" name=\"lasts[]\"",
                            $NewHtml);
            $Crawler->clear();
            $Crawler->addHtmlContent($NewHtml);
            $Form       =   $Crawler->filter('form[name=frmSend]')->form();
            $Crawler    =   $Client->submit($Form);
            
            //  Set campaign and sender details
            // *********************************

            $Form       =   $Crawler->filter('form[name=frmSendStep3]')->form();
            $CampgNum   =   $Crawler->filterXPath("//select[@name='newsletter']/option[contains(text(),'$camptitle')]/@value")->text();

            $Crawler    =   $Client->submit($Form, array(
                            'sendcharset' => 'UTF-8',
                            'newsletter' => $CampgNum,
                            'sendfromname' => "'" . $this->SenderName . ".",
                            'sendfromemail' => "'" . $this->SenderMail . ".",
                            'replytoemail' => "'" . $this->ReplayMail . ".",
                            'bounceemail' => "'" . $this->BounceMail . ".",
                            'sendimmediately' => '1',
                            'datetime[day]' => date('j'),
                            'datetime[month]' => date('n'),
                            'datetime[year]' => date('Y'),
                            'sendtime_hours' => '09',
                            'sendtime_minutes' => '14',
                            'sendtime_ampm' => 'AM',
                            'sendtime' => '06:14AM',
                            'notifyowner' => '1',
                            'to_firstname' => '0',
                            'to_lastname' => '0',
                            'sendmultipart' => '1',
                            'trackopens' => '1',
                            'tracklinks' => '1',
                            'module_tracker_google_options_name' => "$camptitle",
                            'module_tracker_google_options_source' => 'MailingList',
                            ));

            $ConfirmURL =   $this->EmailAdmin . "/index.php?Page=Schedule&A=1";
            $Crawler    =   $Client->request('GET', $ConfirmURL);
            
            if (!$Crawler->html()) {
                echo    'Error!';
            }
      }
    }
    
?>