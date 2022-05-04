<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     3.5.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $product;
if (!comments_open()) {
    return;
}
$styleTab = '';
if (!isset($_COOKIE['tab_comment_new']) || $_COOKIE['tab_comment_new'] === 'false') {
    $styleTab = 'style="display:none!important;"';
}
?>
<div id="reviews" class="woocommerce-Reviews">
    <div id="comments">
		<span class="woocommerce-Reviews-title" style="font-size: 17px;"><?php
            if (get_option('woocommerce_enable_review_rating') === 'no' && ($count !== $product->get_review_count())) {
                _e('Reviews', 'woocommerce');
            } ?>
        </span>
        <?php

        if (have_comments()) { ?>
            <ol class="commentlist" <?= $styleTab ?>>
                <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array('callback' => 'woocommerce_comments'))); ?>
            </ol>
            <?php if (get_comment_pages_count() > 1) {
                echo '<nav class="woocommerce-pagination" ' . $styleTab . '>';
                paginate_comments_links(apply_filters('woocommerce_comment_pagination_args', array(
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                    'type' => 'list',
                )));
                echo '</nav>';
            }
        } else { ?>
            <p class="woocommerce-noreviews"><?php _e('There are no reviews yet.', 'woocommerce'); ?></p>
        <?php } ?>
    </div>

    <?php if (get_option('woocommerce_review_rating_verification_required') === 'no' ||
        wc_customer_bought_product('', get_current_user_id(), $product->get_id())) { ?>
        <div id="review_form_wrapper">
            <div id="review_form">
                <?php
                $commenter = wp_get_current_commenter();

                $comment_form = array(
                    'title_reply' => have_comments() ? __('Add a review', 'woocommerce') : sprintf(__('Be the first to review &ldquo;%s&rdquo;', 'woocommerce'), get_the_title()),
                    'title_reply_to' => __('Leave a Reply to %s', 'woocommerce'),
                    'title_reply_before' => '<span id="reply-title" class="comment-reply-title" style="font-size: 14px;">',
                    'title_reply_after' => '</span>',
                    'comment_notes_after' => '',
                    'fields' => array(
                        'author' => '<p class="comment-form-author">' . '<label for="author">'
                            . esc_html__('Name', 'woocommerce') . '&nbsp;<span class="required">*</span></label> ' .
                            '<input id="author" name="author" type="text" value="'
                            . esc_attr($commenter['comment_author']) . '" size="30" required /></p>',
                        'email' => '<p class="comment-form-email"><label for="email">'
                            . esc_html__('Email', 'woocommerce') . '&nbsp;<span class="required">*</span></label> ' .
                            '<input id="email" name="email" type="email" value="'
                            . esc_attr($commenter['comment_author_email']) . '" size="30" required /></p>',
                    ),
                    'label_submit' => __('Submit', 'woocommerce'),
                    'logged_in_as' => '',
                    'comment_field' => '',
                );

                if ($account_page_url = wc_get_page_permalink('myaccount')) {
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a review.', 'woocommerce'), esc_url($account_page_url)) . '</p>';
                }

                if (get_option('woocommerce_enable_review_rating') === 'yes') {
                    $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">'
                        . esc_html__('Your rating', 'woocommerce') . '</label>
                        <select name="rating" id="rating" required>
							<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
							<option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
							<option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
							<option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
							<option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
							<option value="1">' . esc_html__('Very poor', 'woocommerce') . '</option>
						</select></div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Your review', 'woocommerce') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form)); ?>
            </div>
        </div>
    <?php } else { ?>
        <p class="woocommerce-verification-required">
            <?php _e('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'); ?>
        </p>
    <?php } ?>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('a.page-numbers').on('click', function () {
            let new_link = $(this).attr('href');
            window.location.replace(new_link);
        });
    });
    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
    function setCookie(name, value, options = {}) {

        options = {
            path: '/',
            // при необходимости добавьте другие значения по умолчанию
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }

        document.cookie = updatedCookie;
    }
    function deleteCookie(name) {
        setCookie(name, "", {
            'max-age': -1
        })
    }
    document.addEventListener("DOMContentLoaded", function () {
        jQuery(document).ready(function ($) {
            $('li a[href="#tab-reviews"]').click(function () {
                if (getCookie('tab_comment_new') === 'false' || getCookie('tab_comment_new') === undefined) {
                    deleteCookie('tab_comment_new');
                    setCookie('tab_comment_new','true');
                    $('.commentlist').show(200);
                    $('.woocommerce-pagination').show(200);
                } else if (getCookie('tab_comment_new') === 'true') {
                    deleteCookie('tab_comment_new');
                    setCookie('tab_comment_new','false');
                    $('.commentlist').hide(200);
                    $('.woocommerce-pagination').hide(200).attr('style', 'display:none!important;');
                }
            });
        });
    });
</script>