<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nbm = Auth::guard('karyawan')->user()->nbm;
        $cek = DB::table('tbl_presensi')->where('tgl_presensi', $hariini)->where('nbm', $nbm)->count();
        return view('presesnsi.create', [
            'cek' => $cek
        ]); 
    }

    public function store(Request $request)
    {
        $nbm = Auth::guard('karyawan')->user()->nbm;
        $tgl_presensi = date('Y-m-d');
        $jam = date('H:i:s');
        $latitudekantor =  -7.3650198466841506;
        $longitudekantor = 109.89890821666044;
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];
        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak['meters']);
        dd($radius);
        $image = $request->image;
        $forlderPath = "public/uploads/absensi/";
        $formatName = $nbm."_".$tgl_presensi;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file =$forlderPath . $fileName;
        $data = [
            'nbm' =>$nbm,
            'tgl_presensi' => $tgl_presensi,
            'jam_in' => $jam,
            'foto_in'=> $fileName,
            'lokasi_in' => $lokasi
        ];
        $cek = DB::table('tbl_presensi')->where('tgl_presensi', $tgl_presensi)->where('nbm', $nbm)->count();
        if($radius > 10){
            echo "error|Maaf Anda Berada Diluar Radius!|";
        }else{
            if($cek > 0){
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out'=> $fileName,
                    'lokasi_out' => $lokasi
                ];
                $update = DB::table('tbl_presensi')->where('tgl_presensi', $tgl_presensi)->where('nbm', $nbm)->update($data_pulang);
                if ($update){
                    echo "success|Terima Kasih, Hati-Hati di Jalan|out";
                    Storage::put($file, $image_base64);
                }else{
                    echo "error|Maaf Gagal Absen, Hubungi Tim IT|out";
                }
            }else{
                $data = [
                    'nbm' =>$nbm,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in'=> $fileName,
                    'lokasi_in' => $lokasi
                ];
                $simpan = DB::table('tbl_presensi')->insert($data);
                    if ($simpan){
                        echo "success|Terima Kasih, Selamat Bekerja|in";
                        Storage::put($file, $image_base64);
                    }else{
                        echo "error|Maaf Gagal Absen, Hubungi Tim IT|in";
                }
            }
        }
    }

    //menghitung jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = deg2rad($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
