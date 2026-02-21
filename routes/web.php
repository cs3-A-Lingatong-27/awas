    Route::get('/user/password', [PasswordController::class, 'edit'])->name('user-password.edit');
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');