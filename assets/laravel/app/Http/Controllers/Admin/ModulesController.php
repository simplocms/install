<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module\Entity;
use App\Models\Module\InstalledModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Module;
use Nwidart\Modules\Exceptions\ModuleNotFoundException;

class ModulesController extends AdminController
{

    /**
     * ModulesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:modules-show')->only(['index']);
        $this->middleware('permission:modules-toggle')->only(['toggleEnabled']);
        $this->middleware('permission:modules-install')->only(['install', 'uninstall']);
    }


    /**
     * Request: manage installed modules
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription(
            trans('admin/modules.header_title'), trans('admin/modules.descriptions.index')
        );

        $modules = Module::all();
        $installedModules = InstalledModule::all();

        foreach ($installedModules as $installedModule) {
            if (isset($modules[$installedModule->name])) {
                $modules[$installedModule->name]->installation = $installedModule;
            }
        }

        return view('admin.modules.index', compact('modules'));
    }


    /**
     * Toggle enabled specified module
     *
     * @param InstalledModule $module
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleEnabled(InstalledModule $module)
    {
        $module->update(
            [
                'enabled' => !$module->enabled
            ]
        );

        $text = $module->enabled ? 'admin/modules.notifications.enabled' : 'admin/modules.notifications.disabled';
        flash(trans($text, ['name' => $module->name]), 'success');
        return $this->refresh();
    }


    /**
     * Install module
     *
     * @param string $name
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function install($name)
    {
        /** @var \App\Models\Module\Module $module */
        $module = Module::find($name);

        if (!$module) {
            return response()->json([
                'error' => 'Modul neexistuje.'
            ]);
        }

        $exitCode = $module->install();

        if ($exitCode !== 0) {
            return response()->json([
                'error' => 'Migrace se nezdařila! ExitCode: ' . $exitCode
            ]);
        }

        return $this->refresh();
    }


    /**
     * Uninstall module
     *
     * @param InstalledModule $module
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function uninstall(InstalledModule $module)
    {
        $module->module->uninstall();
        return $this->refresh();
    }


    /**
     * Request: Show the form for editing the module entity.
     *
     * @param Entity $entity
     *
     * @return \Illuminate\View\View
     */
    public function edit(Entity $entity)
    {
        return $this->castRequest($entity);
    }


    /**
     * Request: Update the specified module entity.
     *
     * @param  Entity $entity
     *
     * @return \Illuminate\Http\Response
     */
    public function validateAndPreview(Request $request, Entity $entity)
    {
        // if entity doesnt exists yet, get module name and setup entity
        if (!$entity->exists) {
            $entity->module = $request->input('entity_module_name');
            $module = null;

            // Check if module exists and is designed for pages
            try {
                $module = $entity->getModule();
            } catch (ModuleNotFoundException $e) {
                abort(404);
            }

            if (!$module->isForGridEditor()) {
                abort(404);
            }
        }

        return $this->castRequest($entity);
    }


    /**
     * Load previews of specified modules.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function loadPreviews(Request $request)
    {
        if (!$idsInput = $request->input('ids')) {
            return [];
        }

        $ids = json_decode($idsInput);
        if (!$ids) {
            return [];
        }

        $modules = [];
        $entities = Entity::whereIn('id', $ids)->get();

        foreach ($entities as $entity) {
            $modules[] = [
                'id' => $entity->id,
                'name' => $entity->getModule()->getName(),
                'title' => $entity->getModule()->getGridEditorTitle(),
                'icon' => $entity->getModule()->getIcon(),
                'content' => $entity->renderPreview()
            ];
        }

        return response()->json(
            [
                'modules' => $modules
            ]
        );
    }


    /**
     * Cast request to controller for specified entity.
     *
     * @param Entity $entity
     *
     * @return mixed
     */
    private function castRequest(Entity $entity)
    {
        $request = request();
        list($class, $action) = explode('@', $request->route()->getActionName());

        $controller = "Modules\\{$entity->module}\\Http\\Controllers\\ModuleController";
        $reflection = new \ReflectionClass($controller);
        $parameters = $reflection->getMethod($action)->getParameters();

        $finalParameters = [];

        foreach ($parameters as $parameter) {
            $class = $parameter->getClass()->name;

            // Parameter request.
            if ($class === Request::class) {
                $finalParameters[] = $request;
            } // Parameter request - by inheritance.
            elseif ($parameter->getClass()->getParentClass()->name === Request::class) {
                $finalParameters[] = new Request(
                    $request->query,
                    $request,
                    $request->attributes,
                    $request->cookies,
                    $request->files,
                    $request->server,
                    $request->getContent(true)
                );
            } // Parameter model configuration.
            elseif (preg_match("/Modules\\\\.+\\\\Models\\\\Configuration/", $class)) {
                $finalParameters[] = $entity->getConfiguration();
            } // Entity.
            elseif ($class === Entity::class) {
                $finalParameters[] = $entity;
            } else {
                $finalParameters[] = app($class);
            }
        }

        return call_user_func_array([app($controller), $action], $finalParameters);
    }


    /**
     * GET: Entity detail.
     *
     * @param \App\Models\Module\Entity $entity
     * @return \Illuminate\Http\JsonResponse
     */
    public function entity(Entity $entity)
    {
        if (!$entity->exists) {
            abort(404);
        }

        return new JsonResponse([
            'id' => $entity->id,
            'configuration' => $entity->getConfiguration(),
            'name' => $entity->module,
        ]);
    }
}
