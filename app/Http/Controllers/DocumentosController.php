<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investigador;
use App\Models\Observacion;
use Exception;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class DocumentosController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    function mostrar($idInvestigador, $tabla, $idDocumento) {
        $investigador = Investigador::findOrFail($idInvestigador);
        $doc = null;

        switch ($tabla) {
            case 'ga':
                $doc = $investigador->docs_grados_academicos->find($idDocumento);
            break;
            case 'par':
                $doc = $investigador->docs_participacion_cyts->find($idDocumento);
            break;
            case 'des':
                $doc = $investigador->docs_desempeno_cyts->find($idDocumento);
            break;
            case 'pub':
                $doc = $investigador->docs_publicaciones_cyts->find($idDocumento);
            break;
        }

        if ($doc === null) {
            return abort(404);
        }

        return view('gestion.revision-doc', compact('doc', 'investigador', 'tabla'));
    }

    function agregarObservacion(Request $request) {
        $datos = $request->validate([
            'observacion' => 'required|string',
            'tabla' => 'required|string',
            'id_documento' => 'required',
            'id_investigador' => 'required'
        ]);

        Observacion::create($datos);
        Alert::success('Información', 'La observación sobre este documento ha sido almacenada exitosamente');
        return back();
    }

    function eliminarObservacion(Request $request) {
        try {
            DB::beginTransaction();
            Observacion::destroy($request->id_observacion);
            DB::commit();
            return response()->json(['message' => 'La observacion ha sido eliminado exitosamente']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Ha ocurrido un error al eliminar la observación'], 500);
        }   
    }
}
