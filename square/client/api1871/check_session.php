<?php  header("Access-Control-Allow-Origin: *");

session_start();
if ($_SESSION['id']) {
	return 'authenticated';
}