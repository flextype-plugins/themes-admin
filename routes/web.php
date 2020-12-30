<?php

declare(strict_types=1);

use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;

flextype()->group('/' . $admin_route, function () : void {

    // ThemesAdminController
    flextype()->get('/themes', 'ThemesAdminController:index')->setName('admin.themes.index');
    flextype()->get('/themes/information', 'ThemesAdminController:information')->setName('admin.themes.information');
    flextype()->get('/themes/settings', 'ThemesAdminController:settings')->setName('admin.themes.settings');
    flextype()->post('/themes/settings', 'ThemesAdminController:settingsProcess')->setName('admin.themes.settingsProcess');
    flextype()->post('/themes/activateProcess', 'ThemesAdminController:activateProcess')->setName('admin.themes.activateProcess');

    // ThemesAdminTemplatesController
    flextype()->get('/templates', 'ThemesAdminTemplatesController:index')->setName('admin.themes.templates.index');
    flextype()->get('/templates/add', 'ThemesAdminTemplatesController:add')->setName('admin.themes.templates.add');
    flextype()->post('/templates/add', 'ThemesAdminTemplatesController:addProcess')->setName('admin.themes.templates.addProcess');
    flextype()->get('/templates/edit', 'ThemesAdminTemplatesController:edit')->setName('admin.themes.templates.edit');
    flextype()->post('/templates/edit', 'ThemesAdminTemplatesController:editProcess')->setName('admin.themes.templates.addProcess');
    flextype()->get('/templates/rename', 'ThemesAdminTemplatesController:rename')->setName('admin.themes.templates.rename');
    flextype()->post('/templates/rename', 'ThemesAdminTemplatesController:renameProcess')->setName('admin.themes.templates.renameProcess');
    flextype()->post('/templates/duplicate', 'ThemesAdminTemplatesController:duplicateProcess')->setName('admin.themes.templates.duplicateProcess');
    flextype()->post('/templates/delete', 'ThemesAdminTemplatesController:deleteProcess')->setName('admin.themes.templates.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (flextype('acl')->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                      'roles' => 'admin']))
  ->add('csrf');
