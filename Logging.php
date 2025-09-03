<?php
// Logging PHP class
// Copyright (C) 2013 vbwx
/*
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Logging
{
    // define default log file
    private $log_file = "logfile";
    // define file pointer
    private $fp = null;

    // set log file (path and name)
    public function setFile ($path) {
        $this->log_file = $path;
    }
    // write message to the log file
    public function write ($method, $message){
        // if file pointer doesn't exist, then open log file
        if (!$this->fp) $this->open();
        // define current time
        $now = date('Y-m-d H:i:s');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$now ($method)\n$message\n\n");
    }
    // open log file
    private function open (){
        // define log file path and name
        $lfile = $this->log_file;
		$i = 0;
		while (is_file($lfile) and filesize($lfile) >= 500*1024)
			$lfile = $this->log_file . "." . ++$i;
		if ($lfile !== $this->log_file)
			rename($this->log_file, $lfile);
        // open log file for writing only; place the file pointer at the end of the file
        // if the file does not exist, attempt to create it
        $this->fp = fopen($this->log_file, 'a') or exit("Can't open '$lfile'!");
    }

    public function __destruct() {
        fclose($this->fp);
    }
}

function writeLog ($line, $prefix = null, $suffix = null)
{
	static $log = null;
	if (!$log)
	{
		$log = new Logging();
		$log->setFile(realpath($_SERVER['DOCUMENT_ROOT']."../logs").
		              "/intranet.log");
	}
	list(, $caller) = debug_backtrace(false);
	$log->write((@$caller['class'] ? $caller['class']."::" : "").
				$caller['function'], ($prefix === null ? "" : print_r($prefix, true)."\n").
				print_r($line, true).($suffix === null ? "" : "\n".print_r($suffix, true)));
	return $line;
}
?>

