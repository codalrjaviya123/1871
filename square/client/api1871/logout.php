<?php header("Access-Control-Allow-Origin: *");

session_id('id');
session_start();
session_destroy();
session_write_close();