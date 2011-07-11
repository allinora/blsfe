<?php
class Session_File extends Session {
	protected $session_directory;
    protected $savePath;
    protected $sessionName;

    public function __construct() {
		
	
		$this->session_directory = SESSION_PATH;
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
    }

    public function open($savePath, $sessionName) {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;
        return true;
    }

    public function close() {
        // your code if any
        return true;
    }

    public function read($id) {
	  $sess_file = $this->session_directory  . DS . $id;
	  return (string) @file_get_contents($sess_file);
    }

    public function write($id, $data) {
		$sess_file = $this->session_directory  . DS . $id;
		if ($fp = @fopen($sess_file, "w")) {
		  $return = fwrite($fp, $data);
		  fclose($fp);
		  return $return;
		} else {
		  return(false);
		}
    }

    public function destroy($id) {
		$sess_file = $this->session_directory  . DS . $id;
		return(@unlink($sess_file));
    }

    public function gc($maxlifetime) {
		$sess_save_path=$this->session_directory;
	  foreach (glob("$sess_save_path/*") as $filename) {
	    if (filemtime($filename) + $maxlifetime < time()) {
	      @unlink($filename);
	    }
	  }
	  return true;
    }
}

