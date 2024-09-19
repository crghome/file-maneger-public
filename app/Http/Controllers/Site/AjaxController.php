<?php

namespace App\Http\Controllers\Api\Pixelate;

use App\Helpers\Pixelate;
use App\Http\Controllers\Controller;
use App\Models\Catalog\CatalogPixelatePage;
use App\Models\Catalog\CatalogProduct;
use App\Services\Catalog\Helper\ColorPaletteService;
use App\Services\Catalog\Helper\ColorService;
use App\Services\Catalog\Pixelate\ConfigService;
use App\Services\Catalog\Pixelate\PageService;
use App\Services\Catalog\ProductService;
use App\Services\CRest24Service;
use App\Services\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AjaxController extends Controller
{
}
