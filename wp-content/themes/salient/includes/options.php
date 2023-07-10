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
         padding: 10px;display: flex;flex-direction: column; max-width: 500px;">
			<?php if ( ! empty( $kapsulList ) ) {
				foreach ( $kapsulList as $item ) { ?>
                    <div style="display: flex;
                    flex-direction: row;
                    justify-content: space-between;
                    margin-bottom: 1rem">
                        <b style="font-size:16px"><?= $item->post_title ?></b>
                        <div style="display: flex; flex-direction: row; justify-content: space-between;max-width: 50%;">
                            <a style="margin: 0 20px" href="/wp-admin/post.php?post=<?= $item->ID ?>&action=edit">
                                Редактировать </a>
                            <a style="color:red;margin: 0 20px"
                               href="<?= get_delete_post_link( $item->ID ) ?>">Удалить </a>
                        </div>
                    </div>
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


function customSettings() {
	wp_enqueue_script( 'adminSetting', get_template_directory_uri() . '/js/admin.js' );
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
        <h1 style="margin-bottom: 2rem">Дополнительные настройки сайта</h1>
        <form method="POST" enctype="multipart/form-data" action="sendSettings"
              style="margin-bottom: 3rem; display: flex; flex-direction: column"
              name="sendSettings">
            <input name="action" value="sendSettingsAjax" type="hidden">
            <input name="сheckedInfoOtpusk" value="<?= get_option('сheckedInfoOtpusk') ?? 'off'?>" type="hidden">
            <label style="margin-bottom: 1rem">
                <h3><b>Показать сообщение (над шапкой сайта)</b></h3>
                <input name="ChangedCheckedInfoOtpusk"
                    <?= (get_option('сheckedInfoOtpusk') == 'true' ||
                         get_option('сheckedInfoOtpusk') == 'on') ? 'checked' : ''?>
                       type="checkbox"/>
            </label>
            <label>
                <h3><b>Текст сообщения</b></h3>
                <textarea cols="120" rows="5" name="textInfoOtpusk"><?= get_option('textInfoOtpusk') ?? ''?></textarea>
            </label>
            <div style="display: flex;flex-direction: row;align-items: center">
                <div name="sendSettingsAjax" class="submit button-download">
                    <span class="dashicons dashicons-saved icon"></span>
                    Cохранить настройки
                </div>
            </div>
        </form>
    </div>
	<?php
}
