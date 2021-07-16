<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner; //eloquent
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // QueryBuilder

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class OwnersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /**
         * CarbonはPHPの日付操作ライブラリでLAravelには標準で搭載されている。
         * Elquentモデルでは標準でCarbonインスタンスとして日付データが変えるが、
         * QueryBuilderではインスタンスになっていないので注意！
         */
        // $date_now = Carbon::now();
        // $date_parce = Carbon::parse(now());

        // $e_all = Owner::all();
        // $q_get = DB::table('owners')->select('name', 'created_at')->get();
        // $d_first = DB::table('owners')->select('name')->first();
        // $c_test = collect([
        //     'name' => 'テスト'
        // ]);
        // dd( $e_all, $d_get, $d_first, $c_test );
       
        // return view('admin.owners.index', compact('e_all', 'q_get'));

        // 必要な情報だけをselecetする。
        $owners = Owner::select('id', 'name', 'email', 'created_at')->get();
        return view('admin.owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // テーブル名変更！
            'email' => 'required|string|email|max:255|unique:owners',
            'password' => ['required', 'confirmed'],
        ]);

        $owner = Owner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナー登録を実施しました。',
            'status' => 'info'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // なかったら404画面に飛ぶ
        $owner = Owner::findOrFail($id);
        // dd($owner);

        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->password = Hash::make($request->password);
        $owner->save();

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナーの情報を更新しました。',
            'status' => 'info'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 設定したソフトデリートが適用される。。
        Owner::findOrFail($id)->delete();

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナーの情報を削除しました。',
            'status' => 'alert',
        ]);
    }


    /**
     * ゴミ箱の取得
     *
     * @return void
     */
    public function expiredOwnerIndex()
    {
        $expiredOwners = Owner::onlyTrashed()->get();

        return view('admin.expired-owners',compact('expiredOwners'));
    }


    /**
     * ゴミ箱の削除
     *
     * @param [type] $id
     * @return void
     */
    public function expiredOwnerDestroy($id)
    {
        Owner::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();
            
        return redirect()
            ->route('admin.expired-owners.index')
            ->with([
                'message' => 'オーナーの情報を完全に削除しました。',
                'status' => 'info',
            ]);
    }


}
