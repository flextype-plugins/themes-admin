<?php

declare(strict_types=1);

use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;
use Flextype\Plugin\ThemesAdmin\Controllers\ThemesAdminController;
use Flextype\Plugin\ThemesAdmin\Controllers\ThemesAdminTemplatesController;

flextype()->group('/' . $admin_route, function () : void {

    // ThemesAdminController
    flextype()->get('/themes', ThemesAdminController::class . ':index')->setName('admin.themes.index');
    flextype()->get('/themes/information', ThemesAdminController::class . ':information')->setName('admin.themes.information');
    flextype()->get('/themes/settings', ThemesAdminController::class . ':settings')->setName('admin.themes.settings');
    flextype()->post('/themes/settings', ThemesAdminController::class . ':settingsProcess')->setName('admin.themes.settingsProcess');
    flextype()->post('/themes/activateProcess', ThemesAdminController::class . ':activateProcess')->setName('admin.themes.activateProcess');

    // ThemesAdminTemplatesController
    flextype()->get('/templates',  ThemesAdminTemplatesController::class . ':index')->setName('admin.themes.templates.index');
    flextype()->get('/templates/add', ThemesAdminTemplatesController::class . ':add')->setName('admin.themes.templates.add');
    flextype()->post('/templates/add', ThemesAdminTemplatesController::class . ':addProcess')->setName('admin.themes.templates.addProcess');
    flextype()->get('/templates/edit', ThemesAdminTemplatesController::class . ':edit')->setName('admin.themes.templates.edit');
    flextype()->post('/templates/edit', ThemesAdminTemplatesController::class . ':editProcess')->setName('admin.themes.templates.addProcess');
    flextype()->get('/templates/rename', ThemesAdminTemplatesController::class . ':rename')->setName('admin.themes.templates.rename');
    flextype()->post('/templates/rename', ThemesAdminTemplatesController::class . ':renameProcess')->setName('admin.themes.templates.renameProcess');
    flextype()->post('/templates/duplicate', ThemesAdminTemplatesController::class . ':duplicateProcess')->setName('admin.themes.templates.duplicateProcess');
    flextype()->post('/templates/delete', ThemesAdminTemplatesController::class . ':deleteProcess')->setName('admin.themes.templates.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (flextype('acl')->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                'roles' => 'admin']))
  ->add('csrf');
