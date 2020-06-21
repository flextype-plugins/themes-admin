<?php

declare(strict_types=1);

namespace Flextype;

$app->group('/' . $admin_route, function () use ($app, $flextype) : void {

    // ThemesController
    $app->get('/themes', 'ThemesController:index')->setName('admin.themes.index');
    $app->get('/themes/information', 'ThemesController:information')->setName('admin.themes.information');
    $app->get('/themes/settings', 'ThemesController:settings')->setName('admin.themes.settings');
    $app->post('/themes/settings', 'ThemesController:settingsProcess')->setName('admin.themes.settingsProcess');
    $app->post('/themes/activateProcess', 'ThemesController:activateProcess')->setName('admin.themes.activateProcess');

    // TemplatesController
    $app->get('/templates', 'TemplatesController:index')->setName('admin.templates.index');
    $app->get('/templates/add', 'TemplatesController:add')->setName('admin.templates.add');
    $app->post('/templates/add', 'TemplatesController:addProcess')->setName('admin.templates.addProcess');
    $app->get('/templates/edit', 'TemplatesController:edit')->setName('admin.templates.edit');
    $app->post('/templates/edit', 'TemplatesController:editProcess')->setName('admin.templates.addProcess');
    $app->get('/templates/rename', 'TemplatesController:rename')->setName('admin.templates.rename');
    $app->post('/templates/rename', 'TemplatesController:renameProcess')->setName('admin.templates.renameProcess');
    $app->post('/templates/duplicate', 'TemplatesController:duplicateProcess')->setName('admin.templates.duplicateProcess');
    $app->post('/templates/delete', 'TemplatesController:deleteProcess')->setName('admin.templates.deleteProcess');

})->add(new AclAccountIsUserLoggedInMiddleware(['container' => $flextype, 'redirect' => 'admin.accounts.login']))
  ->add(new AclAccountsIsUserLoggedInRolesOneOfMiddleware(['container' => $flextype,
                                                           'redirect' => ($flextype->acl->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                           'roles' => 'admin']))
  ->add('csrf');
