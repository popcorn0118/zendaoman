<?php

namespace WPDM\__;

class Template
{
    public $vars;

    function __construct(){
        return $this;
    }

    public static function locate($file, $tpldir = '', $fallback = ''){

        $template_dirs = array(
            get_stylesheet_directory().'/download-manager/',
            get_template_directory().'/download-manager/',
        );
        if($tpldir !== '') {
            $template_dirs[] = trailingslashit($tpldir);
			if(!substr_count(trailingslashit($tpldir), ABSPATH)) {
				$template_dirs[] = trailingslashit(get_stylesheet_directory() . '/download-manager/' . trim( $tpldir, '/' ));
				$template_dirs[] = trailingslashit(get_template_directory() . '/download-manager/' . trim( $tpldir, '/' ));
			}
        } else
            $template_dirs[] = '';

        if($fallback !== '')
            $template_dirs[] = trailingslashit($fallback);
		$template_dirs = apply_filters("wpdm_template_path", $template_dirs, $file);
        foreach ($template_dirs as $template_dir){
            if(file_exists($template_dir.$file)) {
	            return $template_dir . $file;
            }
        }
        //wpdmdd($file);
        return "";
    }

	public static function locate_url($file, $tpldir = '', $fallback = ''){
		$file = self::locate($file, $tpldir, $fallback);
		$url = str_replace(ABSPATH, site_url('/'), $file);
		return $url;
	}

    function assign($var, $val = null){
        if(is_array($var) && is_array($val)){
            foreach ($var as $index => $key){
                $this->vars[$key] = isset($val[$index]) ? $val[$index] : '';
            }
        } else if(is_array($var) && $val === null){
            foreach ($var as $key => $value){
                $this->vars[$key] = $value;
            }
        } else if(is_string($var))
            $this->vars[$var] = $val;
        return $this;
    }

    function fetch($template, $tpldir = '' , $fallback = ''){
        $template = self::locate($template, $tpldir);
        if(is_array($this->vars))
        extract($this->vars);
        ob_start();
        include $template;
        return ob_get_clean();
    }

    function display($template, $tpldir = '' , $fallback = ''){
        echo $this->fetch($template, $tpldir, $fallback);
    }

    function execute($code){
        ob_start();
        if(is_array($this->vars))
            extract($this->vars);
        echo $code;
        return ob_get_clean();
    }

    static function output($data, $vars, $tpldir = '')
    {
        if(strstr($data, '.php')) {
            $filename = self::locate($data, $tpldir);
            $data = file_get_contents($filename);
        }
        $data = str_replace(array_keys($vars), array_values($vars), $data);
        return $data;
    }

}

