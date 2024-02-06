Route::post('/addexpenses',[expensController::class,'AddExpenses']);
Route::post('/getexpenses',[expensController::class,'getAllExpenes']);
Route::post('/updateexpense', [expensController::class,'UpdateExpense']);
Route::post('/deleteexpense',[expensController::class,'deleteExpense']);
