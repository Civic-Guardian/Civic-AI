<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperPortalController extends Controller
{
    /**
     * Show the developer portal documentation page.
     */
    public function index()
    {
        return view('admin.dev_docs');
    }
}
