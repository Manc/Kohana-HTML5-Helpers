<?php defined('SYSPATH') or die('No direct script access.');
/**
 * More HTML helpers for Kohana 3.1 all made for HTML5.
 *
 * @author André Zahn
 */
class HTML extends Kohana_HTML {

	/**
	 * Creates a script link.
	 *
	 *     echo HTML::script('media/js/jquery.min.js');
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   mixed    protocol to pass to URL::base()
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE) {
		if (strpos($file, '://') === FALSE) {
			// Add the base URL
			$file = URL::base($protocol, $index).$file;
		}

		// Set the script link
		$attributes['src'] = $file;

		// Set the script type
		//$attributes['type'] = 'text/javascript';

		return '<script'.HTML::attributes($attributes).'></script>';
	}


	/**
	 * Creates a style sheet link element.
	 *
	 *     echo HTML::style('media/css/screen.css');
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   mixed    protocol to pass to URL::base()
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function style($file, array $attributes = NULL, $protocol = NULL, $index = FALSE) {
		if (strpos($file, '://') === FALSE) {
			// Add the base URL
			$file = URL::base($protocol, $index).$file;
		}

		// Set the stylesheet link
		$attributes['href'] = $file;

		// Set the stylesheet rel
		$attributes['rel'] = 'stylesheet';

		// Set the stylesheet type
		//$attributes['type'] = 'text/css';

		return '<link'.HTML::attributes($attributes).'>';
	}

	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 * Attributes will be sorted using HTML::$attribute_order for consistency.
	 *
	 *     echo '<div'.HTML::attributes($attrs).'>'.$content.'</div>';
	 *
	 * @param   array   attribute list
	 * @return  string
	 */
	public static function attributes(array $attributes = NULL) {
		if (empty($attributes)) return '';

		$sorted = array();
		foreach (HTML::$attribute_order as $key) {
			if (isset($attributes[$key])) {
				// Add the attribute to the sorted list
				$sorted[$key] = $attributes[$key];
			}
		}

		// Combine the sorted attributes
		$attributes = $sorted + $attributes;

		$compiled = '';
		foreach ($attributes as $key => $value) {
			if ($value === NULL) {
				// Skip attributes that have NULL values
				continue;
			}

			elseif ($value === '') {
				// Add the attribute without value
				$compiled .= ' '.$key;
			}

			else {
				// Add the attribute value
				$compiled .= ' '.$key.'="'.htmlspecialchars($value, ENT_QUOTES, Kohana::$charset).'"';
			}
		}

		return $compiled;
	}


	/**
	 * Generates an obfuscated version of an email address. Helps prevent spam
	 * robots from finding email addresses.
	 *
	 *     echo HTML::email_advanced($address, $at_alternative, $class_name);
	 *
	 * @param   string  email address
	 * @param   string  at sign replacement (eg. '(at)')
	 * @param   string  class name to wrap at sign with in span
	 * @return  string
	 * @uses    HTML::obfuscate
	 * @uses    HTML::email
	 */
	public static function email_advanced($email, $at_replacement, $at_wrap_class = NULL) {
		$at_replacement = HTML::obfuscate($at_replacement);
		if ($at_wrap_class !== NULL) {
			$at_replacement = '<span' . HTML::attributes(array('class' => $at_wrap_class)) . '>' . $at_replacement . '</span>';
		}

		$at_pos = strpos($email, '@');
		if ($at_pos !== FALSE) {
			return HTML::obfuscate(substr($email, 0, $at_pos)) . $at_replacement . HTML::obfuscate(substr($email, $at_pos + 1));
		} else {
			return HTML::email($email);
		}
	}



	/**
	 * Generates an obfuscated version of a string. Text passed through this
	 * method is less likely to be read by web crawlers and robots, which can
	 * be helpful for spam prevention, but can prevent legitimate robots from
	 * reading your content.
	 *
	 * Difference to original Kohana function:
	 * To make pages cacheable this version does not use a random algorythm.
	 * Instead of rand() this uses more or less random numbers from the MD5
	 * hash of the given string.
	 *
	 *     echo HTML::obfuscate($text);
	 *
	 * @param   string  string to obfuscate
	 * @return  string
	 */
	public static function obfuscate($string) {
		$hash = md5($string);
		$pseudo_random_numbers = array();
		foreach (str_split($hash) as $digit) {
			$pseudo_random_numbers[] = hexdec($digit);
		}

		$safe = '';
		$i = 0;
		foreach (str_split($string) as $letter) {
			$use_hash_index = $i % 32;
			$random = $pseudo_random_numbers[$use_hash_index]; // "static random" number between 0 and 15
			if ($random < 6) $safe .= '&#'.ord($letter).';'; // HTML entity code; chances: 6/16 = 37.5%
			elseif ($random < 10) $safe .= '&#x'.dechex(ord($letter)).';'; // Hex character code; chances: 5/16 = 31.25%
			else $safe .= $letter; // Raw (no) encoding; chances: 5/16 = 31.25%
			$i++;
		}

		return $safe;
	}


	/**
	 * Wraps text paragraphs in p tags
	 * @param string $string The input string.
	 * @param string $class [optional] Class name(s) added to p tag
	 * @return string The altered string.
	 */
	public static function nl2p($string, $class = '') {
		$class_attr = $class ? ' class="' . $class . '"' : '';
		return '<p' . $class_attr . '>'
			. preg_replace('#(<br\s*?/?>\s*?){2,}#', '</p>' . "\n" . '<p' . $class_attr . '>', self::nl2br($string))
			. '</p>';
	}


	/**
	 * nl2br
	 * @param string $string
	 * @return string
	 */
	public static function nl2br($string) {
		if (defined('PHP_MAJOR_VERSION') && ((PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION > 2) || PHP_MAJOR_VERSION > 5)) {
			return nl2br($string, FALSE);
		} else {
			return nl2br($string);
		}
	}


}
