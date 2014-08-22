<?php
	/*
	* This is a PHP library that handles calling reCAPTCHA.
	*    - Documentation and latest version
	*          http://recaptcha.net/plugins/php/
	*    - Get a reCAPTCHA API Key
	*          https://www.google.com/recaptcha/admin/create
	*    - Discussion group
	*          http://groups.google.com/group/recaptcha
	*
	* Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
	* AUTHORS:
	*   Mike Crawford
	*   Ben Maurer
	*
	* MODIFIED BY:
	*	Federico Stange (for Apollo PHP Framework)
	*
	* Permission is hereby granted, free of charge, to any person obtaining a copy
	* of this software and associated documentation files (the "Software"), to deal
	* in the Software without restriction, including without limitation the rights
	* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	* copies of the Software, and to permit persons to whom the Software is
	* furnished to do so, subject to the following conditions:
	*
	* The above copyright notice and this permission notice shall be included in
	* all copies or substantial portions of the Software.
	*
	* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	* THE SOFTWARE.
	*/

	/**
	 * The reCAPTCHA server URL's
	 */

	namespace apolloFramework\web\captcha{

		class Recaptcha{

			const API_SERVER				=	"http://www.google.com/recaptcha/api";
			const	API_SECURE_SERVER		=	"https://www.google.com/recaptcha/api";
			const	VERIFY_SERVER			=	"www.google.com";

			private	$_privateKey		=	NULL;
			private	$_publicKey			=	NULL;

			public function __construct($privateKey=NULL,$publicKey=NULL){

				if(!is_null($privateKey)){

					$this->setPrivateKey($privateKey);

				}

				if(!is_null($publicKey)){

					$this->setPublicKey($publicKey);

				}

			}

			public function setPublicKey($key){

				if(empty($key)){

					throw(new \Exception("Public key can't be empty!"));

				}

				$this->_publicKey	=	$key;

			}

			public function getPublicKey(){

				return $this->_publicKey;

			}

			public function setPrivateKey($key){

				if(empty($key)){

					throw(new \Exception("Private key can't be empty!"));

				}

				$this->_privateKey	=	$key;

			}

			public function getPrivateKey(){

				return $this->_privateKey;

			}


			/**
			* Encodes the given data into a query string format
			* @param $data - array of string elements to be encoded
			* @return string - encoded request
			*/

			private function _qsencode ($data) {

				$req	=	"";

				foreach ( $data as $key => $value){

					$req	.=	$key	.	'='	.	urlencode(stripslashes($value)) . '&';

				}

				// Cut the last '&'
				$req	=	substr($req,0,strlen($req)-1);

				return	$req;

			}



			/**
			* Submits an HTTP POST to a reCAPTCHA server
			* @param string $host
			* @param string $path
			* @param array $data
			* @param int port
			* @return array response
			*/

			private function _httpPost($host, $path, $data, $port = 80) {

				$req	=	$this->_qsencode ($data);

				$http_request	=	"POST $path HTTP/1.0\r\n";
				$http_request	.=	"Host: $host\r\n";
				$http_request	.=	"Content-Type: application/x-www-form-urlencoded;\r\n";
				$http_request	.=	"Content-Length: " . strlen($req) . "\r\n";
				$http_request	.=	"User-Agent: reCAPTCHA/PHP For Apollo Framework\r\n";
				$http_request	.=	"\r\n";
				$http_request	.=	$req;

				$response = '';

				if( FALSE == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {

					throw(new \Exception('Could not open socket'));

				}

				fwrite($fs, $http_request);

				while (!feof($fs)){

                $response .= fgets($fs, 1160); // One TCP-IP packet

				}

				fclose($fs);

				$response	=	explode("\r\n\r\n", $response, 2);

				return $response;

			}



			/**
			* Gets the challenge HTML (javascript and non-javascript version).
			* This is called from the browser, and the resulting reCAPTCHA HTML widget
			* is embedded within the HTML form it was called from.
			* @param string $pubkey A public key for reCAPTCHA
			* @param string $error The error given by reCAPTCHA (optional, default is null)
			* @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
			* @return string - The HTML to be embedded in the user's form.
			*/

			public function getHtml($error = null, $use_ssl = FALSE){

				if (is_null($this->_publicKey) || empty($this->_publicKey)) {

					$msg	=	"To use reCAPTCHA you must get an API ".
					"key from <a href='https://www.google.com/recaptcha/admin/create'>".
					"https://www.google.com/recaptcha/admin/create</a>";

					throw(new \Exception($msg));

				}

			
				$server		=	($use_ssl)	?	self::API_SECURE_SERVER : self::API_SERVER;

				$errorpart	=	"";

				if ($error) {

					$errorpart = "&amp;error=" . $error;

				}

				$script	=	'<script type="text/javascript" src="'. $server . 
				'/challenge?k=' . $this->_publicKey . $errorpart . '">'.
				'</script>'.
				'<noscript>'.
				'<iframe src="'. $server . '/noscript?k=' . $this->_publicKey . $errorpart . 
				'" height="300" width="500" frameborder="0"></iframe><br/>'.
				'<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>'.
				'<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>'.
				'</noscript>';

				return $script;

			}

			/**
			* Calls an HTTP POST function to verify if the user's guess was correct
			* @param string $privkey
			* @param string $remoteip
			* @param string $challenge
			* @param string $response
			* @param array $extra_params an array of extra variables to post to the server
			* @return ReCaptchaResponse
			*/

			public function checkAnswer($remoteip, $challenge, $response, $extra_params = array()){

				if (is_null($this->_privateKey) || empty($this->_privateKey)){

					$msg	=	"To use reCAPTCHA you must get an API key from ".
					"<a href='https://www.google.com/recaptcha/admin/create'>".
					"https://www.google.com/recaptcha/admin/create</a>";

					throw(new \Exception($msg));

				}

				if($remoteip == null || $remoteip == '') {

					throw(new \Exception("For security reasons, you must pass the remote ip to reCAPTCHA"));

				}
	
				//discard spam submissions
				if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {

					$recaptcha_response = new ReCaptchaResponse();
					$recaptcha_response->is_valid = false;
					$recaptcha_response->error = 'incorrect-captcha-sol';

					return $recaptcha_response;

				}

				$args			=	Array (
											'privatekey' => $this->_privateKey,
											'remoteip' => $remoteip,
											'challenge' => $challenge,
											'response' => $response
				) + $extra_params;

				$response	=	$this->_httpPost(self::VERIFY_SERVER, "/recaptcha/api/verify",$args);

				$answers		=	explode ("\n", $response [1]);

				$recaptcha_response = new ReCaptchaResponse();

				if (trim ($answers [0]) == 'true') {

					$recaptcha_response->is_valid	=	TRUE;

				}else{

					$recaptcha_response->is_valid	=	FALSE;
					$recaptcha_response->error		=	$answers [1];

				}

				return $recaptcha_response;

			}

			/**
			* gets a URL where the user can sign up for reCAPTCHA. If your application
			* has a configuration page where you enter a key, you should provide a link
			* using this function.
			* @param string $domain The domain where the page is hosted
			* @param string $appname The name of your application
			*/

			public function getSignupUrl ($domain = null, $appname = null) {

				$args	=	Array(
									'domains' => $domain,
									'app' => $appname
				);

				return "https://www.google.com/recaptcha/admin/create?" .
				$this->_qsencode($args);

			}

			private function _aesPad($val) {

				$block_size	=	16;
				$numpad		=	$block_size - (strlen($val) % $block_size);

				return str_pad($val, strlen ($val) + $numpad, chr($numpad));

			}

			/* Mailhide related code */

			private function _aesEncrypt($val,$ky) {

				if (!function_exists ("mcrypt_encrypt")) {

					$msg	=	"To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.";

					throw(new \Exception($msg));

				}

				$mode	=	MCRYPT_MODE_CBC;   
				$enc	=	MCRYPT_RIJNDAEL_128;
				$val	=	$this->_aesPad($val);

				return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");

			}


			private function _mailhideUrlBase64 ($x) {

				return strtr(base64_encode ($x), '+/', '-_');

			}

			/* gets the reCAPTCHA Mailhide url for a given email, public key and private key */

			public function _mailHideUrl($email) {

				$condition	=	empty($this->_publicKey)	|| 
									is_null($this->_publicKey)	||
									empty($this->_privateKey)	||
									is_null($this->_privateKey);

				if ($condition){

				$msg	=	"To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " .
				"you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>";

					throw(new \Exception($msg));

		     
				}
	
				$ky			=	pack('H*', $this->_privateKey);
				$cryptmail	=	$this->_aesEncrypt ($email, $ky);
	
				return "http://www.google.com/recaptcha/mailhide/d?k=".
				$this->_publicKey.
				"&c=".

				$this->_mailHideUrlbase64($cryptmail);

			}

			/**
			* gets the parts of the email to expose to the user.
			* eg, given johndoe@example,com return ["john", "example.com"].
			* the email is then displayed as john...@example.com
			*/

			private function _mailHideEmailParts ($email) {

				$arr = preg_split("/@/", $email );

				if (strlen ($arr[0]) <= 4) {

					$arr[0] = substr ($arr[0], 0, 1);

				} else if (strlen ($arr[0]) <= 6) {

					$arr[0] = substr ($arr[0], 0, 3);

				} else {

					$arr[0] = substr ($arr[0], 0, 4);

				}

				return $arr;

			}

			/**
			* Gets html to display an email address given a public an private key.
			* to get a key, go to:
			*
			* http://www.google.com/recaptcha/mailhide/apikey
			*/

			public function mailhideHtml($email) {

				$emailparts = $this->_mailHideEmailParts ($email);

				$url = $this->mailHideUrl($this->_publicKey, $this->_privateKey, $email);

				$return	=	htmlentities($emailparts[0]) . "<a href='" . htmlentities ($url) .
				"' onclick=\"window.open('" . htmlentities ($url) .
				"', '', ".
				"'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300');".
				"return false;\" title=\"Reveal this e-mail address\">...</a>@".
				htmlentities ($emailparts [1]);

				return $return;

			}

		}

	}

?>
