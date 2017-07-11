<?php

class Nivo_Image_Slider_Deactivator {

	public static function deactivate() {
		flush_rewrite_rules();
	}

}
