<?php

declare(strict_types=1);

/**
 * @link http://digital.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Slim\Flash\Messages;
use Flextype\Component\I18n\I18n;
use function Flextype\Component\I18n\__;

// Add Admin Navigation
$flextype->registry->set('plugins.admin.settings.navigation.extends.themes', ['title' => __('admin_themes'),'icon' => 'fas fa-palette', 'link' => $flextype->router->pathFor('admin.themes.index')]);

/**
 * Add themes service to Flextype container
 */
$flextype['themes'] = static function ($container) use ($flextype, $app) {
    return new Themes($flextype, $app);
};

/**
 * Init themes
 */
$flextype['themes']->init($flextype, $app);

// Add ThemesController
$flextype['ThemesController'] = static function ($container) {
    return new ThemesController($container);
};

// Add TemplatesController
$flextype['TemplatesController'] = static function ($container) {
    return new TemplatesController($container);
};