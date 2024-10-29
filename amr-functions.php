<?php

/* This holds common amr functions file - it may  be in several plugins  */
if (function_exists('mimic_meta_box')) return;
{
	function mimic_meta_box($id, $title, $callback ) {
	global $screen_layout_columns;

	//	$style = 'style="display:none;"';
		$h = (2 == $screen_layout_columns) ? ' has-right-sidebar' : '';
		echo '<div style="clear:both;" class="metabox-holder'.$h.'">';
		echo '<div class="postbox-container" style="width: 49%;">';
		echo '<div class="meta-box-sortables" style="min-height: 10px;">';
		echo '<div id="' . $id . '" class="postbox" ' . $style . '>' . "\n";
		echo '<div class="handlediv" title="' . __('Click to toggle') . '"><br /></div>';
		echo "<h3 class='hndle'><span>".$title."</span></h3>\n";
		echo '<div class="inside">' . "\n";
		call_user_func($callback);
		echo "</div></div></div></div></div>";
		
	}
}

	
if (function_exists('amrp_flag_error')) return;{
	function amrp_flag_error ($text) {
		echo '<div class="error">'.$text.'</div>';
	}
}
	
if (function_exists('amrp_message')) return;
{
	function amrp_message ($text) {
		echo '<div class="update"><p>'.$text.'</p></div>';
	}
}
	
/*
    * Convert an object to an array
    *
    * @param    object  $object The object to convert
    * @reeturn      array
    *
    */
if (function_exists('amrp_objectToArray')) return;
{
	function amrp_objectToArray( $object ) {
	/* useful for converting any meta values that are objects into arrays */

		 if (gettype ($object) == 'object') {
			$s =  (get_object_vars ($object));
				if (isset ($s['__PHP_Incomplete_Class_Name'])) unset ($s['__PHP_Incomplete_Class_Name']);
			/*		forced access */
				return($s);
			 }
		else if (is_array ($object)) return array_map( 'amrp_objectToArray', $object ); /* repeat function on each value of array */
		else return ($object );
		}
}

	
if (function_exists('amrp_novalue')) return;
{
function amrp_novalue ($v) {
/* since empty returns true on 0 and 0 is valid , use this instead */
return (empty($v) or (strlen($v) <1));
};
}
