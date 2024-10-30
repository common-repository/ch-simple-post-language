<div class="wrap">
    <h1>Simple Post Language</h1>
    <p>
        Support by mail: <a href="mailto:chris@haensel.pro">chris@haensel.pro</a><br>
        More information on my website: <a href="https://haensel.pro/wordpress-ch-simple-post-language-plugin" target="_blank">https://haensel.pro/wordpress-ch-simple-post-language-plugin</a>
    </p>
    <hr>
    <p>
    Below you can set which languages should be displayed in the drop down menu in the page/ post editor.<br>
        If you don't select any specific languages, all languages will be shown in the dropdown.
    </p>

    <p>
        <form method="POST">
	        <input type="hidden" name="saveLanguages" value="yep">

            <h3>Languages</h3>
            Which languages should be shown in the dropdown?
    <p>
    <p style="column-count: 4">
		<?php
		foreach ( $html_languages_all->languageCodes as $lang ):
			$disabled = "";
			$lang_checked = "";
			$lang->display_code = $lang->code;
			if ( $lang->code == "" ) {
				$disabled = "disabled";
				$lang_checked = 'checked="checked"';
				$lang->display_code = get_bloginfo("language");
			}

			if ( in_array( $lang->code, $html_languages_activated ) ) {
				$lang_checked = 'checked="checked"';
			}
			?>
            <label for="lang_<?= $lang->code ?>">
                <input type="checkbox" name="ch_html_languages[]" id="lang_<?= $lang->code ?>" value="<?= $lang->code ?>" <?= $lang_checked ?> <?= $disabled ?>>
				<?= $lang->language ?> (<?= $lang->display_code ?>)
            </label>
            <br>
		<?php


		endforeach;

		?>
    </p>

    <p>
        <input type="submit" value="Save" class="button button-primary button-large">
    </p>
    </form>
    </p>
</div>