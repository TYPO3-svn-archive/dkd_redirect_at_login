<?php

/**
 * Plugin 'Better login-box' for the 'newloginbox' extension.
 * 
 * @author	Kasper Skårhøj (kasperYYYY@typo3.com)
 * @author	Ingmar Schlecht (ingmars@web.de)
 * @author	Hans J. Martin <Hans-Jakob.Martin@gmx.net>
 * @author	Tony Murray <tonymurray@fastmail.fm>
 * @author	Karsten Dambekalns <karsten@typo3.org>
 * @package TYPO3
 * @subpackage ux_dkd_redirect_user_at_login
 */

class ux_tx_newloginbox_pi1 extends tx_newloginbox_pi1  {

		// Default plugin variables:
	var $prefixId = 'tx_newloginbox_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_newloginbox_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'newloginbox';	// The extension key.
	
	/**
	 * Displays an alternative, more advanced / user friendly login form (than the default)
	 * Extended to enable 	1. redirect after login (according to groups->redirect page in database
	 *			2. redirect to requested uri afetr login. (overrides the 1. function)
	 * 
	 * @param	string		Default content string, ignore
	 * @param	array		TypoScript configuration for the plugin
	 * @return	string		HTML for the plugin
	 */
	function main($content,$conf)	{
	
			// Loading TypoScript array into object variable:
		$this->conf=$conf;
		
			// Loading language-labels
		$this->pi_loadLL();
		
			// Init FlexForm configuration for plugin:
		$this->pi_initPIflexForm();

			// Get storage PIDs:
		$d=$GLOBALS['TSFE']->getStorageSiterootPids();

			// GPvars:		
		$logintype=t3lib_div::GPvar('logintype');
		$redirect_url=t3lib_div::GPvar('redirect_url');

		// if redirect_url is not set, we will set this to the requested URI if it is not the
		// page with the login-form
		if (!$redirect_url){
			$redirect_url=t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
			if (  (is_int(strpos(t3lib_div::locationHeaderUrl($redirect_url),
				  t3lib_div::locationHeaderUrl($GLOBALS['TSFE']->id))))  || 
				(is_int(strpos(t3lib_div::locationHeaderUrl($redirect_url),
				  t3lib_div::locationHeaderUrl('index.php?id='.$GLOBALS['TSFE']->id))))  || 
				(t3lib_div::getIndpEnv('TYPO3_SITE_URL') == t3lib_div::locationHeaderUrl($redirect_url)) ) { 
				$redirect_url='';
			}
		}
		if ($this->piVars['forgot'])	{
			$content.='<h3>'.$this->pi_getLL('forgot_password','',1).'</h3>';

			if (trim($this->piVars['DATA']['forgot_email']) && t3lib_div::validEmail($this->piVars['DATA']['forgot_email']))	{

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username,password', 'fe_users', 'email='.$GLOBALS['TYPO3_DB']->fullQuoteStr(trim($this->piVars['DATA']['forgot_email']), 'fe_users')." AND pid=".intval($d['_STORAGE_PID']).$this->cObj->enableFields('fe_users'));
				if ($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
					$msg=sprintf($this->pi_getLL('forgot_password_pswmsg','',1),trim($this->piVars['DATA']['forgot_email']),$row['username'],$row['password']);
				} else {
					$msg=sprintf($this->pi_getLL('forgot_password_no_pswmsg','',1),trim($this->piVars['DATA']['forgot_email']));
				}
				$this->cObj->sendNotifyEmail($msg, trim($this->piVars['DATA']['forgot_email']), '', $this->conf['email_from'], $this->conf['email_fromName'], $this->conf['replyTo']);
				
				$content.='<p>'.sprintf($this->pi_getLL('forgot_password_emailSent','',1), '<em>'.htmlspecialchars(trim($this->piVars['DATA']['forgot_email'])).'</em>').'</p>';
				$content.='<p'.$this->pi_classParam('back').'>'.$this->pi_linkTP_keepPIvars($this->pi_getLL('forgot_password_backToLogin','',1),array('forgot'=>'')).'</p>';
			} else {
				$content.='<p>'.$this->pi_getLL('forgot_password_enterEmail','',1).'</p>';
				$content.='
				
					<!--
						"Send password" form
					-->
					<form action="'.htmlspecialchars(t3lib_div::getIndpEnv('REQUEST_URI')).'" method="post" style="margin: 0 0 0 0;">
					<table '.$this->conf['tableParams_details'].'>
						<tr>
							<td><p>'.$this->pi_getLL('your_email','',1).'</p></td>
							<td><input type="text" name="'.$this->prefixId.'[DATA][forgot_email]" value="" /></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type="submit" name="submit" value="'.$this->pi_getLL('send_password','',1).'"'.$this->pi_classParam('submit').' />
							</td>
						</tr>
					</table>
					</form>
				';
			}
		} else {
			if ($GLOBALS['TSFE']->loginUser)	{
				
				if ($logintype=='login')	{
					$outH = $this->getOutputLabel('header_success','s_success','header');
					$outC = str_replace('###USER###',$GLOBALS['TSFE']->fe_user->user['username'],$this->getOutputLabel('msg_success','s_success','message'));
	
					if ($outH)	$content.='<h3>'.$outH.'</h3>';
					if ($outC)	$content.='<p>'.$outC.'</p>';
					
//added for redirect 
					$groupData = $GLOBALS["TSFE"]->fe_user->groupData;
					reset($groupData);
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_dkdredirectatlogin_redirectpage', $GLOBALS["TSFE"]->fe_user->usergroup_table, 'tx_dkdredirectatlogin_redirectpage!=\'\' OR NOT(tx_dkdredirectatlogin_redirectpage IS NULL) AND uid IN ('.implode(',',$groupData['uid']).')');
					if ($row=$GLOBALS['TYPO3_DB']->sql_fetch_row($res))	{
						($redirect_url)? '' : $redirect_url = $this->pi_getPageLink($row[0]); //take the first group with a redirect page
					}

					if (!$GLOBALS['TSFE']->fe_user->cookieId)	{
						$content.='<p style="color:red; font-weight:bold;">'.$this->pi_getLL('cookie_warning','',1).'</p>';
					} elseif ($redirect_url)	{
						header('Location: '.t3lib_div::locationHeaderUrl($redirect_url));
						exit;
					}
				} else {
					$outH = $this->getOutputLabel('header_status','s_status','header');
					$outC = str_replace('###USER###',$GLOBALS['TSFE']->fe_user->user['username'],$this->getOutputLabel('msg_status','s_status','message'));
	
					if ($outH)	$content.='<h3>'.$outH.'</h3>';
					if ($outC)	$content.='<p>'.$outC.'</p>';
					
					$usernameInfo = '<strong>'.htmlspecialchars($GLOBALS['TSFE']->fe_user->user['username']).'</strong>, '.htmlspecialchars($GLOBALS['TSFE']->fe_user->user['name']);
					if ($this->conf['detailsPage'])	{
						$usernameInfo = $this->pi_linkToPage($usernameInfo,$this->conf['detailsPage'],'',array('tx_newloginbox_pi3[showUid]' => $GLOBALS['TSFE']->fe_user->user['uid'], 'tx_newloginbox_pi3[returnUrl]'=>t3lib_div::getIndpEnv('REQUEST_URI')));
					}
					
					$content.='
				
						<!--
							"Logout" form
						-->
						<form action="'.htmlspecialchars($this->pi_getPageLink($GLOBALS['TSFE']->id,'_top')).'" target="_top" method="post" style="margin: 0 0 0 0;">
						<table '.$this->conf['tableParams_details'].'>
							<tr>
								<td><p'.$this->pi_classParam('username').'>'.$this->pi_getLL('username','',1).'</p></td>
								<td><p>'.$usernameInfo.'</p></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" name="submit" value="'.$this->pi_getLL('logout','',1).'"'.$this->pi_classParam('submit').' />
									<input type="hidden" name="logintype" value="logout" />
									<input type="hidden" name="pid" value="'.intval($d['_STORAGE_PID']).'" />
								</td>
							</tr>
						</table>
						</form>
					';
				}
			} else {
				if ($logintype=='login')	{
					$outH = $this->getOutputLabel('header_error','s_error','header');
					$outC = $this->getOutputLabel('msg_error','s_error','message');
				} elseif ($logintype=='logout')	{
					$outH = $this->getOutputLabel('header_logout','s_logout','header');
					$outC = $this->getOutputLabel('msg_logout','s_logout','message');
				} else {	// No user currently logged in:
					$outH = $this->getOutputLabel('header_welcome','s_welcome','header');
					$outC = $this->getOutputLabel('msg_welcome','s_welcome','message');
				}
				if ($outH)	$content.='<h3>'.$outH.'</h3>';
				if ($outC)	$content.='<p>'.$outC.'</p>';
				$content.='
				
					<!--
						"Login" form, including login result if failure.
					-->
					<form action="'.htmlspecialchars($this->pi_getPageLink($GLOBALS['TSFE']->id,'_top')).'" target="_top" method="post" style="margin: 0 0 0 0;">
					<table '.$this->conf['tableParams_details'].'>
						<tr>
							<td><p>'.$this->pi_getLL('username','',1).'</p></td>
							<td><input type="text" name="user" value="" /></td>
						</tr>
						<tr>
							<td><p>'.$this->pi_getLL('password','',1).'</p></td>
							<td><input type="password" name="pass" value="" /></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" name="submit" value="'.$this->pi_getLL('login','',1).'"'.$this->pi_classParam('submit').' />
								<input type="hidden" name="logintype" value="login" />
								<input type="hidden" name="pid" value="'.intval($d['_STORAGE_PID']).'" />
								<input type="hidden" name="redirect_url" value="';
			($redirect_url)?$content.=htmlspecialchars(t3lib_div::locationHeaderUrl($redirect_url)):'';
			$content.='" />
							</td>
						</tr>
					</table>
					</form>
				';
				
				if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'],'show_forgot_password','sDEF'))	{
					$content.='<p'.$this->pi_classParam('forgotP').'>'.$this->pi_linkTP_keepPIvars($this->pi_getLL('forgot_password','',1),array('forgot'=>1)).'</p>';
				}
			}
		}

		return $this->pi_wrapInBaseClass($content);
	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/newloginbox/pi1/class.tx_newloginbox_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/newloginbox/pi1/class.tx_newloginbox_pi1.php']);
}
?>