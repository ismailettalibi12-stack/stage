<?php
// Ce fichier est inclus comme fragment par dashbord.php
// Les classes patientdao, userdao, et la session sont déjà initialisées.
$users    = $users ?? [];
$userlogin = $_SESSION['user'] ?? null;
if ($userlogin instanceof __PHP_Incomplete_Class) {
    unset($_SESSION['user']);
    $userlogin = null;
}
?>

<!-- Contenu Accueil extrait du Dashboard -->
<div class="welcome-banner p-4 mb-4 shadow-sm rounded-4" style="background-color: #fef5e7;">
    <h3 class="h4 text-warning-emphasis fw-bold mb-1">Bonjour, <?= htmlspecialchars($userlogin ? $userlogin->getName() : 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?> 👋</h3>
    <p class="mb-0 text-muted">Bienvenue dans votre espace de gestion applicatif.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm text-white" style="background-color: #b02a2a; border-radius:16px;">
            <h6>Total Patients</h6>
            <h2><?= $totalPatients ?></h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm text-white" style="background-color: #b02a2a; border-radius:16px;">
            <h6>Articles en Stock</h6>
            <h2><?= $totalStock ?></h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm text-white" style="background-color: #b02a2a; border-radius:16px;">
            <h6>Prescriptions</h6>
            <h2><?= $totalPrescriptions ?></h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm text-white" style="background-color: #b02a2a; border-radius:16px;">
            <h6>Utilisateurs</h6>
            <h2><?= $totalUsers ?></h2>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #e0e7ff; border-radius:16px;">
            <h6 class="text-primary">Étudiants</h6>
            <h2 class="text-primary"><?= $totalStudents ?></h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #fef0e6; border-radius:16px;">
            <h6 class="text-warning">Stages en cours</h6>
            <h2 class="text-warning"><?= $totalActiveInternships ?></h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #fce8e6; border-radius:16px;">
            <h6 class="text-danger">Alertes Stock</h6>
            <h2 class="text-danger"><?= $totalLowStock ?></h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #d1fae5; border-radius:16px;">
            <h6 class="text-success">PEC en attente</h6>
            <h2 class="text-success"><?= $totalPendingPec ?></h2>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #f3e8ff; border-radius:16px;">
            <h6 class="text-purple">Valeur Stock</h6>
            <h2 class="text-purple"><?= number_format($totalStockValue, 2) ?> DH</h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #e0f2fe; border-radius:16px;">
            <h6 class="text-info">Taux Réussite</h6>
            <h2 class="text-info"><?= $validationSuccessRate ?>%</h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #fef3c7; border-radius:16px;">
            <h6 class="text-amber">Soutenances</h6>
            <h2 class="text-amber"><?= $totalValidations ?></h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 shadow-sm" style="background-color: #fce7f3; border-radius:16px;">
            <h6 class="text-pink">Cette Semaine</h6>
            <h2 class="text-pink"><?= count($upcomingDefenses) ?></h2>
        </div>
    </div>
</div>

<!-- Graphiques avec conteneur à hauteur fixe pour éviter le gigantisme -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4 text-dark">Répartition des Soutenances</h5>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="validationChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4 text-dark">Stock par Catégorie</h5>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-dark">Utilisateurs système</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-secondary small">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Créé le</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)) : ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Aucun utilisateur trouvé.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($users as $systemUser) : ?>
                        <tr>
                            <td><?= htmlspecialchars($systemUser->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($systemUser->getName(), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($systemUser->getEmail(), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($systemUser->getRole(), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($systemUser->getCreatedAt(), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($lowStockItems)) : ?>
<div class="card border-0 shadow-sm rounded-4 p-4 mt-4" style="background-color: #fff5f5;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Alertes Stock Bas
        </h5>
        <a href="?page=stock" class="btn btn-sm btn-outline-danger">Voir tout</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-secondary small">
                <tr>
                    <th>Article</th>
                    <th>Catégorie</th>
                    <th>Quantité</th>
                    <th>Min. Requis</th>
                    <th>Unité</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($lowStockItems, 0, 5) as $item) : ?>
                    <tr>
                        <td><?= htmlspecialchars($item->getItemName(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getCategory(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="fw-bold text-danger"><?= $item->getQuantity(); ?></td>
                        <td><?= $item->getMinQuantity(); ?></td>
                        <td><?= htmlspecialchars($item->getUnit(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <span class="badge bg-danger">Stock Critique</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($upcomingDefenses)) : ?>
<div class="card border-0 shadow-sm rounded-4 p-4 mt-4" style="background-color: #f0f9ff;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-info">
            <i class="bi bi-calendar-event me-2"></i>Soutenances cette semaine
        </h5>
        <a href="?page=validations" class="btn btn-sm btn-outline-info">Voir tout</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-secondary small">
                <tr>
                    <th>Étudiant</th>
                    <th>Entreprise</th>
                    <th>Date de soutenance</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingDefenses as $defense) : ?>
                    <tr>
                        <td class="fw-medium"><?= htmlspecialchars($defense['student_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($defense['company_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($defense['defense_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($defense['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
// Chart.js Configuration
document.addEventListener('DOMContentLoaded', function() {
    // Validation Status Pie Chart
    const validationCtx = document.getElementById('validationChart');
    if (validationCtx) {
        const validatedCount = <?= isset($validationDao) && method_exists($validationDao, 'countValidationsByStatus') ? $validationDao->countValidationsByStatus('Validé') : 0 ?>;
        const nonValidatedCount = <?= isset($validationDao) && method_exists($validationDao, 'countValidationsByStatus') ? $validationDao->countValidationsByStatus('Non Validé') : 0 ?>;
        const inProgressCount = <?= isset($validationDao) && method_exists($validationDao, 'countValidationsByStatus') ? $validationDao->countValidationsByStatus('En cours') : 0 ?>;

        if (validatedCount > 0 || nonValidatedCount > 0 || inProgressCount > 0) {
            new Chart(validationCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Validé', 'Non Validé', 'En cours'],
                    datasets: [{
                        data: [validatedCount, nonValidatedCount, inProgressCount],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            validationCtx.parentElement.innerHTML = '<p class="text-muted text-center py-4">Aucune donnée de validation disponible</p>';
        }
    }

    // Stock by Category Bar Chart
    const stockCtx = document.getElementById('stockChart');
    if (stockCtx) {
        const stockData = <?= isset($stockByCategory) && is_array($stockByCategory) ? json_encode($stockByCategory) : '[]' ?>;

        if (stockData.length > 0) {
            const categories = stockData.map(item => item.category);
            const quantities = stockData.map(item => parseInt(item.total_quantity) || 0);

            new Chart(stockCtx, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Quantité',
                        data: quantities,
                        backgroundColor: '#b02a2a',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            stockCtx.parentElement.innerHTML = '<p class="text-muted text-center py-4">Aucune donnée de stock disponible</p>';
        }
    }
});
</script>

<!-- Calendrier des Soutenances -->
<div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-calendar-week me-2"></i>Planning des Soutenances  
        </h5>
    </div>
    <div id="calendar" style="min-height: 600px;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            <?php
            $calendarEvents = [];
            if (!empty($upcomingDefenses)) {
                foreach ($upcomingDefenses as $def) {
                    $calendarEvents[] = [
                        'title' => 'Soutenance : ' . ($def['student_name'] ?? 'Étudiant'),
                        'start' => $def['defense_date'],
                        'backgroundColor' => '#10b981',
                        'borderColor' => '#10b981'
                    ];
                }
            }
            ?>
            var phpEvents = <?= json_encode($calendarEvents); ?>;
            successCallback(phpEvents);
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            omitZeroMinute: false
        }
    });

    calendar.render();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('menuSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase().trim();
            // Sélectionne tous les éléments de liste de la sidebar (les liens du menu)
            const navItems = document.querySelectorAll('.sidebar .nav-item');

            navItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = ''; // Affiche l'élément s'il correspond
                } else {
                    item.style.display = 'none'; // Le masque s'il ne correspond pas
                }
            });
        });
    }
});
</script>