<?php
/**
 * Simple logger class 
 * @@author onasus <contact@onasus.com>
 * @copyright  2009-2014 Onasus.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 /**
 * Usage
 * require_once('SimpleLogger.php'); 
 * $log = new SimpleLogger(dirname(__FILE__).'/debug.log');
 * $tag = "log test"// or shop name
 * $log->i($tag, "message");
 * .....
 * 
 * */
class SimpleLogger{
	private $log_file;
	
	/**
	 * Constructor
	 * @param String logfile - [optional] Absolute file name/path. Defaults to ubuntu apache log.
	 * @return void
	 **/
	function  __construct($log_file){
		$this->log_file = $log_file;
		if($log_file == null){
			$this->$log_file = dirname(__FILE__).'/debug.log';
		}
		
		if(!file_exists($log_file)){ //Attempt to create log file
			touch($log_file);
		}
		
		//Make sure we'ge got permissions
		if(!(is_writable($log_file) || $this->win_is_writable($log_file))){
			//Cant write to file,
			throw new Exception("LOGGER ERROR: Can't write to log", 1);
		}
	}
	
	/**
	 * d - Log Debug
	 * @param String tag - Log Tag
	 * @param String message - message to spit out
	 * @return void
	 **/
	public function d($tag, $message){
		$this->writeToLog("DEBUG", $tag, $message);
	}
	
	/**
	 * e - Log Error
	 * @param String tag - Log Tag
	 * @param String message - message to spit out
	 * @author
	 **/
	public function e($tag, $message){
		$this->writeToLog("ERROR", $tag, $message);
	}
	
	/**
	 * w - Log Warning
	 * @param String tag - Log Tag
	 * @param String message - message to spit out
	 * @author
	 **/
	public function w($tag, $message){
		$this->writeToLog("WARNING", $tag, $message);
	}
	
	/**
	 * i - Log Info
	 * @param String tag - Log Tag
	 * @param String message - message to spit out
	 * @return void
	 **/
	public function i($tag, $message){
		$this->writeToLog("INFO", $tag, $message);
	}
	
	/**
	 * writeToLog - writes out timestamped message to the log file as
	 * defined by the $log_file class variable.
	 *
	 * @param String status - "INFO"/"DEBUG"/"ERROR" e.t.c.
	 * @param String tag - "Small tag to help find log entries"
	 * @param String message - The message you want to output.
	 * @return void
	 **/
	private function writeToLog($status, $tag, $message) {
		$date = date('[Y-m-d H:i:s]');
		$msg = "$date: [$tag][$status] - $message" . PHP_EOL;
		file_put_contents($this->log_file, $msg, FILE_APPEND);
	}
	
	//Function lifted from wordpress
	//see: http://core.trac.wordpress.org/browser/tags/3.3/wp-admin/includes/misc.php#L537
	private function win_is_writable( $path ) {
		/* will work in despite of Windows ACLs bug
		 * NOTE: use a trailing slash for folders!!!
		* see http://bugs.php.net/bug.php?id=27609
		* see http://bugs.php.net/bug.php?id=30931
		*/
		if ( $path[strlen( $path ) - 1] == '/' ) // recursively return a temporary file path
			return win_is_writable( $path . uniqid( mt_rand() ) . '.tmp');
		else if ( is_dir( $path ) )
			return win_is_writable( $path . '/' . uniqid( mt_rand() ) . '.tmp' );
	
		// check tmp file for read/write capabilities
		$should_delete_tmp_file = !file_exists( $path );
		$f = @fopen( $path, 'a' );
		if ( $f === false )
			return false;
	
		fclose( $f );
	
		if ( $should_delete_tmp_file )
			unlink( $path );
	
		return true;
	}
}

?>