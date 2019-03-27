<?php

Route::get('/responder/test', function () {
    return json_encode(['message' => 'welcome to api responder']);
});