<?php


function kapsule_list()
{
    wp_enqueue_script('kapsule', get_template_directory_uri() .'/js/admin.js'); ?>
    <div class="admin_fields width-100 m-0 wp-core-ui">
        <div class="d-flex flex-row justify-content-between mb-2">
            <div class="d-flex flex-column justify-content-between align-content-between">
                <h1 class="mb-2">Капсулы</h1>
                <form method="post" enctype="multipart/form-data" action="sendKapsuls" name="sendKapsuls">
                    <div>
                        <label>Загрузите файл c капсулами согласно схеме: <br></label>
                        <input type="file" class="loadXls" name="loadXls"/>
                    </div>
                    <input type="submit" name="sendKapsuls" class="submit" />
                </form>
            </div>
        </div>
    </div>
    <?php
}
add_action( 'wp_ajax_sendKapsuls', 'sendKapsuls' );
add_action( 'wp_ajax_nopriv_sendKapsuls', 'sendKapsuls' );
//if ( count( $arrForPost ) !== 0 ) {
//    $id = wp_insert_post(
//        wp_slash(
//            array(
//                'post_title'  => get_the_title( $_POST['post_id'] ),
//                'post_parent' => $_POST['post_id'],
//                'post_status' => 'register_app',
//                'post_type'   => 'contestsapplications',
//            )
//        )
//    );
//    if ( $id ) {
//        foreach ( $arrForPost as $key => $value ) {
//            update_post_meta( $id, $key, $value );
//        }
//        update_post_meta( $id, 'join', $_POST['post_id'] );
//    }
//}