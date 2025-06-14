<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
/*===========================
=           stories           =
=============================*/

Route::apiResource('/stories', \App\Http\Controllers\API\StoryController::class);

/*=====  End of stories   ======*/

/*===========================
=           articles           =
=============================*/

Route::apiResource('/articles', \App\Http\Controllers\API\ArticleController::class);

/*=====  End of articles   ======*/
