<?php
/**
 * User: shahnuralam
 * Date: 17/11/18
 * Time: 1:06 AM
 */

namespace WPDM\__;


use WPDM\__\Template;

class UI
{
	static $elements = [];

	static function el($element) {
		self::$elements['name'] = $element;
	}
    static function div($content, $class = '', $attrs = [])
    {
        $class = $class ? " class='{$class}'" : '';
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= " {$name}='$val'";
        }
        return "<div{$class} $_attrs>{$content}</div>";
    }

    static function html($tag, $attrs = [], $content = '')
    {
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= " {$name}='$val'";
        }
        return "<$tag $_attrs>{$content}</$tag>";
    }

    static function a($link, $label = '', $attrs = [])
    {
        $label = $label ? $label : $link;
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= esc_attr($name) . '="'.esc_attr($val).'"';
        }
        return '<a href="'.esc_attr($link).'"'.$_attrs.'>'.$label.'</a>';
    }

    static function button($label, $attrs = []){
        $button = "<button";
        foreach ($attrs as $name => $val){
            $button .= " {$name}='$val'";
        }
        $button .= ">{$label}</button>";
        return $button;
    }

    static function card($heading = '', $content = [], $footer = '', $attrs = []){
        $template = new Template();
        return $template->assign('heading', $heading)
            ->assign('attrs', $attrs)
            ->assign('content', $content)
            ->assign('footer', $footer)
            ->fetch("views/ui-blocks/card.php", __DIR__);
    }

    static function table($thead, $data, $css){
        $template = new Template();
        return $template->assign('thead', $thead)
            ->assign('data', $data)
            ->assign('css', $css)
            ->fetch("views/ui-blocks/table.php", __DIR__);
    }

    static function img($src, $alt = '', $attrs = [])
    {
        // Escape every interpolated value: $src as a URL, and $alt plus all
        // attribute names/values as HTML attributes, so a caller-supplied value
        // (e.g. a shortcode attribute) cannot break out of the quoted attribute
        // and inject markup such as an onerror handler.
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= " " . esc_attr($name) . "='" . esc_attr($val) . "'";
        }
        return "<img src='" . esc_url($src) . "' alt='" . esc_attr($alt) . "' {$_attrs} />";
    }

    static function minifyHTML($html)
    {
        $html = str_replace(["\r", "\n"], "", $html);
        return $html;
    }

}
