<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 21/7/16
 * Time: 6:58 AM
 */
class FileUploader
{
	public static function Save($postName) {
		// Undefined | Multiple Files | $_FILES Corruption Attack
		// If this request falls under any of them, treat it invalid.
		if (
			!isset($_FILES[$postName]['error']) ||
			is_array($_FILES[$postName]['error'])
		) {
			return null;
		}

		// Check $_FILES['upfile']['error'] value.
		switch ($_FILES[$postName]['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				return null;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return null;
			default:
				return null;
		}

		// You should also check filesize here.
		if ($_FILES[$postName]['size'] > 52428800) { //50MB
			return null;
		}

		// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
		// Check MIME Type by yourself.
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		if (false === $ext = array_search(
				$finfo->file($_FILES[$postName]['tmp_name']),
				array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
					'pdf' => 'application/pdf',
					'txt' => 'text/plain',
					'doc' => 'application/msword',
					'mp4' => 'video/mp4',
					'mov' => 'video/mov'
				),
				true
			)) {
			return null;
		}

		// You should name it uniquely.
		// DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
		// On this example, obtain safe unique name from its binary data.
		$fileID = sha1_file($_FILES[$postName]['tmp_name']);

		if (!move_uploaded_file(
			$_FILES[$postName]['tmp_name'],
			sprintf(PUB_PATH.'/media/%s.%s',
				$fileID,
				$ext
			)
		)) {
			return null;
		}

		return $fileID.'.'.$ext;
	}
}