<?php
/*
  Plugin Name: Email Secure
  Description: 
  Version: 1.0.0
  License: GPL
  Plugin URI: https://zeroseven.pl/email-secure
  Author: Paweł Sadowski
  Author URI: https://zeroseven.pl/
  Text Domain: email-secure
  Domain Path: /languages
 */

/**
 * Copyright (c) 2020-2020 Paweł Sadowski(email: pawel.sadowski@quatree.pl)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('WPINC') or die;
require_once('EmailSecure.php');
add_action( 'plugins_loaded', array( 'EmailSecure', 'init' ));

