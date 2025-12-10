@php
$config = [
    'tableId' => 'table-activites',
    'ajaxUrl' => route('activites-log.data'),
    'lengthChange' => true,
    'pageLength' => 15,
    'buttons' => [],
    'serverSide' => true,
    'ajax' => [
        'data' => 'function(d) {
            d.gestionnaire_id = document.getElementById("filter-gestionnaire").value;
            d.action = document.getElementById("filter-action").value;
            d.date_debut = document.getElementById("filter-date-debut").value;
            d.date_fin = document.getElementById("filter-date-fin").value;
        }'
    ]
];

$columns = [
    [
        'title' => 'Gestionnaire',
        'data' => 'gestionnaire',
        'render' => 'function(data, type, row) {
            return `<div class="d-flex align-items-center">
                <img src="${row.gestionnaire.photo}" class="avatar me-3" alt="">
                <span class="fw-semibold">${row.gestionnaire.nom}</span>
            </div>`;
        }'
    ],
    [
        'title' => 'Action',
        'data' => 'action_label',
        'render' => 'function(data, type, row) {
            return `<span class="badge bg-${row.action_color}">
                <i class="fas ${row.action_icon} me-1"></i>${data}
            </span>`;
        }'
    ],
    [
        'title' => 'Type',
        'data' => 'model_type',
        'render' => 'function(data) {
            return `<span class="badge bg-light text-dark">${data}</span>`;
        }'
    ],
    [
        'title' => 'Description',
        'data' => 'description',
        'render' => 'function(data) {
            return `<span class="text-truncate d-inline-block" style="max-width: 300px;" title="${data}">${data}</span>`;
        }'
    ],
    [
        'title' => 'Adresse IP',
        'data' => 'ip_address',
        'render' => 'function(data) {
            return `<code class="bg-light px-2 py-1 rounded">${data || "N/A"}</code>`;
        }'
    ],
    [
        'title' => 'Date',
        'data' => 'created_at',
        'render' => 'function(data, type, row) {
            return `<div>
                <span class="d-block fw-semibold">${data}</span>
                <small class="text-muted">${row.created_at_human}</small>
            </div>`;
        }'
    ],
];

@endphp

<x-generic.xtable :config="$config" :columns="$columns" />
