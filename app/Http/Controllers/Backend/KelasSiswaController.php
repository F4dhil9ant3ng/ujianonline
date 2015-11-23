<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Mst\KelasUser;
use App\Models\Ref\Kelas;
use Illuminate\Http\Request;

class KelasSiswaController extends Controller
{

    private $base_view = 'konten.backend.siswakelas.';
    protected $kelas_user;
    protected $kelas;


    public function __construct(KelasUser $kelas_user, Kelas $kelas)
    {
        $this->kelas_user = $kelas_user;
        $this->kelas = $kelas;
        view()->share('backend_siswakelas_index', true);
        view()->share('base_view', $this->base_view);
    }


    public function index()
    {
        $kelas_user = $this->kelas_user
            ->where('mst_user_id', '=', \Auth::user()->id)
            ->with('ref_kelas')
            ->paginate(10);
        $vars = compact('kelas_user');
        return view($this->base_view.'index', $vars);
    }


    public function stop_kelas(Request $request)
    {
        $ku = $this->kelas_user->findOrFail($request->id);
        $ku->delete();
        return 'ok';
    }


    public function ikut_kelas()
    {
        return view($this->base_view.'popup.ikut_kelas');
    }

    public function do_ikut_kelas(Request $request)
    {
        $k = $this->kelas
                  ->where('kode_kelas', '=', $request->kode_kelas)
                  ->where('is_open', '=', 1)
                  ->first();
        if(count($k)>0){
            $ku = $this->kelas_user
                       ->where('mst_user_id', '=', \Auth::user()->id)
                       ->first();
                if(count($ku)>0){
                    return 2;                    
                }

            $data_kelas_user = ['mst_user_id' => \Auth::user()->id, 'ref_kelas_id' => $k->id];
            $this->kelas_user->create($data_kelas_user);
            return 1;
        }else{
            return 0;//kode kelas tdk ditemukan
        }
    }




 
}
