<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiecController;
use App\Http\Controllers\Admin\ItemTypeController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Admin\PrintingContentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProfitReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\SalesOrderController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TtsController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Userend\WelcomeController;
use Illuminate\Support\Facades\Route;



// that route for storage link using on cpanel
// Route::get('/storage-link', function () {
//     $targeted_folder = storage_path('app/public');
//     $linkUpFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
//     symlink($targeted_folder, $linkUpFolder);
// });

Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome');

// auth routes
Route::middleware('guest')->group(function () {
    Route::get('/sign-in', [SignInController::class, 'index'])->name('sign.in');
    Route::post('/sign-in', [SignInController::class, 'req_sign_in'])->name('sign.in.request')->middleware('account.active.or.deactive');
    Route::middleware('authenticate.off.or.on')->group(function () {
        Route::get('/sign-up', [SignUpController::class, 'index'])->name('sign.up');
        Route::post('/sign-up', [SignUpController::class, 'store'])->name('sign.up.store');
    });
    Route::get('/forget-password', [ForgetPasswordController::class, 'index'])->name('forget.password');
    Route::post('/forget-password', [ForgetPasswordController::class, 'send_mail'])->name('forget.password.send');
    Route::get('/reset-password', [ResetPasswordController::class, 'index'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset_password'])->name('password.reset.request');
});


Route::post('/sign-out', [SignInController::class, 'sign_out'])->name('sign.out')->middleware('authenticate');
// admin prefix
// Route::get(uri: '/products/get-for-sales', [ProductController::class, 'get_for_sales_order']);
Route::middleware(['authenticate', 'is_access_admin_panel'])->group(function () {
    Route::prefix('admin')->group(function () {
        // common routes
        Route::get('/low-stocks', [CommonController::class, 'low_stocks']);
        Route::get('/send-email-for-low-stocks', [CommonController::class, 'send_email_for_low_stocks']);
        Route::get('/low-stocks-products', [CommonController::class, 'low_stocks_products'])->name('admin.low.stock.products');
        // Route::get('/tts', [TtsController::class, 'generate'])->name('text.to.voice');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
        Route::post('/profile', [ProfileController::class, 'store_or_update'])->name('admin.profile.store_or_update');

        // staff routes
        Route::middleware('staff.and.user.management.access')->group(function () {
            Route::get('/user/get', [StaffController::class, 'get'])->name('admin.user.get');
            Route::get('/user', [StaffController::class, 'index'])->name('admin.user');
            Route::get('/user/show/{id}', [StaffController::class, 'show'])->name('admin.user.show');
            Route::get('/user/create', [StaffController::class, 'create'])->name('admin.user.create');
            Route::post('/user/store', [StaffController::class, 'store'])->name('admin.user.store');
            Route::get('/user/edit/{id}', [StaffController::class, 'edit'])->name('admin.user.edit');
            Route::post('/user/update/{id}', [StaffController::class, 'update'])->name('admin.user.update');
            Route::get('/user/delete/{id}', [StaffController::class, 'destroy'])->name('admin.user.destroy');
        });

        // customer routes
        Route::get('/customers/get', [CustomerController::class, 'get']);
        Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers');
        Route::get('/customer/create', [CustomerController::class, 'create'])->name('admin.customer.create');
        Route::post('/customer/store', [CustomerController::class, 'store'])->name('admin.customer.store');
        Route::get('/customer/edit/{id}', [CustomerController::class, 'edit']);
        Route::post('/customer/update/{id}', [CustomerController::class, 'update'])->name('admin.customer.update');
        Route::get('/customer/delete/{id}', [CustomerController::class, 'destroy']);

        // item type routes
        Route::get('/item-type/get', [ItemTypeController::class, 'get']);
        Route::get('/item-types', [ItemTypeController::class, 'index'])->name('admin.item.types');
        Route::get('/item-type/create', [ItemTypeController::class, 'create'])->name('admin.item.type.create');
        Route::post('/item-type/store', [ItemTypeController::class, 'store'])->name('admin.item.type.store');
        Route::get('/item-type/edit/{id}', [ItemTypeController::class, 'edit'])->name('admin.item.type.edit');
        Route::post('/item-type/update/{id}', [ItemTypeController::class, 'update'])->name('admin.item.type.update');
        Route::get('/item-type/delete/{id}', [ItemTypeController::class, 'destroy']);

        // brand routes
        Route::get('/brands/get', [BrandController::class, 'get']);
        Route::get('/brands', [BrandController::class, 'index'])->name('admin.brands');
        Route::get('/brand/create', [BrandController::class, 'create'])->name('admin.brand.create');
        Route::post('/brand/store', [BrandController::class, 'store'])->name('admin.brand.store');
        Route::get('/brand/edit/{id}', [BrandController::class, 'edit'])->name('admin.brand.edit');
        Route::post('/brand/update/{id}', [BrandController::class, 'update'])->name('admin.brand.update');
        Route::get('/brand/delete/{id}', [BrandController::class, 'destroy']);

        // category routes
        Route::get('/categories/get', [CategoryController::class, 'get']);
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
        Route::get('/category/create', [CategoryController::class, 'create'])->name('admin.category.create');
        Route::post('/category/store', [CategoryController::class, 'store'])->name('admin.category.store');
        Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('admin.category.edit');
        Route::post('/category/update/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
        Route::get('/category/delete/{id}', [CategoryController::class, 'destroy']);

        // sub category routes
        Route::get('/sub-categories/get', [SubCategoryController::class, 'get']);
        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('admin.sub.categories');
        Route::get('/sub-category/create', [SubCategoryController::class, 'create'])->name('admin.sub.category.create');
        Route::post('/sub-category/store', [SubCategoryController::class, 'store'])->name('admin.sub.category.store');
        Route::get('/sub-category/edit/{id}', [SubCategoryController::class, 'edit'])->name('admin.sub.category.edit');
        Route::post('/sub-category/update/{id}', [SubCategoryController::class, 'update'])->name('admin.sub.category.update');
        Route::get('/sub-category/delete/{id}', [SubCategoryController::class, 'destroy']);

        // product routes
        Route::get('/products/get', [ProductController::class, 'get']);
        Route::get('/products/get-for-sales', [ProductController::class, 'get_for_sales_order']);
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
        Route::get('/product/create', [ProductController::class, 'create'])->name('admin.product.create');
        Route::get('/product/show/{id}', [ProductController::class, 'show'])->name('admin.product.show');
        Route::post('/product/store', [ProductController::class, 'store'])->name('admin.product.store');
        Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('admin.product.edit');
        Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('admin.product.update');
        Route::get('/product/delete/{id}', [ProductController::class, 'destroy']);
        Route::post('/product/show/on/delete/{id}', [ProductController::class, 'delete'])->name('admin.product.show.on.delete');

        // sales order routes
        Route::get('/sales/orders/get', [SalesOrderController::class, 'get']);
        Route::get('/products/for-order/get', [SalesOrderController::class, 'get_products']);
        Route::get('/customers/for-order/get', [SalesOrderController::class, 'get_customers']);
        Route::get('/products/for-order-billed/get', [SalesOrderController::class, 'get_products_for_card']);
        Route::post('/sales/order/add/new/customer', [SalesOrderController::class, 'add_new_customer'])->name('admin.sales.order.add.new.customer');
        Route::get('/sales/orders', [SalesOrderController::class, 'index'])->name('admin.sales.orders');
        Route::get('/sales/order/invoice/{id}', [SalesOrderController::class, 'show'])->name('admin.sales.order.invoice');
        Route::get('/sales/order/create', [SalesOrderController::class, 'create'])->name('admin.sales.order.create');
        Route::post('/sales/order/store', [SalesOrderController::class, 'store'])->name('admin.sales.order.store');
        Route::get('/sales/order/edit/{id}', [SalesOrderController::class, 'edit'])->name('admin.sales.order.edit');
        Route::post('/sales/order/update/{id}', [SalesOrderController::class, 'update'])->name('admin.sales.order.update');
        Route::get('/sales/order/delete/{id}', [SalesOrderController::class, 'destroy']);
        // Route::post('/sales/order/show/on/delete/{id}', [SalesOrderController::class, 'delete'])->name('admin.sales.order.show.on.delete');

        // invoice routes
        Route::get('/invoice/{id}/pdf', [InvoiecController::class, 'generatePDF'])->name('invoice.pdf');
        Route::get('/invoice/{id}/print', [InvoiecController::class, 'showPrint'])->name('invoice.print');
        Route::get('/invoice/{id}/excel', [InvoiecController::class, 'generateCsv'])->name('invoice.csv');

        // reports routes
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
        Route::get('/sales-reports', [SalesReportController::class, 'sales_reports'])->name('admin.sales.reports');
        Route::get('/profit-reports', [ProfitReportController::class, 'profit_reports'])->name('admin.profit.reports');

        // settings routes
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
        Route::post('/settings/auth-off-or-on/{id}', [SettingsController::class, 'auth_off_or_on'])->name('admin.auth.off.or.on')->middleware('access.admin.settings');
        Route::post('/settings/low-stock-alert-off-or-on/{id}', [SettingsController::class, 'low_stock_alert_off_or_on'])->name('admin.low.stock.alert.off.or.on');
        Route::post('/settings/delete-option/{id}', [SettingsController::class, 'delete_option'])->name('admin.settings.delete.option')->middleware('access.admin.settings');
        Route::post('/settings/low-stock-alert-msg-store/{id}', [SettingsController::class, 'low_stock_alert_msg_store'])->name('admin.low.stock.alert.msg.store')->middleware('access.admin.settings');
        Route::post('/settings/alert-times-store/{id}', [SettingsController::class, 'alert_times_store'])->name('admin.alert.times.store')->middleware('access.admin.settings');

        // sales report export excel sheel
        Route::get('/sales-reports/export', [ReportExportController::class, 'sales_export'])->name('admin.sales.reports.export');
        Route::get('/profit-reports/export', [ReportExportController::class, 'profit_export'])->name('admin.profit.reports.export');
        Route::get('/inventory-reports/export', [ReportExportController::class, 'inventory_export'])->name('admin.inventory.reports.export');

        // admin landing page routes
        Route::middleware('website.manage.access')->group(function () {
            Route::get('/landing-page', [LandingPageController::class, 'index'])->name('admin.landing.page');
            Route::post('/landing-page/store-or-update', [LandingPageController::class, 'store_or_update'])->name('admin.landing.page.store.or.update');
            Route::post('/landing-page/contact-info-store', [LandingPageController::class, 'contact_info_store'])->name('admin.landing.page.contact.info.store');
            Route::get('/landing-page/contact-info-edit/{id}', [LandingPageController::class, 'contact_info_edit']);
            Route::post('/landing-page/contact-info-update/{id}', [LandingPageController::class, 'contact_info_update'])->name('admin.landing.page.contact.info.update');
            Route::get('/landing-page/contact-info-delete/{id}', [LandingPageController::class, 'contact_info_delete'])->name('admin.landing.page.contact.info.delete');
            Route::post('/landing-page/feature-store', [LandingPageController::class, 'feature_info_store'])->name('admin.landing.page.feature.store');
            Route::get('/landing-page/feature-edit/{id}', [LandingPageController::class, 'feature_info_edit']);
            Route::post('/landing-page/feature-update/{id}', [LandingPageController::class, 'feature_info_update'])->name('admin.landing.page.feature.update');
            Route::get('/landing-page/feature-delete/{id}', [LandingPageController::class, 'feature_info_delete'])->name('admin.landing.page.feature.delete');
        });

        // printing contents
        Route::get('/printing-content', [PrintingContentController::class, 'index'])->name('admin.printing.contents');
        Route::post('/printing-content/store-or-update', [PrintingContentController::class, 'store_or_update'])->name('admin.printing.content.store.or.update');
    });
});

Route::get('/test-text-to-voice', [TestController::class, 'test'])->name('text.to.voice');

