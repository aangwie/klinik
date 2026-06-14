import './bootstrap';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css?url';

// DataTables Indonesian Language
const indonesianLang = {
    "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
    "sProcessing": "Sedang memproses...",
    "sLengthMenu": "Tampilkan _MENU_ data",
    "sZeroRecords": "Tidak ditemukan data yang sesuai",
    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
    "sInfoFiltered": "(disaring dari _MAX_ data keseluruhan)",
    "sSearch": "Cari:",
    "oPaginate": {
        "sFirst": "<<",
        "sPrevious": "<",
        "sNext": ">",
        "sLast": ">>"
    }
};

// Custom styling to align search right and pagination right
const style = document.createElement('style');
style.textContent = `
    div.dt-container .dt-layout-row.dt-layout-table {
        overflow-x: auto;
    }
    div.dt-container .dt-layout-cell {
        padding: 0.5rem 0;
    }
    div.dt-container .dt-search {
        text-align: right;
        float: right;
    }
    div.dt-container .dt-search label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }
    div.dt-container .dt-search input {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        outline: none;
        font-size: 0.875rem;
        width: 200px;
    }
    div.dt-container .dt-search input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    div.dt-container .dt-length {
        float: left;
        text-align: left;
    }
    div.dt-container .dt-length label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    div.dt-container .dt-length select {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        outline: none;
        font-size: 0.875rem;
        background: white;
    }
    div.dt-container .dt-length select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    div.dt-container .dt-paging {
        text-align: right;
        float: right;
    }
    div.dt-container .dt-paging nav {
        display: inline-flex;
        gap: 0.25rem;
    }
    div.dt-container .dt-paging button {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background: white;
        font-size: 0.875rem;
        cursor: pointer;
        color: #374151;
        transition: all 0.15s;
    }
    div.dt-container .dt-paging button:hover:not(.disabled) {
        background: #f3f4f6;
    }
    div.dt-container .dt-paging button.current {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
    div.dt-container .dt-paging button.disabled {
        opacity: 0.4;
        cursor: default;
    }
    div.dt-container .dt-info {
        float: left;
        font-size: 0.875rem;
        color: #6b7280;
        padding-top: 0.5rem;
    }
    div.dt-container {
        padding: 0 !important;
    }
    div.dt-container .dt-layout-row {
        margin: 0 !important;
    }
    div.dt-container .dt-layout-row:first-child {
        padding-bottom: 0.75rem;
    }
    div.dt-container .dt-layout-row:last-child {
        padding-top: 0.75rem;
    }
    div.dt-container table.dataTable {
        margin: 0 !important;
        width: 100% !important;
    }

    /* Tom Select Theme - Light Green/Emerald */
    .ts-wrapper-green .ts-control {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem !important;
        min-height: 42px !important;
        box-shadow: none !important;
    }
    .ts-wrapper-green .ts-control:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
    }
    .ts-wrapper-green .ts-control .item {
        background: #d1fae5 !important;
        color: #065f46 !important;
        border: 1px solid #a7f3d0 !important;
        border-radius: 0.375rem !important;
        padding: 0.125rem 0.5rem !important;
        font-size: 0.8rem !important;
    }
    .ts-wrapper-green .ts-control .item .remove {
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
    }
    .ts-wrapper-green .ts-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1) !important;
        font-size: 0.875rem !important;
    }
    .ts-wrapper-green .ts-dropdown .option.active {
        background: #d1fae5 !important;
        color: #065f46 !important;
    }
    .ts-wrapper-green .ts-dropdown .option:hover {
        background: #f0fdf4 !important;
    }
`;
document.head.appendChild(style);

// Suppress DataTables warning alerts (prevent popup errors)
const originalAlert = window.alert;
window.alert = function(msg) {
    if (typeof msg === 'string' && msg.indexOf('DataTables warning') !== -1) {
        return; // Silently ignore DataTables warnings
    }
    originalAlert(msg);
};

// Initialize Tom Select for enhanced multi-select
document.addEventListener('DOMContentLoaded', function() {
    // Multi-select with search
    document.querySelectorAll('select[multiple]').forEach(select => {
        new TomSelect(select, {
            plugins: ['remove_button'],
            maxItems: null,
        });
    });

    // Auto-initialize all tables with class 'datatable'
    const tables = document.querySelectorAll('table.datatable');
    tables.forEach(table => {
        new DataTable(table, {
            language: indonesianLang,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            order: [],
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
            // dom: 'lftip' -> l=length, f=filter(search), t=table, i=info, p=paginate
            // Custom: length left, search right, table, info left, pagination right
            dom: '<"dt-top-row"lf>t<"dt-bottom-row"ip>'
        });
    });
});