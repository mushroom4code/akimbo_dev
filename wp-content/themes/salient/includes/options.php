<?php


function kapsule_list() {
	wp_enqueue_script( 'kapsule', get_template_directory_uri() . '/js/admin.js' );
	$kapsulList = get_posts( [
		'post_type'   => 'page',
		'meta_key'    => 'kapsula',
		'meta_value'  => 'true',
		'numberposts' => - 1,
	] )
	?>
    <style>.input__wrapper {
            width: 100%;
            position: relative;
            margin: 15px 0;
            text-align: center;
        }

        .input__file {
            opacity: 0;
            visibility: hidden;
            position: absolute;
        }

        .input__file-icon-wrapper {
            height: 30px;
            width: 30px;
            margin-right: 15px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            border-right: 1px solid #fff;
        }

        .input__file-button-text {
            line-height: 1;
            margin-top: 1px;
        }

        .input__file-button {
            width: 100%;
            max-width: 190px;
            padding: 8px 15px;
            background: #1bbc9b;
            color: #fff;
            font-weight: 700;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: start;
            -ms-flex-pack: start;
            justify-content: flex-start;
            border-radius: 3px;
            cursor: pointer;
        }

        .icon {
            margin-right: 0.5rem;
            border-right: 1px solid #fff;
            padding-right: 5px;
        }

        .button-download, .button-download:hover {
            cursor: pointer;
            padding: 13px 15px;
            background-color: #2271b1;
            text-align: center;
            border-radius: 3px;
            margin-right: 1rem;
            text-decoration: none;
            color: white
        }
    </style>
    <div class="admin_fields width-100 m-0 wp-core-ui">
        <h1 style="margin-bottom: 2rem">Капсулы - загрузка</h1>
        <form method="POST" enctype="multipart/form-data" action="sendKapsuls"
              style="margin-bottom: 3rem; display: flex; align-items: center; flex-direction: row"
              name="sendKapsuls">
            <div style="margin-right: 3rem;">
                <div class="input__wrapper">
                    <input type="file" id="input__file" class="input input__file loadXls" name="loadXls">
                    <label for="input__file" class="input__file-button">
                        <span class="input__file-icon-wrapper">
                            <span class="dashicons dashicons-cloud-upload"></span>
                        </span>
                        <span class="input__file-button-text">Выберите файл</span>
                    </label>
                </div>
            </div>
            <input name="action" value="sendKapsulsAjax" type="hidden">
            <div style="display: flex;flex-direction: row;align-items: center">
                <div name="sendKapsulsSubmit" class="submit button-download">
                    <span class="dashicons dashicons-saved icon"></span>
                    Подтвердить загрузку капсул
                </div>
                <a href="/wp-content/themes/salient/img/Задание на Осень 23.xls" class="button-download" download>
                    <span class="dashicons dashicons-download icon"></span>
                    Скачать пример файла</a>
            </div>
        </form>
        <h1 class="mb-2">Капсулы - текущий список</h1>
        <p>если вы загрузили файл и они не появились в списке - обновите страницу</p>
        <div style="background-color: white;
         padding: 10px;display: flex;flex-direction: column;">
			<?php if ( ! empty( $kapsulList ) ) {
				foreach ( $kapsulList as $item ) { ?>
                    <p>
						<?= $item->post_title ?>
                        <a href="/wp-admin/post.php?post=<?= $item->ID ?>&action=edit">
                            Редактировать </a></p>
				<?php }
			} ?>
        </div>
    </div>
    <script>
        let inputs = document.querySelectorAll('.input__file');
        Array.prototype.forEach.call(inputs, function (input) {
            let label = input.nextElementSibling,
                labelVal = label.querySelector('.input__file-button-text').innerText;

            input.addEventListener('change', function (e) {
                let countFiles = '';
                if (this.files && this.files.length >= 1)
                    countFiles = this.files.length;

                if (countFiles)
                    label.querySelector('.input__file-button-text').innerText = 'Выбрано файлов: ' + countFiles;
                else
                    label.querySelector('.input__file-button-text').innerText = labelVal;
            });
        });
    </script>
	<?php
}

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