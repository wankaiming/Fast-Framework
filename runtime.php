<?php class runtime{     var $StartTime = 0;     var $StopTime = 0;      function get_microtime()     {         list($usec, $sec) = explode(' ', microtime());         return ((float)$usec + (float)$sec);     }      function start()     {         $this->StartTime = $this->get_microtime();     }      function stop()     {         $this->StopTime = $this->get_microtime(); 		echo "\r\n<!-- show page use ".$this->spent()."s -->";    }      function spent()     {         return round(($this->StopTime - $this->StartTime), 2);     } }?>