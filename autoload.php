<?php

// Using a closure to keep from tainting the global scope
call_user_func(function() {
	if (!class_exists('SplClassLoader')) {
		require __DIR__.'/SplClassLoader.php';
	}
	$classLoader = new SplClassLoader('HAProxy', __DIR__.'/src');
	$classLoader->register();
});