<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * More form helpers for Kohana 3.1 all made for HTML5.
 *
 * @author André Zahn
 */
class Form extends Kohana_Form {

	/**
	 * Creates a checkbox form input.
	 *
	 *     echo Form::checkbox('remember_me', 1, (bool) $remember);
	 *
	 * @param   string   input name
	 * @param   string   input value
	 * @param   boolean  checked status
	 * @param   array    html attributes
	 * @return  string
	 * @uses    Form::input
	 */
	public static function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL) {
		$attributes['type'] = 'checkbox';

		if ($checked === TRUE) $attributes['checked'] = '';

		return Form::input($name, $value, $attributes);
	}


	/**
	 * Creates a form input. If no type is specified, a "text" type input will
	 * be returned.
	 *
	 *     echo Form::input('username', $username);
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function input($name, $value = NULL, array $attributes = NULL) {
		// Set the input name
		if (!empty($name)) $attributes['name'] = $name;

		// Set the input value
		$attributes['value'] = $value;

		if (!isset($attributes['type'])) {
			// Default type is text
			$attributes['type'] = 'text';
		}

		return '<input'.HTML::attributes($attributes).'>';
	}


	/**
	 * Creates a email form input.
	 *
	 *     echo Form::email('email');
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Form::input
	 */
	public static function email($name, $value = NULL, array $attributes = NULL) {
		$attributes['type'] = 'email';
		return Form::input($name, $value, $attributes);
	}


}