<?php

declare(strict_types=1);

use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;

flextype()->group('/' . $admin_route, function () : void {

    // ThemesController
    flextype()->get('/themes', 'ThemesController:index')->setName('admin.themes.index');
    flextype()->get('/themes/information', 'ThemesController:information')->setName('admin.themes.information');
    flextype()->get('/themes/settings', 'ThemesController:settings')->setName('admin.themes.settings');
    flextype()->post('/themes/settings', 'ThemesController:settingsProcess')->setName('admin.themes.settingsProcess');
    flextype()->post('/themes/activateProcess', 'ThemesController:activateProcess')->setName('admin.themes.activateProcess');

    // TemplatesController
    flextype()->get('/templates', 'TemplatesController:index')->setName('admin.templates.index');
    flextype()->get('/templates/add', 'TemplatesController:add')->setName('admin.templates.add');
    flextype()->post('/templates/add', 'TemplatesController:addProcess')->setName('admin.templates.addProcess');
    flextype()->get('/templates/edit', 'TemplatesController:edit')->setName('admin.templates.edit');
    flextype()->post('/templates/edit', 'TemplatesController:editProcess')->setName('admin.templates.addProcess');
    flextype()->get('/templates/rename', 'TemplatesController:rename')->setName('admin.templates.rename');
    flextype()->post('/templates/rename', 'TemplatesController:renameProcess')->setName('admin.templates.renameProcess');
    flextype()->post('/templates/duplicate', 'TemplatesController:duplicateProcess')->setName('admin.templates.duplicateProcess');
    flextype()->post('/templates/delete', 'TemplatesController:deleteProcess')->setName('admin.templates.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (flextype('acl')->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                      'roles' => 'admin']))
  ->add('csrf');
