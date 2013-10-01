<?php
	/**
	* JSON - EMAIL VERIFICATION BUNDLE
	*
	* @package MAIL VERIFICATION BUNDLE
	* @author Ahmad Abdel Naser
	* @version 1.-
	* @website http://ahmadnaser.com


-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `huno` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_huno` (`huno`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'ahmad@ahmad.com', 'asd', 'asd', '1', 'asdds', 'asd1');	*/
	

	
	
	
	
	
	
	
	
	
$format= 'json';
$method=$_GET['method'];



//http://localhost/index.php?method=docx
if($method=="docx")
{
	include "docx.php";
}



if($method=="getallusers")
{
/* connect to the db */
	$link = mysql_connect('localhost','root','') or die('Cannot connect to the DB');
	mysql_select_db('users_db',$link) or die('Cannot select the DB');


	
	//$query = "SELECT post_title, guid FROM wp_posts WHERE post_author = $user_id AND post_status = 'publish' ORDER BY ID DESC LIMIT $number_of_posts";
	
	$query ="SELECT
user.id,
user.email,
user.password,
user.username,
user.`status`,
user.token,
user.huno
FROM `user`;
";

	$result = mysql_query($query,$link) or die('Errant query:  '.$query);

	/* create one master array of the records */
	$posts = array();
	if(mysql_num_rows($result)) {
		while($post = mysql_fetch_assoc($result)) {
			$posts[] = array('users'=>$post);
		}
	}

	/* output in necessary format */
	if($format == 'json') {
		header('Content-type: application/json');
		echo json_encode(array('users'=>$posts));
	}


	/* disconnect from the db */
	@mysql_close($link);

}

if(isset($_GET['email'])){
if($method=="registeruser")
{
	$random=RandNumber(4);
$result ="false";		
$result2 ="false";
$isvalid= 'false';
	include "class.emailverify.php";
	
	$verify = new EmailVerify();
	$verify->debug_on = false;

	$verify->local_user = 'localuser';	//username of your address from which you are sending message to verify
	$verify->local_host = 'hundw.me';	//domain name of your address

	if($verify->verify($_GET['email'])){
		$isvalid= 'true';
		}
			else
	{
	$isvalid= 'false';
	}
	
	
	
	


	
	

if($isvalid=='true'){////if email is available on the web


/* connect to the db */
$link = mysql_connect('localhost','root','') or die('Cannot connect to the DB');
	mysql_select_db('users_db',$link) or die('Cannot select the DB');

	
	
	
	
		$sSql ="SELECT COUNT(*) AS `count` FROM `user`
			WHERE `email` = '$_GET[email]'";

		$result =mysql_result(mysql_query($sSql,$link),0);//check for unique email
		
				$sSql2 ="SELECT COUNT(*) AS `count` FROM `user`
			WHERE `huno` = '$_GET[huno]'";

		$result2 =mysql_result(mysql_query($sSql2,$link),0);//check for unique huno	
		
		
		
		if ($result == '0' && $result2 == '0'){//make sure there is no email,huno for the same user

	
$query="INSERT INTO `user` (username, email, password,huno,token,status)
VALUES
('$_GET[username]','$_GET[email]','$_GET[password]','$_GET[huno]','".$random."','0')";
	$resultx = mysql_query($query,$link) or die('Errant query:  '.$query);


	/* create one master array of the records */
	$posts = array();
	
	sendemail($_GET['username'],$_GET['email'],$random);//send verification code to the user
	

	
	}//end of make sure there is no email,huno for the same user
	
	
	}
	

	
	
	
	if($format == 'json') {
	if($result!="0")
	{
	$result='false';
	}
	else
	{
	$result='true';////if email-not-registered
	}
	
		if($result2!="0")
	{
	$result2='false';
	}
		else
	{
	$result2='true';////if hnuo-not-registered
	}
	
		header('Content-type: application/json');
		echo json_encode(array('email-not-registered'=>$result,'hnuo-not-registered'=>$result2,'validEmail'=>$isvalid));
	}


	/* disconnect from the db */
	@mysql_close($link);

}

if($method=="activateuser")

{
$result="false";
	$link = mysql_connect('localhost','root','') or die('Cannot connect to the DB');
	mysql_select_db('users_db',$link) or die('Cannot select the DB');

	
	

$sql="UPDATE `user`
SET `status`='1'
WHERE email='".$_GET['email']."' and token='".$_GET['token']."' ";


	mysql_query($sql,$link);
	$result =mysql_affected_rows($link);

	if($format == 'json') {
	if($result=="1")
	{
	$result='true';
	}
	else
	{
	$result='false';
	}

		header('Content-type: application/json');
		echo json_encode(array('activated'=>$result));
	}
	
	
	@mysql_close($link);
}


}
	
function RandNumber($e){
 
 $rand="";
 for($i=0;$i<$e;$i++){
 $rand =  $rand .  rand(0, 9);  
 }
 return $rand;

 }

 
 function buildBody($username,$email,$activationcode)//build email body
{
$body = "Dear ".$username."<br /> Your Activation Code is :<h1>".$activationcode."</h1>";
return $body;
}

/*
if(
isset($_POST['contactform_subject'])&&!empty($_POST['contactform_subject']) &&
isset($_POST['contactform_firstname'])&&!empty($_POST['contactform_firstname']) &&
isset($_POST['contactform_lastname'])&&!empty($_POST['contactform_lastname']) &&
isset($_POST['contactform_message'])&&!empty($_POST['contactform_message']) &&
isset($_POST['contactform_phone'])&&!empty($_POST['contactform_phone']) &&
isset($_POST['contactform_email'])&&!empty($_POST['contactform_email']) &&
isset($_POST['contactform_company'])&&!empty($_POST['contactform_company'])
)
{
$contactform_title=$_POST['contactform_title'];
$contactform_firstname=$_POST['contactform_firstname'];
$contactform_lastname=$_POST['contactform_lastname'];
$contactform_phone=$_POST['contactform_phone'];
$contactform_email=$_POST['contactform_email'];
$contactform_zipcode=$_POST['contactform_zipcode'];
$contactform_company=$_POST['contactform_company'];
$contactform_street=$_POST['contactform_street'];
$contactform_city=$_POST['contactform_city'];
$contactform_subject=$_POST['contactform_subject'];
$contactform_message=$_POST['contactform_message'];
  sendemail($contactform_title,$contactform_firstname,$contactform_lastname,$contactform_phone,$contactform_email,$contactform_zipcode,$contactform_company,$contactform_street,$contactform_city,$contactform_subject,$contactform_message);
}
*/

     function sendemail($username,$email,$activationcode)//send email via mail service ( non-pear )
    {

$from = "Activation <ahmadnassr@gmail.com>";

$message =buildBody($username,$email,$activationcode);
$subject="Activation";
//$to = 'info@hundw.com';
$to=$email;

$headers = "From: ".$email."\r\nReply-To: ".$email."";
$headers .= "\r\nContent-Type: text/html; charset='iso-8859-1'; charset='iso-8859-1';Content-Transfer-Encoding: 7bit"; 

$mail_sent = @mail( $to, $subject, $message, $headers );
if($mail_sent)
{
return true;
?>

<?php 
}
else
{
return false;
}

    }
	
	
	
		    function   sendemailE($username,$email,$activationcode)//send email via pear mail service ( pear )
    {

	$e="ahmadnassr@gmail.com";
	

require_once "Mail.php";
$from = "Activation <sterio007@gmail.com>";
$to = $$username." <".$email.">";
$subject="Activation";
$body=buildBody($username,$email,$activationcode);


$host = "ssl://smtp.gmail.com";//"smtp.gmail.com";
$port = "465";//"587";
$username = "sterio007";
$password = "xxxx";
$headers = array ('From' => $from,
'To' => $to,
'Subject' => $subject,
'Content-type'=> "text/html",
'charset'=>"utf-8");
$smtp =@ Mail::factory('smtp',
array ('host' => $host,
'port' => $port,
'auth' => true,
'username' => $username,
'password' => $password));
$mail = @$smtp->send($to, $headers, $body);

if (@PEAR::isError($mail)) {
//echo("<p>" . $mail->getMessage() .":(". "</p>");
return false;
} else {
	//echo '<h1 align="center"><a >activation for  '.$e.'   send to your email</a></h1>';
	return true;
	?>
	
	
	
	<?php
}


    }