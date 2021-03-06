<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_ClientComputer_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	//*************************************************************************************************************************
	//*************************************************************************************************************************
	// MAIN METHODS
	//*************************************************************************************************************************
	//*************************************************************************************************************************

	public function setPrinter($printerName)
	{
		try
		{
			$obj = $this->logOperation('set_printer', null, $printerName, 'Set default printer to '.$printerName);
			
			$this->createActionFile($obj);
			
			$obj->setcco_result('OK')->save();	
		}
		catch (Exception $ex)
		{
			$obj->setcco_result($ex->getMessage())->save();			
			throw new Exception($ex->getMessage());
		}
	}
	
	/**
	 * Print document on client computer
	 *
	 * @param unknown_type $documentPath
	 */
	public function printDocument($content, $filename, $description)
	{
		$obj = $this->logOperation('print', $filename, '', $description);
		
		try 
		{
			//add document in exchange folder
			$filePath = $this->getUserExchangeDirectory().$filename;
			$f = fopen($filePath, 'w');
			if (!fwrite($f, $content))
				throw new Exception('Unable to write file');
			fclose($f);	
			
			$this->createActionFile($obj);
			
			$obj->setcco_result('OK '.$filePath)->save();		
		}
		catch (Exception $ex)
		{
			$obj->setcco_result($ex->getMessage())->save();			
			throw new Exception($ex->getMessage());
		}
	}
	
	/**
	 * Copy file in a location in client network
	 *
	 * @param unknown_type $filePath
	 * @param unknown_type $targetLocation
	 */
	public function copyFile($content, $filename, $targetLocation, $description)
	{
		$obj = $this->logOperation('copy', $filename, $targetLocation, $description);
		
		try 
		{
			//add document in exchange folder
			$filePath = $this->getUserExchangeDirectory().$filename;
			$f = fopen($filePath, 'w');
			if (!fwrite($f, $content))
				throw new Exception('Unable to write file : '.$filePath);
			fclose($f);	
			
			$this->createActionFile($obj);
			
			$obj->setcco_result('OK')->save();		
		}
		catch (Exception $ex)
		{
			$obj->setcco_result($ex->getMessage())->save();	
			throw new Exception($ex->getMessage());
		}
	}
	
	/**
	 * Return actions as xml
	 *
	 */
	public function getActionsAsXml($userName)
	{
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>'."\n";
		$xml .= '<actions>'."\n";

		//Get first files list
		$files = array();
		$directory = $this->getUserExchangeDirectory($userName);
		$handle = opendir($directory);
		while (false !== ($file = readdir($handle))) 
		{
			$pos = strpos($file, 'action_');
			if (!($pos === false))
			{
				$files[] = $file;
			}
		}
		
		//sort
		sort($files);
		
		//process files
		foreach($files as $file)
		{
			$string = file($directory.$file);
			
			$action = mage::getModel('ClientComputer/Action')->fromString($string);
			$xml .= '<action id="'.$file.'" operation="'.$action->getcco_operation().'" file="'.$action->getcco_file().'" name="'.$action->getcco_name().'" param="'.$action->getcco_param().'" />'."\n";
	    }
		closedir($handle);
		
		$xml .= '</actions>';
				
		return $xml;
	}
	
	//*************************************************************************************************************************
	//*************************************************************************************************************************
	//TOOLS
	//*************************************************************************************************************************
	//*************************************************************************************************************************

	/**
	 * Return exchange directory
	 *
	 * @return unknown
	 */
	public function getExchangeDirectory()
	{
		$magentoDirectory = BP;
		$retour = $magentoDirectory.'/exchangeDirectory/';
		if (($retour == '') || ($retour == null))
			throw new Exception('Exchange directory is empty !');
		if (!is_dir($retour))
			throw new Exception('Exchange directory doesnt exists : '.$retour);
		if (!is_writable($retour))
			throw new Exception('Exchange directory is not writeable : '.$retour);
		return $retour;
	}
	
	/**
	 * Return user exchange directory
	 *
	 */
	public function getUserExchangeDirectory($userName = null)
	{
		$value = $this->getExchangeDirectory();
		if (($userName == null) && (Mage::getSingleton('admin/session')->getUser()))
			$userName = Mage::getSingleton('admin/session')->getUser()->getusername();
		if ($userName == null)
			throw new Exception('Username is undefined');
		$value .= $userName.'/';
		if (!is_dir($value))
			mkdir($value);
			
		return $value;
	}
	
	/**
	 * Insert in database
	 *
	 * @param unknown_type $operation
	 * @param unknown_type $file
	 * @param unknown_type $param
	 * @param unknown_type $description
	 */
	protected function logOperation($operation, $file, $param, $description)
	{
		return mage::getModel('ClientComputer/Action')
					->setcco_operation($operation)
					->setcco_file($file)
					->setcco_param($param)
					->setcco_date(date('Y-m-d h:i'))
					->setcco_name($description)
					->save();
	}
	
	/**
	 * Create action file
	 *
	 */
	private function createActionFile($action)
	{
		$filePath = $this->getUserExchangeDirectory().'action_'.$action->getId().'.txt';
		$f = fopen($filePath, 'w');
		
		fwrite($f, $action->convertToString());
		fclose($f);
	}
}

?>