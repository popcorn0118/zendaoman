<?php

use WPDM\__\__;

if(!defined("ABSPATH")) die("Shit happens!");
?>
<div id="wpdm-admin-page-header">
    <div id="wpdm-admin-main-header" class="<?=wpdm_valueof($params, 'class')?>">
        <div class="media">
            <div class="pull-right">
                <?php
                foreach($actions as $action){

                    $class = __::valueof($action, 'class', ['default' => 'primary btn-simple']);

                    $attrs = '';
                    if(isset($action['attrs'])) {
                        foreach ($action['attrs'] as $key => $value) {
                            $attrs .= "{$key}='{$value}' ";
                        }
                    }

                    if(wpdm_valueof($action, 'link')) {
                        $link = wpdm_valueof($action, 'link');
                        if(!substr_count($link, '#'))
                            $link = admin_url($link);
                        echo "<a class='btn btn-{$class} btn-sm' href='{$link}' {$attrs}>{$action['name']}</a> ";
                    }

                    if(wpdm_valueof($action, 'type') === 'submit') {
                        echo "<button type='submit' class='btn btn-{$class}' {$attrs}>{$action['name']}</button> ";
                    }

                    if(wpdm_valueof($action, 'type') === 'button') {

                        echo "<button type='button' class='btn btn-{$class}' {$attrs}>{$action['name']}</button> ";
                    }
                }
                ?>
            </div>
            <div class="media-body">
                <div class="media">
                    <div class="media-body">
                        <h3>
                           <?= __::is_url($icon) ? "<img src='{$icon}' />" : "<i class='fa fa-{$icon}'></i>" ?> <?= $title; ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(is_array($menus) && count($menus) > 0){ ?>
    <div id="wpdm-admin-sub-header" class="text-center">
            <ul class="nav nav-pills nav-wrapper-tabs" id="wpdm-page-header-tabs">

                <?php
                foreach($menus as $menu){
                    $attrs = '';
                    if(isset($menu['attrs'])) {
                        foreach ($menu['attrs'] as $key => $value) {
                            $attrs .= "{$key}='{$value}' ";
                        }
                    }
                    $link_endpoint = wpdm_valueof($menu, 'link');
                    $link = !preg_match("/#(.*)/", $link_endpoint, $result) ? admin_url($link_endpoint) : $link_endpoint;
                    $active = wpdm_valueof($menu, 'active') ? 'active' : '';
                    echo "<li><a class='nav-link {$active}' href='{$link}' {$attrs}>{$menu['name']}</a><span></span></li>";
                }
                ?>
            </ul>
    </div>
    <?php } ?>


</div>
