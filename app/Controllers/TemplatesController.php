<?php

declare(strict_types=1);

namespace Flextype\Plugin\ThemesAdmin\Controllers;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function date;
use function Flextype\Component\I18n\__;

class TemplatesController
{
    /**
     * __construct
     */
     public function __construct()
     {

     }

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        // Get theme from request query params
        $theme = $request->getQueryParams()['theme'];

        return flextype('twig')->render(
            $response,
            'plugins/themes-admin/templates/extends/themes/templates/index.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'templates_list' => flextype('themes')->getTemplates($theme),
                'partials_list' => flextype('themes')->getPartials($theme),
                'links' =>  [
                    'themes' => [
                        'link' => flextype('router')->pathFor('admin.themes.index'),
                        'title' => __('themes_admin_themes'),

                    ],
                    'templates' => [
                        'link' => flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('themes_admin_templates'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'templates_create' => [
                        'link' => flextype('router')->pathFor('admin.templates.add') . '?theme=' . $theme,
                        'title' => __('themes_admin_create_new_template'),

                    ],
                ],
            ]
        );
    }

    /**
     * Add template
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function add(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        // Get theme from request query params
        $theme = $request->getQueryParams()['theme'];

        return flextype('twig')->render(
            $response,
            'plugins/themes-admin/templates/extends/themes/templates/add.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'links' =>  [
                    'themes' => [
                        'link' => flextype('router')->pathFor('admin.themes.index'),
                        'title' => __('themes_admin_themes'),

                    ],
                    'templates' => [
                        'link' => flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('themes_admin_templates'),

                    ],
                    'templates_add' => [
                        'link' => flextype('router')->pathFor('admin.templates.add') . '?theme=' . $theme,
                        'title' => __('themes_admin_create_new_template'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    /**
     * Add template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        // Get data from POST
        $post_data = $request->getParsedBody();

        $id    = $post_data['id'];
        $type  = $post_data['type'];
        $theme = $post_data['theme'];

        $file = PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . flextype('slugify')->slugify($id) . '.html';

        if (! Filesystem::has($file)) {
            if (Filesystem::write(
                $file,
                ''
            )) {
                flextype('flash')->addMessage('success', __('themes_admin_message_' . $type . '_created'));
            } else {
                flextype('flash')->addMessage('error', __('themes_admin_message_' . $type . '_was_not_created'));
            }
        } else {
            flextype('flash')->addMessage('error', __('themes_admin_message_' . $type . '_was_not_created'));
        }

        if (isset($post_data['create-and-edit'])) {
            return $response->withRedirect(flextype('router')->pathFor('admin.templates.edit') . '?theme=' . $theme . '&type=' . $type . '&id=' . $id);
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    /**
     * Edit template
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function edit(Request $request, Response $response) : Response
    {
        // Get type and theme from request query params
        $type  = $request->getQueryParams()['type'];
        $theme = $request->getQueryParams()['theme'];

        return flextype('twig')->render(
            $response,
            'plugins/themes-admin/templates/extends/themes/templates/edit.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'id' => $request->getQueryParams()['id'],
                'data' => Filesystem::read(PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . $request->getQueryParams()['id'] . '.html'),
                'type' => ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template'),
                'links' => [
                    'themes' => [
                        'link' => flextype('router')->pathFor('admin.themes.index'),
                        'title' => __('themes_admin_themes'),

                    ],
                    'templates' => [
                        'link' => flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('themes_admin_templates'),

                    ],
                    'templates_editor' => [
                        'link' => flextype('router')->pathFor('admin.templates.edit') . '?id=' . $request->getQueryParams()['id'] . '&type=' . ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template') . '&theme=' . $theme,
                        'title' => __('admin_editor'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'save_template' => [
                        'link'       => 'javascript:;',
                        'title'      => __('admin_save'),
                        'type' => 'action',
                    ],
                ],
            ]
        );
    }

    /**
     * Edit template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function editProcess(Request $request, Response $response) : Response
    {
        // Get theme and type and id from request query params
        $theme = $request->getParsedBody()['theme'];
        $id    = $request->getParsedBody()['id'];
        $type  = $request->getParsedBody()['type'];

        if (Filesystem::write(PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html', $request->getParsedBody()['data'])) {
            flextype('flash')->addMessage('success', __('themes_admin_message_' . $type . '_saved'));
        } else {
            flextype('flash')->addMessage('error', __('themes_admin_message_' . $type . '_was_not_saved'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.templates.edit') . '?id=' . $id . '&type=' . $type . '&theme=' . $theme);
    }

    /**
     * Rename template
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function rename(Request $request, Response $response) : Response
    {
        // Get theme from request query params
        $theme = $request->getQueryParams()['theme'];

        return flextype('twig')->render(
            $response,
            'plugins/themes-admin/templates/extends/themes/templates/rename.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'types' => ['partial' => __('admin_partial'), 'template' => __('admin_template')],
                'id_current' => $request->getQueryParams()['id'],
                'type_current' => ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template'),
                'links' => [
                    'themes' => [
                        'link' => flextype('router')->pathFor('admin.themes.index'),
                        'title' => __('themes_admin_themes'),

                    ],
                    'templates' => [
                        'link' => flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('themes_admin_templates'),

                    ],
                    'templates_rename' => [
                        'link' => flextype('router')->pathFor('admin.templates.rename') . '?id=' . $request->getQueryParams()['id'] . '&type=' . ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template') . '&theme=' . $theme,
                        'title' => __('admin_rename'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    /**
     * Rename template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function renameProcess(Request $request, Response $response) : Response
    {
        // Get theme and type from request query params
        $theme = $request->getParsedBody()['theme'];
        $type  = $request->getParsedBody()['type_current'];

        if (! Filesystem::has(PATH['project'] . '/themes/' . flextype('registry')->get('plugins.site.settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html')) {
            if (Filesystem::rename(
                PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()['id_current'] . '.html',
                PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html'
            )
            ) {
                flextype('flash')->addMessage('success', __('themes_admin_message_' . $type . '_renamed'));
            } else {
                flextype('flash')->addMessage('error', __('themes_admin_message_' . $type . '_was_not_renamed'));
            }
        } else {
            flextype('flash')->addMessage('error', __('themes_admin_message_' . $type . '_was_not_renamed'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    /**
     * Delete template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        // Get theme and type from request query params
        $theme = $request->getParsedBody()['theme'];
        $type  = $request->getParsedBody()['type'];

        $file_path = PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()[$type . '-id'] . '.html';

        if (Filesystem::delete($file_path)) {
            flextype('flash')->addMessage('success', __('themes_admin_message_' . $type . '_deleted'));
        } else {
            flextype('flash')->addMessage('error', __('themes_admin_message_' . $type . '_was_not_deleted'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    /**
     * Duplicate template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function duplicateProcess(Request $request, Response $response) : Response
    {
        // Get theme and type from request query params
        $theme = $request->getParsedBody()['theme'];
        $type  = $request->getParsedBody()['type'];

        $file_path     = PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()[$type . '-id'] . '.html';
        $file_path_new = PATH['project'] . '/themes/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()[$type . '-id'] . '-duplicate-' . date('Ymd_His') . '.html';

        if (Filesystem::copy($file_path, $file_path_new)) {
            flextype('flash')->addMessage('success', __('themes_admin_message_' . $type . '_duplicated'));
        } else {
            flextype('flash')->addMessage('error', __('themes_admin_message_' . $type . '_was_not_duplicated'));
        }

        return $response->withRedirect(flextype('router')->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    private function _type_location($type)
    {
        if ($type === 'partial') {
            $_type = '/templates/partials/';
        } else {
            $_type = '/templates/';
        }

        return $_type;
    }
}
