<?php
/**
 * User: shahnuralam
 * Date: 17/11/18
 * Time: 1:09 AM
 */
if (!defined('ABSPATH')) die();
$class = '';
if(isset($attrs['class'])) {
    $class = $attrs['class'];
    unset($attrs['class']);
}

?>
<div class="card <?= $class ?>" <?php foreach ($attrs as $atname => $atval) { echo "$atname='$atval' "; } ?>>
    <?php if($heading != ''){ ?>
    <div class="card-header"><strong><?php echo $heading; ?></strong></div>
    <?php }
    if(count($content) > 0){
        foreach ($content as $html) {
        ?>
        <div class="card-body"><?php echo $html; ?></div>
    <?php
        }
    }
    if($footer != ''){
        ?><div class="card-footer"><?php echo $footer; ?></div><?php
    }
    ?></div>
