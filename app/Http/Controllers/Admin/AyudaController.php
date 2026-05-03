<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ayuda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AyudaController extends Controller
{
    public function index()
    {
        $ayudas  = Ayuda::orderBy('orden')->orderBy('modulo')->get();
        $modulos = Ayuda::modulos();
        return view('ayuda.index', compact('ayudas', 'modulos'));
    }

    public function create()
    {
        $modulos = Ayuda::modulos();
        return view('ayuda.create', compact('modulos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'modulo'            => 'required|string',
            'plataforma'        => 'required|in:web,movil,ambas',
            'titulo'            => 'required|string|max:255',
            'descripcion_corta' => 'nullable|string|max:500',
            'contenido'         => 'nullable|string',
            'activo'            => 'boolean',
            'orden'             => 'integer|min:0',
        ]);

        $data['pasos']    = $this->procesarPasos($request);
        $data['imagenes'] = $this->procesarImagenes($request, null);
        $data['activo']   = $request->boolean('activo', true);

        Ayuda::create($data);

        return redirect()->route('ayuda.index')
            ->with('success', 'Guía creada correctamente.');
    }

    public function edit(Ayuda $ayuda)
    {
        $modulos = Ayuda::modulos();
        return view('ayuda.edit', compact('ayuda', 'modulos'));
    }

    public function update(Request $request, Ayuda $ayuda)
    {
        $data = $request->validate([
            'modulo'            => 'required|string',
            'plataforma'        => 'required|in:web,movil,ambas',
            'titulo'            => 'required|string|max:255',
            'descripcion_corta' => 'nullable|string|max:500',
            'contenido'         => 'nullable|string',
            'activo'            => 'boolean',
            'orden'             => 'integer|min:0',
        ]);

        $data['pasos']    = $this->procesarPasos($request);
        $data['imagenes'] = $this->procesarImagenes($request, $ayuda);
        $data['activo']   = $request->boolean('activo', true);

        $ayuda->update($data);

        return redirect()->route('ayuda.index')
            ->with('success', 'Guía actualizada correctamente.');
    }

    public function destroy(Ayuda $ayuda)
    {
        // Eliminar imágenes subidas
        foreach ($ayuda->imagenes ?? [] as $img) {
            if (($img['tipo'] ?? '') === 'upload' && !empty($img['path'])) {
                Storage::disk('public')->delete($img['path']);
            }
        }
        // Eliminar imágenes de pasos
        foreach ($ayuda->pasos ?? [] as $paso) {
            if (($paso['imagen_tipo'] ?? '') === 'upload' && !empty($paso['imagen_path'])) {
                Storage::disk('public')->delete($paso['imagen_path']);
            }
        }

        $ayuda->delete();
        return redirect()->route('ayuda.index')->with('success', 'Guía eliminada.');
    }

    public function publico()
    {
        $web     = Ayuda::paraPlatforma('web');
        $movil   = Ayuda::paraPlatforma('movil');
        $modulos = Ayuda::modulos();
        return view('ayuda.publico', compact('web', 'movil', 'modulos'));
    }

    public function modulo(string $modulo, string $plataforma = 'web')
    {
        $ayuda = Ayuda::where('modulo', $modulo)
            ->whereIn('plataforma', [$plataforma, 'ambas'])
            ->where('activo', true)
            ->first();

        if (!$ayuda) {
            return redirect()->route('ayuda.publico')
                ->with('info', 'Aún no hay guía para este módulo.');
        }

        $modulos = Ayuda::modulos();
        return view('ayuda.modulo', compact('ayuda', 'modulos', 'plataforma'));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function procesarPasos(Request $request): array
    {
        $titulos       = $request->input('paso_titulo', []);
        $descripciones = $request->input('paso_descripcion', []);
        $iconos        = $request->input('paso_icono', []);
        $imgUrls       = $request->input('paso_imagen_url', []);
        $imgArchivos   = $request->file('paso_imagen_archivo', []);

        $pasos = [];
        foreach ($titulos as $i => $titulo) {
            if (empty(trim($titulo))) continue;

            $paso = [
                'orden'       => $i + 1,
                'titulo'      => trim($titulo),
                'descripcion' => trim($descripciones[$i] ?? ''),
                'icono'       => trim($iconos[$i] ?? ''),
                'imagen_url'  => null,
                'imagen_path' => null,
                'imagen_tipo' => null,
            ];

            // Imagen por URL
            if (!empty($imgUrls[$i])) {
                $paso['imagen_url']  = trim($imgUrls[$i]);
                $paso['imagen_tipo'] = 'url';
            }

            // Imagen subida (tiene prioridad sobre URL)
            if (!empty($imgArchivos[$i])) {
                $path = $imgArchivos[$i]->store('ayuda/pasos', 'public');
                $paso['imagen_path'] = $path;
                $paso['imagen_url']  = Storage::disk('public')->url($path);
                $paso['imagen_tipo'] = 'upload';
            }

            $pasos[] = $paso;
        }
        return $pasos;
    }

    private function procesarImagenes(Request $request, ?Ayuda $ayuda): array
    {
        $imagenesActuales = $ayuda?->imagenes ?? [];

        // Imágenes a eliminar
        $eliminar = $request->input('eliminar_imagen', []);
        $imagenesActuales = array_values(array_filter(
            $imagenesActuales,
            fn($img, $i) => !in_array((string)$i, $eliminar),
            ARRAY_FILTER_USE_BOTH
        ));
        foreach ($eliminar as $idx) {
            $img = ($ayuda?->imagenes ?? [])[$idx] ?? null;
            if ($img && ($img['tipo'] ?? '') === 'upload' && !empty($img['path'])) {
                Storage::disk('public')->delete($img['path']);
            }
        }

        // Nuevas por URL
        $nuevasUrls = array_filter($request->input('imagen_url_nueva', []), fn($u) => !empty(trim($u)));
        foreach ($nuevasUrls as $url) {
            $imagenesActuales[] = [
                'url'     => trim($url),
                'path'    => null,
                'caption' => '',
                'tipo'    => 'url',
            ];
        }

        // Nuevas por archivo
        foreach ($request->file('imagen_archivo_nueva', []) as $archivo) {
            if (!$archivo) continue;
            $path = $archivo->store('ayuda/galeria', 'public');
            $imagenesActuales[] = [
                'url'     => Storage::disk('public')->url($path),
                'path'    => $path,
                'caption' => '',
                'tipo'    => 'upload',
            ];
        }

        return array_values($imagenesActuales);
    }
}