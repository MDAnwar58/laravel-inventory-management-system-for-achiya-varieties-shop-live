<?php

use App\Http\Middleware\AccessAdminSettings;
use App\Http\Middleware\AccessAdminWebsiteManage;
use App\Http\Middleware\AccountActivateOrDeactive;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AuthenticateOffOrOn;
use App\Http\Middleware\Guest;
use App\Http\Middleware\IsAccessAdminPanel;
use App\Http\Middleware\IsSuperAdminOrOwner;
use App\Http\Middleware\StaffAndUserManagementAccess;
use App\Http\Middleware\StaffManagementAccess;
use App\Http\Middleware\UserManagementAccess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'guest' => Guest::class,
            'admin' => Admin::class,
            'is_access_admin_panel' => IsAccessAdminPanel::class,
            'is_super_admin_or_owner' => IsSuperAdminOrOwner::class,
            'access.admin.settings' => AccessAdminSettings::class,
            'authenticate.off.or.on' => AuthenticateOffOrOn::class,
            'account.active.or.deactive' => AccountActivateOrDeactive::class,
            'authenticate' => Authenticate::class,
            'staff.and.user.management.access' => StaffAndUserManagementAccess::class,
            'website.manage.access' => AccessAdminWebsiteManage::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
