<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Ministry;
use App\Models\OfficialDocument;
use App\Models\Payment;
use App\Models\Procedure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NationalDashboardService
{
    /**
     * Ensemble des données nécessaires au tableau de bord national.
     */
    public function getDashboardData(): array
    {
        return [
            'summary' => $this->getSummary(),
            'applicationStatuses' => $this->getApplicationStatuses(),
            'paymentSummary' => $this->getPaymentSummary(),
            'monthlyApplications' => $this->getMonthlyApplications(),
            'monthlyPayments' => $this->getMonthlyPayments(),
            'ministryPerformance' => $this->getMinistryPerformance(),
            'alerts' => $this->getOperationalAlerts(),
            'recentPayments' => $this->getRecentPayments(),
            'recentDocuments' => $this->getRecentOfficialDocuments(),
            'systemHealth' => $this->getSystemHealth(),
        ];
    }

    /**
     * Indicateurs principaux.
     */
    private function getSummary(): array
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        return [
            'citizens' => User::where('role', 'citoyen')->count(),

            'agents' => User::whereIn(
                'role',
                ['agent', 'responsable']
            )->count(),

            'administrators' => User::where(
                'role',
                'admin'
            )->count(),

            'ministries' => Ministry::count(),

            'procedures' => Procedure::count(),

            'applications' => Application::count(),

            'applications_today' => Application::where(
                'created_at',
                '>=',
                $today
            )->count(),

            'applications_week' => Application::where(
                'created_at',
                '>=',
                $weekStart
            )->count(),

            'applications_month' => Application::where(
                'created_at',
                '>=',
                $monthStart
            )->count(),

            'official_documents' => Schema::hasTable(
                'official_documents'
            )
                ? OfficialDocument::count()
                : 0,

            'payments' => Schema::hasTable('payments')
                ? Payment::count()
                : 0,

            'revenue_total' => Schema::hasTable('payments')
                ? (float) Payment::where('status', 'paye')
                    ->sum('amount')
                : 0,

            'revenue_today' => Schema::hasTable('payments')
                ? (float) Payment::where('status', 'paye')
                    ->where('paid_at', '>=', $today)
                    ->sum('amount')
                : 0,

            'revenue_month' => Schema::hasTable('payments')
                ? (float) Payment::where('status', 'paye')
                    ->where('paid_at', '>=', $monthStart)
                    ->sum('amount')
                : 0,
        ];
    }

    /**
     * Nombre de demandes pour chaque statut.
     */
    private function getApplicationStatuses(): array
    {
        $statuses = [
            'soumise',
            'en_traitement',
            'validee',
            'rejetee',
            'terminee',
        ];

        $counts = Application::query()
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect($statuses)
            ->mapWithKeys(function (string $status) use ($counts) {
                return [
                    $status => (int) ($counts[$status] ?? 0),
                ];
            })
            ->all();
    }

    /**
     * Statistiques financières.
     */
    private function getPaymentSummary(): array
    {
        if (! Schema::hasTable('payments')) {
            return [
                'en_attente' => 0,
                'paye' => 0,
                'echoue' => 0,
                'rembourse' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
            ];
        }

        $counts = Payment::query()
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'en_attente' => (int) ($counts['en_attente'] ?? 0),
            'paye' => (int) ($counts['paye'] ?? 0),
            'echoue' => (int) ($counts['echoue'] ?? 0),
            'rembourse' => (int) ($counts['rembourse'] ?? 0),

            'total_amount' => (float) Payment::sum('amount'),

            'paid_amount' => (float) Payment::where(
                'status',
                'paye'
            )->sum('amount'),
        ];
    }

    /**
     * Évolution des demandes sur les douze derniers mois.
     */
    private function getMonthlyApplications(): array
    {
        $start = now()
            ->subMonths(11)
            ->startOfMonth();

        $rows = Application::query()
            ->selectRaw(
                "TO_CHAR(created_at, 'YYYY-MM') AS period"
            )
            ->selectRaw('COUNT(*) AS total')
            ->where('created_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $values = [];

        for ($index = 0; $index < 12; $index++) {
            $date = $start->copy()->addMonths($index);
            $period = $date->format('Y-m');

            $labels[] = ucfirst(
                $date->locale('fr')->translatedFormat('M Y')
            );

            $values[] = (int) ($rows[$period] ?? 0);
        }

        return compact('labels', 'values');
    }

    /**
     * Recettes confirmées sur les douze derniers mois.
     */
    private function getMonthlyPayments(): array
    {
        $start = now()
            ->subMonths(11)
            ->startOfMonth();

        if (! Schema::hasTable('payments')) {
            return [
                'labels' => [],
                'values' => [],
            ];
        }

        $rows = Payment::query()
            ->selectRaw(
                "TO_CHAR(paid_at, 'YYYY-MM') AS period"
            )
            ->selectRaw('COALESCE(SUM(amount), 0) AS total')
            ->where('status', 'paye')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $values = [];

        for ($index = 0; $index < 12; $index++) {
            $date = $start->copy()->addMonths($index);
            $period = $date->format('Y-m');

            $labels[] = ucfirst(
                $date->locale('fr')->translatedFormat('M Y')
            );

            $values[] = (float) ($rows[$period] ?? 0);
        }

        return compact('labels', 'values');
    }

    /**
     * Performance de chaque ministère.
     */
    private function getMinistryPerformance(): Collection
    {
        return Ministry::query()
            ->withCount('procedures')
            ->withCount([
                'procedures as total_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    );
                },

                'procedures as validated_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    )->whereIn(
                        'applications.status',
                        ['validee', 'terminee']
                    );
                },

                'procedures as processing_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    )->where(
                        'applications.status',
                        'en_traitement'
                    );
                },

                'procedures as rejected_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    )->where(
                        'applications.status',
                        'rejetee'
                    );
                },
            ])
            ->orderByDesc('total_applications')
            ->get()
            ->map(function (Ministry $ministry) {
                $total = (int) $ministry->total_applications;
                $validated = (int) $ministry->validated_applications;

                $ministry->validation_rate = $total > 0
                    ? round(($validated / $total) * 100, 1)
                    : 0;

                return $ministry;
            });
    }

    /**
     * Alertes nécessitant une intervention.
     */
    private function getOperationalAlerts(): array
    {
        $threeDaysAgo = now()->subDays(3);
        $sevenDaysAgo = now()->subDays(7);

        $unpaid = Application::query()
            ->where('payment_status', '!=', 'paye')
            ->whereHas('procedure', function ($query) {
                $query->where('fee', '>', 0);
            })
            ->count();

        $longProcessing = Application::query()
            ->where('status', 'en_traitement')
            ->where('updated_at', '<=', $sevenDaysAgo)
            ->count();

        $submittedTooLong = Application::query()
            ->where('status', 'soumise')
            ->where('created_at', '<=', $threeDaysAgo)
            ->count();

        $rejectedDocuments = Schema::hasTable(
            'application_documents'
        )
            ? DB::table('application_documents')
                ->where('status', 'rejete')
                ->count()
            : 0;

        $missingOfficialDocuments = Schema::hasTable(
            'official_documents'
        )
            ? Application::query()
                ->whereIn(
                    'status',
                    ['validee', 'terminee']
                )
                ->whereDoesntHave('officialDocument')
                ->count()
            : 0;

        return [
            [
                'level' => $submittedTooLong > 0
                    ? 'warning'
                    : 'success',

                'title' => 'Demandes soumises en attente',
                'count' => $submittedTooLong,
                'description' =>
                    'Demandes déposées depuis plus de trois jours.',
            ],

            [
                'level' => $longProcessing > 0
                    ? 'danger'
                    : 'success',

                'title' => 'Dossiers en retard',
                'count' => $longProcessing,
                'description' =>
                    'Dossiers en traitement depuis au moins sept jours.',
            ],

            [
                'level' => $unpaid > 0
                    ? 'warning'
                    : 'success',

                'title' => 'Paiements en attente',
                'count' => $unpaid,
                'description' =>
                    'Démarches payantes non encore réglées.',
            ],

            [
                'level' => $rejectedDocuments > 0
                    ? 'danger'
                    : 'success',

                'title' => 'Pièces rejetées',
                'count' => $rejectedDocuments,
                'description' =>
                    'Documents nécessitant une correction citoyenne.',
            ],

            [
                'level' => $missingOfficialDocuments > 0
                    ? 'warning'
                    : 'success',

                'title' => 'Documents officiels à générer',
                'count' => $missingOfficialDocuments,
                'description' =>
                    'Dossiers validés sans document officiel.',
            ],
        ];
    }

    /**
     * Derniers paiements.
     */
    private function getRecentPayments(): Collection
    {
        if (! Schema::hasTable('payments')) {
            return collect();
        }

        return Payment::with([
            'user',
            'application.procedure',
        ])
            ->latest()
            ->limit(8)
            ->get();
    }

    /**
     * Derniers documents officiels.
     */
    private function getRecentOfficialDocuments(): Collection
    {
        if (! Schema::hasTable('official_documents')) {
            return collect();
        }

        return OfficialDocument::with([
            'application.user',
            'application.procedure',
            'generator',
        ])
            ->latest('issued_at')
            ->limit(8)
            ->get();
    }

    /**
     * Santé technique simple de la plateforme.
     */
    private function getSystemHealth(): array
    {
        $databaseConnected = true;

        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $databaseConnected = false;
        }

        $storagePath = storage_path();
        $freeSpace = @disk_free_space($storagePath);
        $totalSpace = @disk_total_space($storagePath);

        $storageUsage = (
            is_numeric($freeSpace)
            && is_numeric($totalSpace)
            && $totalSpace > 0
        )
            ? round(
                (($totalSpace - $freeSpace) / $totalSpace) * 100,
                1
            )
            : null;

        return [
            'database' => [
                'status' => $databaseConnected,
                'label' => $databaseConnected
                    ? 'Opérationnelle'
                    : 'Indisponible',
            ],

            'storage' => [
                'status' => $storageUsage === null
                    || $storageUsage < 90,

                'label' => $storageUsage === null
                    ? 'Non mesuré'
                    : $storageUsage . ' % utilisé',

                'usage' => $storageUsage,
            ],

            'php' => [
                'status' => true,
                'label' => PHP_VERSION,
            ],

            'laravel' => [
                'status' => true,
                'label' => app()->version(),
            ],

            'environment' => [
                'status' => app()->environment('production'),
                'label' => app()->environment(),
            ],

            'debug' => [
                'status' => ! config('app.debug'),
                'label' => config('app.debug')
                    ? 'Activé'
                    : 'Désactivé',
            ],
        ];
    }
}