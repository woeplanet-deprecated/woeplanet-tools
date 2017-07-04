<?php

namespace Woeplanet\Utils;

abstract class TaskRunner {
	protected $verbose;
	protected $tasks;
	static protected $spinner = [
		'/',
		'-',
		'\\',
		'|',
		'/',
		'-',
		'\\',
		'|'
	];

	public function __construct($verbose=false) {
		$this->verbose = $verbose;
	}

	abstract public function run();

	protected function log($message) {
		echo "$message\n";
	}

	protected function logVerbose($message) {
		if ($this->verbose) {
			$this->log($message);
		}
	}

	protected function make_placeholder_woeid($woeid, $placeholder=NULL) {
		$placetype = $this->placetypes->get_by_id(0);
		$doc = [
			// 'body' => [
			// '_id' => (int)$woeid,
			// 'woe:id' => (int)$woeid,
			'iso' => '',
			'name' => '',
			'lang' => 'ENG',
			'placetype' => (int)$placetype['placetype']['id'],
			'placetypename' => $placetype['placetype']['name'],
			'parent' => 0,
			'history' => $this->history
			// ],
			// 'index' => self::DATABASE,
			// 'type' => self::PLACES_TYPE,
			// 'id' => (int)$woeid,
			// 'refresh' => true
		];

		if (NULL !== $placeholder) {
			// error_log('merging in placeholder:');
			// error_log(var_export($placeholder, true));
			$doc = array_merge($doc, $placeholder);
		}

		$doc['woeid'] = (int)$woeid;

		return $doc;
	}

	protected function sanitize_coord($coord) {
        if ($coord == '\N' || $coord == '\n' || $coord == NULL) {
            $coord = 0;
        }

        return $coord;
    }

	protected function check_raw_data($file, $row, $data, $fields) {
		$missing = [];

		foreach ($fields as $field) {
			if (!isset($data[$field])) {
				$missing[] = $field;
			}
		}

		if (!empty($missing)) {
			$fields = implode(',',$missing);
			throw new \Exception("$file:$row - Missing fields $fields");
		}
	}


	protected static function show_spinner($count, $title, $init=false) {
		static $i = 0;
		if ($init) {
			$i = 0;
		}
		echo sprintf("\r(%s) %s: %s", self::$spinner[ (($i++ > 7) ? ($i = 1) : ($i % 8)) ], $title, $count); //restore cursor position and print
		flush();
	}

	// Thanks to Brian Moon for this - http://brian.moonspot.net/php-progress-bar
	protected static function show_status($done, $total, $size=30) {
		if ($done === 0) {
			$done = 1;
		}
		static $start_time;
		if ($done > $total)
			return; // if we go over our bound, just ignore it
		if (empty ($start_time))
			$start_time = time();
		$now = time();
		$perc = (double) ($done / $total);
		$bar = floor($perc * $size);
		$status_bar = "\r[";
		$status_bar .= str_repeat("=", $bar);
		if ($bar < $size) {
			$status_bar .= ">";
			$status_bar .= str_repeat(" ", $size - $bar);
		} else {
			$status_bar .= "=";
		}
		$disp = number_format($perc * 100, 0);
		$status_bar .= "] $disp%  $done/$total";
		if ($done === 0){$done = 1;}//avoid div zero warning
		$rate = ($now - $start_time) / $done;
		$left = $total - $done;
		$eta = round($rate * $left, 2);
		$elapsed = $now - $start_time;

		echo "$status_bar  ";
		flush();
		// when done, send a newline
		if($done == $total) {
			echo "\n";
		}
	}
}

?>
