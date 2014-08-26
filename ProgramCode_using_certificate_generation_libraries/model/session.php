<?php

	class SESSION{
			public static function checkSessionTime($expiredTime){
				$now = time();
				if($now > $expiredTime)
					return "EXPIRED";
				else
					return "ALLOWED";
			}
			
			public static function setExpiredTime(){
				$minute_15 = 15 * 60;
				return time() + $minute_15;
			}
	}
?>