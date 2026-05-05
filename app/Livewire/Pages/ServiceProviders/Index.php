<?php

namespace App\Livewire\Pages\ServiceProviders;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public string $f_province = '';
    public string $f_service = '';
    public int $perPage = 10;

    public array $provinces = []; // [{code,name}]
    public array $serviceOptions = [
        "Prise en charge médicale",
        "Soutien psychologique et psychosocial",
        "Prise en charge juridique et judiciaire",
        "Soutien socio-économique",
        "Gestion de cas",
        "Autre",
    ];

    public array $provinceMap = [];

    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    public array $form = [
        'provider_name' => '',
        'provider_location' => [], // array codes provinces
        'focalpoint_name' => '',
        'focalpoint_email' => '',
        'focalpoint_number' => '',
        'type_services_proposes' => [], // array services
    ];

    protected $queryString = [
        'q' => ['except' => ''],
        'f_province' => ['except' => ''],
        'f_service' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount(): void
    {
        $this->authorizeManage();

        /*  $this->provinces = DB::table('provinces')
            ->select('code_province', 'nom_province')
            ->orderBy('nom_province')
            ->get()
            ->map(fn($p) => ['code' => $p->code_province, 'name' => $p->nom_province])
            ->toArray();*/

        $provinces = \App\Models\Province::select('code_province', 'nom_province')
            ->orderBy('nom_province')
            ->get();

        $this->provinces = $provinces
            ->map(fn($p) => [
                'code' => $p->code_province,
                'name' => $p->nom_province
            ])
            ->toArray();

        //  mapping code => nom
        $this->provinceMap = $provinces
            ->pluck('nom_province', 'code_province')
            ->toArray();
    }

    private function authorizeManage(): void
    {
        if (!in_array(Auth::user()->user_role, ['superadmin', 'admin'], true)) {
            abort(403);
        }
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }
    public function updatingFProvince(): void
    {
        $this->resetPage();
    }
    public function updatingFService(): void
    {
        $this->resetPage();
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorizeManage();

        $this->resetValidation();
        $this->editing = false;
        $this->editingId = null;

        $this->form = [
            'provider_name' => '',
            'provider_location' => [],
            'focalpoint_name' => '',
            'focalpoint_email' => '',
            'focalpoint_number' => '',
            'type_services_proposes' => [],
        ];

        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->authorizeManage();

        $this->resetValidation();
        $sp = ServiceProvider::findOrFail($id);

        $this->editing = true;
        $this->editingId = $id;

        $this->form = [
            'provider_name' => $sp->provider_name ?? '',
            'provider_location' => $sp->provider_location ?? [],
            'focalpoint_name' => $sp->focalpoint_name ?? '',
            'focalpoint_email' => $sp->focalpoint_email ?? '',
            'focalpoint_number' => $sp->focalpoint_number ?? '',
            'type_services_proposes' => $sp->type_services_proposes ?? [],
        ];

        $this->showModal = true;
    }

    protected function rules(): array
    {
        $id = $this->editingId;

        return [
            'form.provider_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_providers', 'provider_name')->ignore($id),
            ],
            'form.provider_location' => ['required', 'array', 'min:1'],
            'form.provider_location.*' => ['string', Rule::exists('provinces', 'code_province')->where('is_active', 'YES')],

            'form.focalpoint_name' => ['nullable', 'string', 'max:255'],
            'form.focalpoint_email' => ['nullable', 'email', 'max:255'],
            'form.focalpoint_number' => ['nullable', 'string', 'max:50'],

            'form.type_services_proposes' => ['required', 'array', 'min:1'],
            'form.type_services_proposes.*' => ['string', Rule::in($this->serviceOptions)],
        ];
    }

    public function save(): void
    {
        $this->authorizeManage();
        $this->validate();

        if ($this->editing && $this->editingId) {
            $sp = ServiceProvider::findOrFail($this->editingId);
            $sp->fill($this->form);
            $sp->save();

            $this->dispatch('toast', message: "Structure mise à jour.", type: 'success', duration: 5000);
        } else {
            $sp = new ServiceProvider();
            $sp->fill($this->form);
            $sp->created_by = Auth::id();
            $sp->save();

            $this->dispatch('toast', message: "Structure créée.", type: 'success', duration: 5000);
        }

        $this->showModal = false;
    }

    public function render()
    {
        $this->authorizeManage();

        $query = ServiceProvider::query();

        if ($this->q !== '') {
            $s = '%' . trim($this->q) . '%';
            $query->where(function ($qq) use ($s) {
                $qq->where('provider_name', 'ilike', $s)
                    ->orWhere('focalpoint_email', 'ilike', $s)
                    ->orWhere('focalpoint_name', 'ilike', $s);
            });
        }

        // Filtre province (jsonb)
        if ($this->f_province !== '') {
            $query->whereJsonContains('provider_location', $this->f_province);
        }

        // Filtre service (jsonb)
        if ($this->f_service !== '') {
            $query->whereJsonContains('type_services_proposes', $this->f_service);
        }

        $providers = $query
            ->orderBy('provider_name')
            ->paginate($this->perPage);

        return view('livewire.pages.service-providers.index', [
            'providers' => $providers,
        ]);
    }
}
