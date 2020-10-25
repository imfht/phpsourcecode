<?php

namespace Controller;
use \Base62;
use \Base256;

define('BR', "<br />\n");

class Id extends \MyController {

	public function index() {
		$this->load->helper('id');

		$id = 37865461456789;
		$incrase = 70;

		$added = 0;
		
		$minLength = false;
		$kassKey = null;

		while ($added < $incrase) {
			echo $id;
		
			//Alpha Id
			$alphaId = alphaId($id, false, $minLength, $kassKey);
			$num = alphaId($alphaId, true, $minLength, $kassKey);

			echo ' (<b>alphaId</b>-&gt; '.$alphaId.' -&gt; '.$num;
			
			//Base62
			$base62 = Base62::encode($num);
			$num = Base62::decode($base62);
			
			echo ' (<b>Base62</b>-&gt; '.$base62.' -&gt; '.$num;
			
			//Base256
			$base256 = Base256::encode($num);
			$num = Base256::decode($base256);
			
			echo ' (<b>Base256</b>-&gt; '.$base256.' -&gt; '.$num;
			
			//Pack Id
			$packId = packId($num);
			$num = unpackId($packId);
			
			echo ' (<b>packId</b>-&gt; '.$packId.' -&gt; '.$num;
			
			echo BR;

			$id = bcadd($id, 1);
			++$added;
		}
	}

}
