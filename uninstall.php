<?php
/**
 * Uninstall functionality
 * 
 * Removes the plugin cleanly in WP 2.7 and up
 */
require_once('amr-personalise-uninstall.php');

// delete the option that the plugin added
amr_personalise_uninstall();

