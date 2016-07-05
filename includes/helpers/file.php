<?php
/**
 * WP Download Codes Plugin
 * 
 * FILE
 * includes/helpers/file.php
 *
 * DESCRIPTION
 * Functionality related to handling of files (locations, file types, file size formatting, etc.).
 *
 */
 
 /**
 * Returns the full path of the download file location.
 */
function dc_file_location() {
	// Get location of download file (for compatibility of older versions)
	$dc_file_location = ( '' == get_option( 'dc_file_location' ) ? get_option( 'dc_zip_location' ) : get_option( 'dc_file_location' ) );

	// Check if location is an absolute or relative path
	if ( strlen( $dc_file_location ) > 0 && '/' == substr( $dc_file_location, 0, 1 ) ) {
		// Absolute locations are returned directly
		return $dc_file_location;
	}
	else {
		// Relative locations are returned with the respective upload path directory
		$wp_upload_dir = wp_upload_dir();
		$upload_path = get_option( 'upload_path' );
		
		if ( ( strlen( $upload_path ) > 0 ) && ( substr( $wp_upload_dir['basedir'], 0, strlen( $upload_path ) ) == $upload_path ) ) {
			return  $upload_path . '/' . $dc_file_location;
		}
		else {
			return $wp_upload_dir['basedir'] . '/' . $dc_file_location;
		}
	}
}

/**
 * Returns a list of allowed file types.
 */
function dc_file_types() {
	$str_file_types = get_option( 'dc_file_types' );
	
	if ( '' == $str_file_types ) {
		$arr_file_types = explode( ',', DC_FILE_TYPES);
	}
	else {
		$arr_file_types = explode( ',', $str_file_types );
	}
	
	// Trim white space
	array_walk($arr_file_types, 'dc_trim_value');
	
	return $arr_file_types;
}

function dc_get_files() {
	// Get zip files in download folder
	$files = scandir( dc_file_location() );
	return $files;
}

function dc_get_file_count() {
	// Give 3rd parties a chance to short circuit and provide a different location.
	if ( ( $alt_count = apply_filters( 'dc_file_count', false, $release ) ) !== false ) {
		return $alt_count;
	}

	$files = dc_get_files();
	$num_download_files = 0;
	foreach ( $files as $filename ) {
		if ( in_array(strtolower( substr($filename,-3) ), dc_file_types() ) ) {
			$num_download_files++;
		}
	}
	return $num_download_files;
}

function dc_file_select( $release ) {
	// Give 3rd parties a chance to short circuit and provide a different location.
	if ( ( $alt_select = apply_filters( 'dc_file_select', false, $release ) ) !== false ) {
		return $alt_select;
	}

	$files = dc_get_files();

	$select =  dc_file_location() . ' <select name="filename" id="release-file">-->';

	// Get array of allowed file types/extensions
	$allowed_file_types = dc_file_types();

	// List all files matching the allowed extensions
	foreach ( $files as $filename ) {
		$file_extension_array = preg_split( '/\./', $filename );
		$file_extension = strtolower( $file_extension_array[ sizeof( $file_extension_array ) - 1 ] );
		if ( in_array( $file_extension, $allowed_file_types ) ) {
			$select .= '<option' . ( $filename == $release->filename ? ' selected="selected"' : '' ) . '>' . $filename . '</option>';
		}
	}
	$select .= '</select>';
	return $select;
}

function dc_get_download_size( $filename ) {
	// Give 3rd parties a chance to short circuit and provide a different location.
	if ( ( $alt_size = apply_filters( 'dc_download_size', false, $filename ) ) !== false ) {
		return $alt_size;
	}

	return format_bytes( filesize( dc_file_location() . $filename ) );
}

/**
 * Converts bytes into meaningful file size
 */
function format_bytes( $filesize ) 
{
    $units = array( ' B', ' KB', ' MB', ' GB', ' TB' );
    for ( $i = 0; $filesize >= 1024 && $i < 4; $i++ ) $filesize /= 1024;
    return round($filesize, 2) . $units[$i];
}

/**
 * Returns the MIME content type of a given file.
 */
function get_mime_content_type( $file )
{
	$mime_types = array(
			"pdf"=>"application/pdf"
			,"exe"=>"application/octet-stream"
			,"zip"=>"application/zip"
			,"docx"=>"application/msword"
			,"doc"=>"application/msword"
			,"xls"=>"application/vnd.ms-excel"
			,"ppt"=>"application/vnd.ms-powerpoint"
			,"gif"=>"image/gif"
			,"png"=>"image/png"
			,"jpeg"=>"image/jpg"
			,"jpg"=>"image/jpg"
			,"mp3"=>"audio/mpeg"
			,"wav"=>"audio/x-wav"
			,"mpeg"=>"video/mpeg"
			,"mpg"=>"video/mpeg"
			,"mpe"=>"video/mpeg"
			,"mov"=>"video/quicktime"
			,"avi"=>"video/x-msvideo"
			,"3gp"=>"video/3gpp"
			,"css"=>"text/css"
			,"jsc"=>"application/javascript"
			,"js"=>"application/javascript"
			,"php"=>"text/html"
			,"htm"=>"text/html"
			,"html"=>"text/html"
	);

	$extension = strtolower(end(explode('.',$file)));
	
	return $mime_types[$extension];
}
?>