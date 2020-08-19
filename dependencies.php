<?php

declare(strict_types=1);

/**
 * @link http://digital.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype\Plugin\ThemesAdmin;

use Flextype\Plugin\ThemesAdmin\Controllers\ThemesController;
use Flextype\Plugin\ThemesAdmin\Controllers\TemplatesController;

use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Flextype\Component\I18n\I18n;
use function Flextype\Component\I18n\__;

// Add Admin Navigation
$flextype->container('registry')->set('plugins.admin.settings.navigation.extends.themes', ['title' => __('themes_admin_themes'),'icon' => 'fas fa-palette', 'link' => $flextype->container('router')->pathFor('admin.themes.index')]);

// Add ThemesController
$flextype->container()['ThemesController'] = static function () use ($flextype) {
    return new ThemesController($flextype);
};

// Add TemplatesController
$flextype->container()['TemplatesController'] = static function () use ($flextype) {
    return new TemplatesController($flextype);
};

$_flextype_menu = ($flextype->container('registry')->has('plugins.admin.settings.flextype_menu')) ? $flextype->container('registry')->get('plugins.admin.settings.flextype_menu') : [];

if ($flextype->container('registry')->has('flextype.settings.url') && $flextype->container('registry')->get('flextype.settings.url') != '') {
    $site_url = $flextype->container('registry')->get('flextype.settings.url');
} else {
    $site_url = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
}

$flextype->container('registry')->set('plugins.admin.settings.flextype_menu',
                       array_merge($_flextype_menu,
                        [0 => ['link' => ['url' => $site_url, 'title' => __('themes_admin_view_site'), 'is_external' => true, 'icon' => 'fas fa-globe']]]));
