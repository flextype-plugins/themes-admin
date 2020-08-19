<?php

declare(strict_types=1);

namespace Flextype\Plugin\ThemesAdmin\Controllers;

use Flextype\Component\Arrays\Arrays;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_merge;
use function array_replace_recursive;
use function count;
use function Flextype\Component\I18n\__;
use function is_array;
use function trim;

class ThemesController
{
    /**
     * Flextype Application
     */
     protected $flextype;

    /**
     * __construct
     */
     public function __construct($flextype)
     {
         $this->flextype = $flextype;
     }

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        return $this->flextype->container('twig')->render(
            $response,
            'plugins/themes-admin/templates/extends/themes/index.html',
            [
                'menu_item' => 'themes',
                'themes_list' => $this->flextype->container('registry')->get('themes'),
                'links' =>  [
                    'themes' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.themes.index'),
                        'title' => __('themes_admin_themes'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'themes_get_more' => [
                        'link' => 'https://github.com/flextype/themes',
                        'title' => __('themes_admin_get_more_themes'),
                        'target' => '_blank',
                    ],
                ],
            ]
        );
    }

    /**
     * Сhange theme status process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function activateProcess(Request $request, Response $response) : Response
    {
        // Get data from the request
        $post_data = $request->getParsedBody();

        $custom_settings_file = PATH['project'] . '/config/plugins/site/settings.yaml';
        $custom_settings_file_data = $this->flextype->container('yaml')->decode(Filesystem::read($custom_settings_file));

        Arrays::set($custom_settings_file_data, 'theme', $post_data['theme-id']);

        Filesystem::write($custom_settings_file, $this->flextype->container('yaml')->encode($custom_settings_file_data));

        // clear cache
        $this->flextype->container('cache')->purge('doctrine');

        // Redirect to themes index page
        return $response->withRedirect($this->flextype->container('router')->pathFor('admin.themes.index'));
    }

    /**
     * Theme information
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response) : Response
    {
        // Get Theme ID
        $id = $request->getQueryParams()['id'];

        // Set theme custom manifest content
        $custom_theme_manifest_file = PATH['project'] . '/themes/' . $id . '/theme.yaml';

        // Get theme custom manifest content
        $custom_theme_manifest_file_content = Filesystem::read($custom_theme_manifest_file);

        return $this->flextype->container('twig')->render(
            $response,
            'plugins/themes-admin/templates/extends/themes/information.html',
            [
                'menu_item' => 'themes',
                'id' => $id,
                'theme_manifest' => $this->flextype->container('yaml')->decode($custom_theme_manifest_file_content),
                'links' =>  [
                    'themes' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.themes.index'),
                        'title' => __('themes_admin_themes'),
                    ],
                    'themes_information' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.themes.information') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_information'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    /**
     * Theme settings
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settings(Request $request, Response $response) : Response
    {
        // Get Theme ID
        $id = $request->getQueryParams()['id'];

        // Set theme config
        $custom_theme_settings_file = PATH['project'] . '/config/themes/' . $id . '/settings.yaml';

        // Get theme settings content
        $custom_theme_settings_file_content = Filesystem::read($custom_theme_settings_file);

        return $this->flextype->container('twig')->render(
            $response,
            'plugins/themes-admin/templates/extends/themes/settings.html',
            [
                'menu_item' => 'themes',
                'id' => $id,
                'theme_settings' => $custom_theme_settings_file_content,
                'links' =>  [
                    'themes' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.themes.index'),
                        'title' => __('themes_admin_themes'),
                    ],
                    'themes_settings' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.themes.settings') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_settings'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'save_theme_settings' => [
                        'link' => 'javascript:;',
                        'title' => __('admin_save'),
                        'type' => 'action',
                    ],
                ],
            ]
        );
    }

    /**
     * Theme settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settingsProcess(Request $request, Response $response) : Response
    {
        $post_data = $request->getParsedBody();

        $id   = $post_data['id'];
        $data = $post_data['data'];

        $custom_theme_settings_file = PATH['project'] . '/config/themes/' . $id . '/settings.yaml';

        if (Filesystem::write($custom_theme_settings_file, $data)) {
            $this->flextype->container('flash')->addMessage('success', __('themes_admin_message_theme_settings_saved'));
        } else {
            $this->flextype->container('flash')->addMessage('error', __('themes_admin_message_theme_settings_not_saved'));
        }

        return $response->withRedirect($this->flextype->container('router')->pathFor('admin.themes.settings') . '?id=' . $id);
    }
}
