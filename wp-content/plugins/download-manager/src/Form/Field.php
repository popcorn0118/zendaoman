<?php


namespace WPDM\Form;


use WPDM\__\__;

class Field
{

    static function heading($attrs)
    {
        $_attrs = "";
        $text = $attrs['text'];
        unset($attrs['text']);
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<div class=''>{$text}</div>";
    }

    static function hidden($attrs)
    {
        $_attrs = "";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='hidden' $_attrs />";
        return $text;
    }

    static function text($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='text' $_attrs />";
        return $text;
    }

    static function textarea($attrs, $value = '')
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $_value) {
            $_attrs .= "{$key}='{$_value}' ";
        }
        $value = stripslashes_deep($value);
        $text = "<textarea $_attrs>{$value}</textarea>";
        return $text;
    }

    static function number($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='number' $_attrs />";
        return $text;
    }

    static function email($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<input type='email' $_attrs />";
    }

    static function password($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
	    $strength = '';
	    if(isset($attrs['strength']) && $attrs['strength'] === 1) {
		    $strength = '<div  class="progress bg-gray-50" style="height: 5px;position: absolute;width: calc(100% - 40px);background: #eee;margin-top: -5px;"><div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 10%;height: 5px;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div></div>';
		    $strength .= '<script type="text/javascript" src="'.WPDM_ASSET_URL.'js/password-strength.js"></script>';
	    }
	    return "<input type='password' {$_attrs} />{$strength}";
    }

    static function checkbox($attrs){
        $_attrs = "";
        if(isset($attrs['class'])) unset($attrs['class']);
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        $_options = "";
        foreach ($options as $value => $label){
            $_options .= "<div><label class='d-block option-label'><input type='checkbox' $_attrs value='{$value}'> {$label}</label></div>\r\n";
        }
        return $_options;
    }

    static function radio($attrs){
        $_attrs = "";
        if(isset($attrs['class'])) unset($attrs['class']);
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        $_options = "";
        foreach ($options as $value => $label){
            $_options .= "<div><label class='d-block option-label'><input type='radio' $_attrs value='{$value}'> {$label}</label></div>\r\n";
        }
        return $_options;
    }

    static function select($attrs, $value = '')
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $_value) {
            $_attrs .= "{$key}='{$_value}' ";
        }
        $_options = "";
        foreach ($options as $_value => $label) {
            $_options .= "<option value='{$_value}' " . selected($_value, $value, false) . ">{$label}</option>\r\n";
        }
        return "<select $_attrs>\r\n{$_options}\r\n</select>";
    }

    static function meidapicker($attrs, $value = '')
    {
        ob_start();
        $_attrs = '';
        if (is_array($attrs)) {
            foreach ($attrs as $attr => $value) {
                $_attrs .= "$attr='$value' ";
            }
        }
        ?>
        <div class="input-group">
            <input <?php echo $_attrs; ?> type="url" value="<?php echo $value; ?>"/>
            <span class="input-group-append">
                <button class="btn btn-secondary btn-media-upload" type="button" rel="#<?php echo $attrs['id']; ?>"><i
                            class="far fa-image"></i></button>
            </span>
        </div>
        <?php
        return ob_get_clean();
    }

    static function reCaptcha($attrs){
        ob_start();
        ?>
        <div class="form-group row">
            <div class="col-sm-12">
                <input type="hidden" id="<?php echo esc_attr($attrs['id']) ?>" name="<?php echo esc_attr($attrs['name']) ?>" value=""/>
                <script src="https://www.google.com/recaptcha/enterprise.js?onload=onloadCallback&render=explicit"
                        async defer></script>
                <div id="<?php echo esc_attr($attrs['id']) ?>_field" class="recap_field"></div>
                <style>
                    .wp-core-ui .recap_field{ padding-bottom: 12px; }
                    .wp-core-ui #<?php echo esc_attr($attrs['id']) ?>_field iframe{ transform: scale(0.89); margin-left: -16px; margin-bottom: 12px; clear:both; }
                    .w3eden #<?php echo esc_attr($attrs['id']) ?>_field iframe{ transform: scale(1.16); margin-left: 24px; margin-top: 5px; margin-bottom: 5px; }
                    .w3eden #<?php echo esc_attr($attrs['id']) ?>_field{ padding-bottom: 10px !important; }
                </style>
                <script type="text/javascript">
                    var verifyCallback = function (response) {
                        jQuery('#<?php echo esc_js($attrs['id']) ?>').val(response);
                    };
                    var widgetId2;
                    var onloadCallback = function () {
                        grecaptcha.enterprise.render('<?php echo esc_js($attrs['id']) ?>_field', {
                            'sitekey': '<?php echo esc_js(get_option('_wpdm_recaptcha_site_key')); ?>',
                            'callback': verifyCallback,
                            'theme': 'light'
                        });
                    };
                </script>
            </div>

        </div>
        <?php
        $captcha = ob_get_clean();
        return $captcha;
    }

    static function custom($field, $attrs)
    {
        return call_user_func($field, $attrs);
    }

}
