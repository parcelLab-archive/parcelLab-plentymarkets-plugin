<?php
namespace ParcelLab\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Templates\Twig;

/**
 * Class ContentController
 *
 * @package ParcelLab\Controllers
 */
class ContentController extends Controller
{

	/**
	 * @param Twig $twig
	 * @return string
	 */
	public function sayHello(Twig $twig): string
	{
		return '';
	}
}