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
flextype('registry')->set('plugins.admin.settings.navigation.extends.themes', ['title' => __('themes_admin_themes'),'icon' => 'fas fa-palette', 'link' => flextype('router')->pathFor('admin.themes.index')]);

// Add ThemesController
flextype()->container()['ThemesController'] = static function () {
    return new ThemesController();
};

// Add TemplatesController
flextype()->container()['TemplatesController'] = static function () {
    return new TemplatesController();
};

$_flextype_menu = (flextype('registry')->has('plugins.admin.settings.flextype_menu')) ? flextype('registry')->get('plugins.admin.settings.flextype_menu') : [];

if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') != '') {
    $site_url = flextype('registry')->get('flextype.settings.url');
} else {
    $site_url = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
}

flextype('registry')->set('plugins.admin.settings.flextype_menu',
                       array_merge($_flextype_menu,
                        [0 => ['link' => ['url' => $site_url, 'title' => __('themes_admin_view_site'), 'is_external' => true, 'icon' => 'fas fa-globe']]]));
