<?php
return array(
    'groups' => array(
        'common-main' => array(
		    'assets' => array(
		        'static/js/jquery.min.js',
		        'static/js/plugins/touchwipe/touchwipe.min.js',
		        'static/js/plugins/nicescroll/jquery.nicescroll.min.js',
                'static/js/plugins/nicescroll/jquery.nicescroll.min.js',
                'static/js/plugins/validation/jquery.validate.min.js',
                'static/js/plugins/validation/additional-methods.min.js',
				'static/js/plugins/icheck/jquery.icheck.min.js',
				'static/js/bootstrap.min.js',
				'static/js/eakroko.js',
		    ),
	
		    'filters' => array(
		      //'js_min' // Specify the filter by name, not by class.
		    ),
		    // NB: must be rewritable
	        'output' => 'common-main.js'
	     ),
	     'common-css-main' => array(
		    'assets' => array(
		        'static/css/bootstrap.min.css',
		        'static/css/plugins/icheck/all.css',
		        'static/css/style.css',
                'static/css/themes.css',
		    ),
	
		    'filters' => array(
		      //'js_min' // Specify the filter by name, not by class.
		    ),
		    // NB: must be rewritable
	        'output' => 'common-css-main.css'
	     ),
    ),
    'filters' => array(
        'js_min'      => 'Assetic\Filter\JSMinFilter',
	    'css_import'  => 'Assetic\Filter\CssImportFilter',
	    'css_min'     => 'Assetic\Filter\CssMinFilter',
	    'css_rewrit'  => 'Assetic\Filter\CssRewriteFilter',
	    'emed_css'    => 'Assetic\Filter\PhpCssEmbedFilter',
	    'coffe_script'=> 'Assetic\Filter\CoffeeScriptFilter',
	    'less_php'    => 'Assetic\Filter\LessphpFilter',
    ),
    'assets' => array(
    
    )

);